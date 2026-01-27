<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Http\Resources\UserResource;

class AdminAuthController extends Controller
{
    public function login(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Chercher l'utilisateur
        $user = User::with('role')->where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Identifiants incorrects'
            ], 401);
        }

        // Vérifier le mot de passe
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Identifiants incorrects'
            ], 401);
        }

        // Vérifier le rôle
        if (!in_array($user->role->role, ['admin', 'superadmin'])) {
            return response()->json([
                'message' => 'Accès refusé. Vous n\'êtes pas administrateur.'
            ], 403);
        }

        // Créer le token
        $token = $user->createToken('admin-token')->plainTextToken;

        return response()->json([
            'message' => 'Connexion admin réussie',
            'token' => $token,
            'user' => new UserResource($user),
        ]);
    }
}
