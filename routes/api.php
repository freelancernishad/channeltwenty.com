<?php

use App\Models\Article;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\VisitorController;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LiveVideoController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\SocialLinkController;
use App\Http\Controllers\api\StudentController;
use App\Http\Controllers\AdvertisementController;
use App\Http\Controllers\VideoCategoryController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\Auth\users\AuthController;
use App\Http\Controllers\SelectedArticleController;
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
Route::get('/update/slug', [ArticleController::class, 'updateSlugs']);



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








Route::post('/articles', [ArticleController::class, 'store']);
Route::get('/articles/{id}', [ArticleController::class, 'show']);
Route::post('/articles/{id}', [ArticleController::class, 'update']);
Route::delete('/articles/{id}', [ArticleController::class, 'destroy']);

// Comment routes
Route::get('/comments', [CommentController::class, 'index']);
Route::get('/comments/{id}', [CommentController::class, 'show']);
Route::delete('/comments/{id}', [CommentController::class, 'destroy']);


Route::post('/categories', [CategoryController::class, 'store']);
Route::post('/categories/{id}', [CategoryController::class, 'update']);
Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);


Route::post('video-categories', [VideoCategoryController::class, 'store']);
Route::get('video-categories/{videoCategory}', [VideoCategoryController::class, 'show']);
Route::post('video-categories/{videoCategory}', [VideoCategoryController::class, 'update']);
Route::delete('video-categories/{videoCategory}', [VideoCategoryController::class, 'destroy']);



Route::get('/videos', [VideoController::class, 'index']);
Route::post('/videos', [VideoController::class, 'store']);
Route::post('/videos/{video}', [VideoController::class, 'update']);
Route::get('/videos/{video}', [VideoController::class, 'show']);
Route::delete('/videos/{video}', [VideoController::class, 'destroy']);



Route::post('/social-links', [SocialLinkController::class, 'store']);
Route::post('/social-links/{idOrPlatform}', [SocialLinkController::class, 'update']);
Route::delete('/social-links/{socialLink}', [SocialLinkController::class, 'destroy']);


Route::get('/pages', [PageController::class, 'index']);
Route::post('/pages', [PageController::class, 'store']);
Route::get('/pages/{page}', [PageController::class, 'show']);
Route::post('/pages/{page}', [PageController::class, 'update']);
Route::delete('/pages/{page}', [PageController::class, 'destroy']);

Route::post('advertisements', [AdvertisementController::class, 'store']);
Route::delete('advertisements/{slug}', [AdvertisementController::class, 'destroy']);



Route::get('/live_videos', [LiveVideoController::class, 'index']);
Route::post('/live_videos', [LiveVideoController::class, 'store']);
Route::get('/live_videos/{id}', [LiveVideoController::class, 'show']);
Route::post('/live_videos/{id}', [LiveVideoController::class, 'update']);
Route::delete('/live_videos/{id}', [LiveVideoController::class, 'destroy']);

Route::post('/live_video/last', [LiveVideoController::class, 'updateLastVideo']);




Route::get('selected-articles', [SelectedArticleController::class, 'index']);
Route::post('selected-articles', [SelectedArticleController::class, 'store']);
Route::get('selected-articles/{id}', [SelectedArticleController::class, 'show']);
Route::post('selected-articles/{id}', [SelectedArticleController::class, 'update']);
Route::delete('selected-articles/{id}', [SelectedArticleController::class, 'destroy']);
Route::delete('selected-article/delete-by-date', [SelectedArticleController::class, 'deleteByDate']);
Route::get('selected-articles/{id}/related', [SelectedArticleController::class, 'relatedArticles']);


Route::post('selected-article/update-multiple-by-date', [SelectedArticleController::class, 'updateMultipleByDate']);
Route::get('selected-article/filter-by-date', [SelectedArticleController::class, 'filterByDate']);


});




    // Article routes
    Route::get('/articles', [ArticleController::class, 'index']);
    Route::get('/articles/by-category/{categoryId}', [ArticleController::class, 'getByCategory']);
    Route::get('/articles/{id}', [ArticleController::class, 'show']);
    Route::get('/article/{slug}', [ArticleController::class, 'showBySlug']);
    Route::get('/articles/list/{slug}', [ArticleController::class, 'getArticlesBySlug']);

    Route::get('/articles/date/{date}', [ArticleController::class, 'getArticlesByDate']);

    Route::get('/all/latest/articles', [ArticleController::class, 'getLatestarticles']);
    Route::get('/all/related/articles/{articleSlug}', [ArticleController::class, 'getRelatedArticles']);


    // Comment routes
    Route::get('/comments', [CommentController::class, 'index']);
    Route::get('/comments/{id}', [CommentController::class, 'show']);


    // Category routes
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{id}', [CategoryController::class, 'show']);
    Route::get('/categories/{id}/subcategories', [CategoryController::class, 'getSubcategories']);


    Route::get('video-categories', [VideoCategoryController::class, 'index']);
    Route::get('/video/all/list', [VideoController::class, 'allListByCategory']);
    Route::get('/videos/list/{categoryname}', [VideoController::class, 'listByCategory']);
    Route::get('/video/{slug}', [VideoController::class, 'showBySlug']);


    Route::get('/social-links', [SocialLinkController::class, 'index']);
    Route::get('/social-links/{platform}', [SocialLinkController::class, 'showByPlatform']);

    Route::get('/pages/slug/{slug}', [PageController::class, 'showBySlug']);

    Route::get('advertisements', [AdvertisementController::class, 'index']);

    Route::get('/visitors', [VisitorController::class, 'index']);
    Route::get('/visitors/reports', [VisitorController::class, 'generateReports']);
    Route::get('/live_video/last', [LiveVideoController::class, 'getLastVideo']);




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







