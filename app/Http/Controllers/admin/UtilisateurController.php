<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UtilisateurController extends Controller
{
    /**
     * Liste des admins
     */
    public function index()
    {

        $adminRole = Role::where('role', 'admin')->firstOrFail();

        $admins = User::whereIn('role_id', [$adminRole->id])
            ->with('role')
            ->latest()
            ->get();

        return UserResource::collection($admins);
    }

    /**
     * Récupérer tous les utilisateurs
     */
    public function allUsers()
    {
        $users = User::with('role', 'commandes')->latest()->get();
        return UserResource::collection($users);
    }


    /**
     * Créer un admin (rôle forcé à admin)
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'telephone' => 'nullable|string|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Récupère le rôle admin
        $adminRole = Role::where('role', 'admin')->firstOrFail();

        $user = User::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'password' => Hash::make($request->password),
            'role_id' => $adminRole->id,
        ]);

        return response()->json([
            'message' => 'Administrateur créé avec succès',
            'data' => new UserResource($user->load('role'))
        ], 201);
    }

    /**
     * Détails d'un admin
     */
    public function show(User $user)
    {

        return new UserResource($user->load('role'));
    }

    /**
     * Modifier un admin
     */
    public function update(Request $request, User $user)
    {

        $validator = Validator::make($request->all(), [
            'nom' => 'sometimes|string|max:255',
            'prenom' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'telephone' => 'sometimes|string|unique:users,telephone,' . $user->id,
            'password' => 'nullable|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return response()->json([
            'message' => 'Administrateur modifié avec succès',
            'data' => new UserResource($user->load('role'))
        ]);
    }

    /**
     * Supprimer un admin
     */
    public function destroy(User $user)
    {

        // Empêcher de supprimer un superadmin
        if ($user->role->role === 'superadmin') {
            return response()->json([
                'message' => 'Impossible de supprimer un super administrateur'
            ], 403);
        }

        $user->delete();

        return response()->json([
            'message' => 'Administrateur supprimé avec succès'
        ]);
    }

    /**
     * Transformer un user en admin
     */
    public function makeAdmin(User $user)
    {

        $adminRole = Role::where('role', 'admin')->firstOrFail();

        $user->update([
            'role_id' => $adminRole->id
        ]);

        return response()->json([
            'message' => 'Utilisateur promu administrateur',
            'data' => new UserResource($user->load('role'))
        ]);
    }

    /**
     * Rétrograder admin en user
     */
    public function makeUser(User $user)
    {

        if ($user->role->role === 'superadmin') {
            return response()->json([
                'message' => 'Impossible de rétrograder un superadmin'
            ], 403);
        }

        $userRole = Role::where('role', 'user')->firstOrFail();

        $user->update([
            'role_id' => $userRole->id
        ]);

        return response()->json([
            'message' => 'Administrateur rétrogradé en utilisateur',
            'data' => new UserResource($user->load('role'))
        ]);
    }
}
