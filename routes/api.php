<?php

use App\Http\Controllers\admin\UtilisateurController;
use App\Http\Controllers\auth\AdminAuthController;
use App\Http\Controllers\auth\UserAuthController;
use App\Http\Controllers\categories\CategorieController;
use App\Http\Controllers\commandes\CommandeController;
use App\Http\Controllers\gateways\GatewayController;
use App\Http\Controllers\livres\LivreController;
use App\Http\Controllers\Paiements\PaiementController;
use App\Http\Controllers\profil\ProfilController;
use App\Http\Controllers\stocks\StockController;
use App\Http\Middleware\IsAdminOrSuperAdmin;
use App\Http\Middleware\IsSuperAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|--------------------------------------------------------------------------|
*/

// Routes ne nécessitant pas d'authentification
Route::prefix('admin')->group(function () {
    Route::post('/login', [AdminAuthController::class, 'login']);
});

Route::prefix('user')->group(function () {
    Route::post('/register', [UserAuthController::class, 'register']);
    Route::post('/login', [UserAuthController::class, 'login']);
    Route::post('/login-sso', [UserAuthController::class, 'login_sso']);
    Route::post('forgot-password', [UserAuthController::class, 'forgotPassword']);
    Route::post('reset-password', [UserAuthController::class, 'resetPassword']);
});

// Categories
Route::get('/categories', [CategorieController::class, 'index']);
Route::get('/categories/{categorie}', [CategorieController::class, 'show']);

// Livres
Route::get('/livres', [LivreController::class, 'index']);
Route::get('/livres/{livre}', [LivreController::class, 'show']);

// Stocks
Route::get('/stocks', [StockController::class, 'index']);
Route::get('/stocks/{livre}', [StockController::class, 'show']);

Route::get('list-orders', function () {
    $service = new \App\Services\CashPayService();
    return $service->getlistOrders();
});

//callback Semoa
Route::post('/paiements/callback', [PaiementController::class, 'callback'])
    ->name('semoa.callback');

//Gateways
Route::get('/gateways', [GatewayController::class, 'index']);


// Routes nécessitant une authentification
Route::middleware('auth:sanctum')->group(function () {

    // Categories
    Route::prefix('/categories')->group(function () {
        Route::post('/', [CategorieController::class, 'store']);
        Route::put('/{categorie}', [CategorieController::class, 'update']);
        Route::delete('/{categorie}', [CategorieController::class, 'destroy']);
    })->middleware(IsAdminOrSuperAdmin::class);

    // Livres
    Route::prefix('/livres')->group(function () {
        Route::post('/', [LivreController::class, 'store']);
        Route::put('/{livre}', [LivreController::class, 'update']);
        Route::delete('/{livre}', [LivreController::class, 'destroy']);
    })->middleware(IsAdminOrSuperAdmin::class);

    // Gestion du stock
    Route::prefix('/stocks')->group(function () {
        Route::get('/mouvements/all', [StockController::class, 'allMouvements']);
        Route::post('/mouvement', [StockController::class, 'store']);
        Route::get('/{livre}/mouvements', [StockController::class, 'mouvements']);
    })->middleware(IsAdminOrSuperAdmin::class);

    // Profil
    Route::prefix('/profil')->group(function () {
        Route::get('/', [ProfilController::class, 'show']);
        Route::put('/', [ProfilController::class, 'update']);
        Route::put('/password', [ProfilController::class, 'updatePassword']);
        Route::delete('/', [ProfilController::class, 'destroy']);
    });

    // Gestion des utilisateurs et administrateurs
    Route::prefix('/admins')->group(function () {

        Route::get('/all-users', [UtilisateurController::class, 'allUsers'])->middleware(IsAdminOrSuperAdmin::class); // Admins et superadmins

        Route::middleware(IsSuperAdmin::class)->group(function () {
            Route::get('/', [UtilisateurController::class, 'index']);
            Route::post('/', [UtilisateurController::class, 'store']);
            Route::get('/{user}', [UtilisateurController::class, 'show']);
            Route::put('/{user}', [UtilisateurController::class, 'update']);
            Route::delete('/{user}', [UtilisateurController::class, 'destroy']);
            Route::put('/{user}/make-admin', [UtilisateurController::class, 'makeAdmin']);
            Route::put('/{user}/make-user', [UtilisateurController::class, 'makeUser']);
            Route::put('/{user}/lock', [UtilisateurController::class, 'lock']);
            Route::put('/{user}/unlock', [UtilisateurController::class, 'unlock']);
        });

        Route::get('/users/{user}', [UtilisateurController::class, 'show'])->middleware(IsAdminOrSuperAdmin::class);

    });

    //Commandes
    Route::prefix('/commandes')->group(function () {
        Route::post('/', [CommandeController::class, 'store']);
        Route::get('/', [CommandeController::class, 'index']);
        Route::get('/all', [CommandeController::class, 'allOrders'])->middleware(IsAdminOrSuperAdmin::class);
        Route::get('/{commande}', [CommandeController::class, 'show']);
        Route::put('/{commande}/traiter', [CommandeController::class, 'traiterCommande']);
    });

    //Paiements
    Route::prefix('/paiements')->group(function () {
        Route::get('/', [PaiementController::class, 'index'])->middleware(IsAdminOrSuperAdmin::class);
        Route::get('/user-paiements', [PaiementController::class, 'userPayments']);
        Route::get('/{id}', [PaiementController::class, 'show']);
    });

    //Notifications
    Route::prefix('/notifications')->group(function () {
        Route::get('/', function (Request $request) {
            return $request->user()->notifications;
        });

        Route::post('/read-all', function (Request $r) {
            $r->user()->unreadNotifications->markAsRead();
            return response()->noContent();
        });

        Route::post('/{id}/read', function ($id, Request $request) {
            $request->user()->notifications()->findOrFail($id)->markAsRead();
        });

        Route::delete('/{id}', function ($id, Request $request) {
            $notification = $request->user()
                ->notifications()
                ->findOrFail($id);

            $notification->delete();

            return response()->noContent();
        });
    })->middleware(IsAdminOrSuperAdmin::class);

});
