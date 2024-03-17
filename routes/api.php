<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\api\AdminController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\api\StudentController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\Auth\users\AuthController;
use App\Http\Controllers\api\OrganizationController;
use App\Http\Controllers\Auth\admins\AdminAuthController;
use App\Http\Controllers\Auth\students\StudentAuthController;
use App\Http\Controllers\Auth\orgs\OrganizationAuthController;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::apiResource('roles', RoleController::class);
Route::apiResource('permissions', PermissionController::class);
Route::post('get/permissions/{roleName}', [RoleController::class, 'getPermissionsByRoleName']);
Route::post('roles/{role}/permissions/{permission}', [RolePermissionController::class, 'attachPermission']);
Route::delete('roles/{role}/permissions/{permission}', [RolePermissionController::class, 'detachPermission']);


Route::get('/userAgent', function (Request $request) {
    return $userAgent = request()->header('User-Agent');
});


Route::get('/organizations', [OrganizationController::class, 'listOrganizations']);
Route::get('/organizations/lists', [OrganizationController::class, 'listOrganizationsWithPaginate']);
Route::get('organizations/single/{id}', [OrganizationController::class, 'show']);



//// user auth
Route::post('/user/login', [AuthController::class, 'login'])->name('login');
Route::post('/user/check/login', [AuthController::class, 'checkTokenExpiration'])->name('checklogin');
Route::post('/user/check-token', [AuthController::class, 'checkToken']);
Route::post('/user/register', [AuthController::class, 'register']);

Route::group(['middleware' => ['auth:api']], function () {
    Route::post('/user/logout', [AuthController::class, 'logout']);

    // Routes for user registration, update, delete, and show
    Route::prefix('users')->group(function () {
        Route::put('{id}', [UserController::class, 'update']);       // Update user by ID
        Route::delete('{id}', [UserController::class, 'delete']);    // Delete user by ID
        Route::get('{id}', [UserController::class, 'show']);          // Show user details by ID
    });
    Route::post('users/change-password', [UserController::class, 'changePassword']);
    Route::get('/user-access', function (Request $request) {
        return 'user access';
    });
});


//// organization auth
Route::post('/organization/login', [OrganizationAuthController::class, 'login']);
Route::post('/organization/check/login', [OrganizationAuthController::class, 'checkTokenExpiration']);
Route::post('/organization/check-token', [OrganizationAuthController::class, 'checkToken']);
Route::post('organization/register', [OrganizationAuthController::class, 'register']); // Organization registration

Route::group(['middleware' => ['auth:organization']], function () {
    Route::post('/organization/logout', [OrganizationAuthController::class, 'logout']);

    // Routes for organization registration, update, delete, and show
    Route::prefix('organizations')->group(function () {
        Route::put('{id}', [OrganizationController::class, 'update']);       // Update organization by ID
        Route::delete('{id}', [OrganizationController::class, 'delete']);    // Delete organization by ID
        Route::get('{id}', [OrganizationController::class, 'show']);          // Show organization details by ID
    });
    Route::post('organization/doners', [OrganizationController::class, 'getDonersByOrganization']);

    Route::post('organization/change-password', [OrganizationController::class, 'changePassword']);


    Route::get('/organization-access', function (Request $request) {
        return 'organization access';
    });
});



//// admin auth
Route::post('/admin/login', [AdminAuthController::class, 'login']);
Route::post('/admin/check/login', [AdminAuthController::class, 'checkTokenExpiration']);
Route::post('/admin/check-token', [AdminAuthController::class, 'checkToken']);
Route::post('admin/register', [AdminAuthController::class, 'register']);

Route::group(['middleware' => ['auth:admin']], function () {
    Route::post('admin/logout', [AdminAuthController::class, 'logout']);
    Route::get('/admin-access', function (Request $request) {
        return 'admin access';
    });
});


Route::post('/student/login', [StudentAuthController::class, 'login']);
Route::post('/student/check/login', [StudentAuthController::class, 'checkTokenExpiration']);
Route::post('/student/check-token', [StudentAuthController::class, 'checkToken']);
Route::post('/student/register', [StudentAuthController::class, 'register']); // Organization registration

Route::middleware('auth:student')->group(function () {
    Route::post('/student/logout', [StudentAuthController::class, 'logout']);

    Route::get('/students/profile/{id}', [StudentController::class, 'show']);

    Route::get('/student-access', function (Request $request) {
        return 'student access';
    });
});
