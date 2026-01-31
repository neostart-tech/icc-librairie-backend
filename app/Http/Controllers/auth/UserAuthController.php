<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;
use App\Models\User;
use App\Models\Role;
use App\Http\Resources\UserResource;

class UserAuthController extends Controller
{
    /**
     * Inscription d'un utilisateur
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'telephone' => 'nullable|string|max:30|unique:users',
            'password' => 'required|string|min:8|',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Récupérer le rôle user
        $role = Role::where('role', 'user')->first();

        $user = User::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'password' => $request->password,
            'role_id' => $role->id,
        ]);

        // Envoyer email de verification
        // event(new Registered($user));

        return response()->json([
            'message' => 'Utilisateur créé avec succès.',
            'user' => new UserResource($user),
        ]);
    }

    /**
     * Login
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->input('email'))
            ->with(['role', 'commandes'])
            ->first();

        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            return response()->json(['message' => 'Email ou mot de passe incorrect'], 401);
        }

        // if (!$user->hasVerifiedEmail()) {
        //     return response()->json(['message' => 'Email non vérifié. Veuillez vérifier votre boîte mail.'], 403);
        // }

        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'message' => 'Connexion réussie',
            'token' => $token,
            'user' => new UserResource($user->load(['role', 'commandes'])),
        ]);
    }

    /**
     * Vérifier l'email
     */
    // public function verifyEmailByHash(Request $request, $id, $hash)
    // {
    //     $user = User::findOrFail($id);

    //     if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
    //         return response()->json(['message' => 'Lien invalide'], 403);
    //     }

    //     if ($user->hasVerifiedEmail()) {
    //         return response()->json(['message' => 'Email déjà vérifié']);
    //     }

    //     $user->markEmailAsVerified();

    //     return response()->json(['message' => 'Email vérifié avec succès']);
    // }


    /**
     * Mot de passe oublié
     */
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json(['message' => 'Lien de réinitialisation envoyé sur votre email']);
        }

        return response()->json(['message' => 'Impossible d’envoyer le lien'], 500);
    }

    /**
     * Réinitialiser le mot de passe via le token envoyé par email
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|exists:users,email',
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = $password;
                $user->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json(['message' => 'Mot de passe réinitialisé avec succès']);
        }

        return response()->json(['message' => 'Le lien ou le token est invalide'], 400);
    }

    /**
     * Login via compte icc-covoiturage (SSO)
     */
    public function login_sso(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $response = Http::timeout(5)
                ->acceptJson()
                ->post(config('services.icc.url'), [
                    'email' => $request->email,
                    'password' => $request->password,
                ]);

        } catch (\Throwable $e) {
            // API distante inaccessible (DNS, SSL, timeout, etc.)
            \Log::error('SSO ICC unreachable', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Service SSO indisponible',
                'error' => $e->getMessage(),
            ], 503);
        }

        // L’API répond mais erreur HTTP
        if ($response->failed()) {
            \Log::warning('SSO ICC failed response', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return response()->json([
                'message' => 'Erreur SSO',
                'sso_status' => $response->status(),
                'sso_response' => $response->json(),
            ], $response->status());
        }

        // Structure attendue : { success: true|false }
        if (!$response->json('success')) {
            return response()->json([
                'message' => $response->json('message') ?? 'Authentification SSO échouée',
                'sso_response' => $response->json(),
            ], 401);
        }

        // Données utilisateur venant du dépôt
        $userData = $response->json('user');

        if (!$userData || !isset($userData['email'])) {
            return response()->json([
                'message' => 'Réponse SSO invalide',
                'sso_response' => $response->json(),
            ], 500);
        }

        // Vérifier ou créer l'utilisateur local
        $user = User::where('email', $userData['email'])->first();

        if (!$user) {
            $role = Role::where('role', 'user')->first();

            $user = User::create([
                'nom' => $userData['nom'] ?? '',
                'prenom' => $userData['prenom'] ?? '',
                'email' => $userData['email'],
                'telephone' => $userData['telephone'] ?? null,
                'password' => Hash::make($request->password),
                'role_id' => $role->id,
            ]);
        }

        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'message' => 'Connexion SSO réussie',
            'token' => $token,
            'user' => new UserResource($user->load(['role', 'commandes'])),
        ]);
    }

}
