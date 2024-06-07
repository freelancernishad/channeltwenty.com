<?php

use App\Models\Article;
use App\Models\Permission;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MacController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\VisitorController;
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\api\UserController;

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\RoleUserController;
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




Route::get('get/all/route/name', function () {
// Get all routes
$routes = Route::getRoutes();

// Iterate through routes
foreach ($routes as $route) {
    // Get route action
    $action = $route->getAction();


    // Check if middleware is defined in the action
    if (isset($action['middleware'])) {
        $hasCheckPermission = false;
        foreach ($action['middleware'] as $middleware) {
            if (strpos($middleware, 'checkPermission') !== false) {
                $hasCheckPermission = true;
            }
        }



        // Check if the middleware is 'checkPermission'
        if ($hasCheckPermission) {



            // Get route name
            $routeName = $route->getName();


            // Check if route has a name
            if ($routeName) {
                // Check if permission already exists
                $existingPermission = Permission::where('path', $routeName)->first();

                // If permission doesn't exist, create it
                if (!$existingPermission) {
                    Permission::create([
                        'name' => $routeName,
                        'path' => $routeName,
                        // Add other attributes like element, permission, description if needed
                    ]);
                }
            }






        }
    }
}
});




Route::get('/update/slug', [ArticleController::class, 'updateSlugs']);



Route::get('roles', [RoleController::class, 'index']);
Route::post('roles', [RoleController::class, 'store']);
Route::post('roles/{roles}', [RoleController::class, 'update']);
// Route::apiResource('roles', RoleController::class);

// Route::get('permissions/', [PermissionController::class, 'index']);
// Route::post('permissions', [PermissionController::class, 'store']);
// Route::post('permissions/{permissions}', [PermissionController::class, 'update']);

Route::apiResource('permissions', PermissionController::class);



Route::post('get/permissions/{id}', [RoleController::class, 'getPermissionsByRoleName']);
Route::post('roles/{role}/permissions/{permission}', [RolePermissionController::class, 'attachPermission']);
Route::post('roles/{roleId}/permissions', [RolePermissionController::class, 'addPermissionsToRole']);
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
    Route::post('/user/logout', [AuthController::class, 'logout'])->name('user.logout');

    // Routes for user registration, update, delete, and show
    // Route::prefix('users')->group(function () {
    //     Route::put('{id}', [UserController::class, 'update'])->name('users.update')->middleware('checkPermission:users.update');
    //     Route::delete('{id}', [UserController::class, 'delete'])->name('users.delete')->middleware('checkPermission:users.delete');
    //     Route::get('{id}', [UserController::class, 'show'])->name('users.show')->middleware('checkPermission:users.show');
    // });


    ///role users

    Route::prefix('users/role/system')->group(function () {
        Route::get('/', [RoleUserController::class, 'index']); // List all users
        Route::post('/', [RoleUserController::class, 'store']); // store a specific user
        Route::post('/{id}', [RoleUserController::class, 'update']); // Update a specific user
        Route::get('/{id}', [RoleUserController::class, 'show']); // show a specific user
        Route::delete('/{id}', [RoleUserController::class, 'destroy']); // Delete a specific user
    });




    Route::post('users/change-password', [UserController::class, 'changePassword'])->name('users.change_password')->middleware('checkPermission:users.change_password');
    Route::get('/user-access', function (Request $request) {
        return 'user access';
    })->name('user.access')->middleware('checkPermission:user.access');

    // Add names to other routes

    Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index')->middleware('checkPermission:articles.index');


    Route::post('/articles', [ArticleController::class, 'store'])->name('articles.store')->middleware('checkPermission:articles.store');
    Route::get('/articles/{id}', [ArticleController::class, 'show'])->name('articles.show')->middleware('checkPermission:articles.show');
    Route::post('/articles/{id}', [ArticleController::class, 'update'])->name('articles.update')->middleware('checkPermission:articles.update');
    Route::delete('/articles/{id}', [ArticleController::class, 'destroy'])->name('articles.destroy')->middleware('checkPermission:articles.destroy');

    // Add names to other routes
    Route::get('/article/list/author', [ArticleController::class, 'getlistByAuthor'])->name('articles.list_author')->middleware('checkPermission:articles.list_author');

    // Comment routes
    Route::get('/comments', [CommentController::class, 'index'])->name('comments.index')->middleware('checkPermission:comments.index');
    Route::get('/comments/{id}', [CommentController::class, 'show'])->name('comments.show')->middleware('checkPermission:comments.show');
    Route::delete('/comments/{id}', [CommentController::class, 'destroy'])->name('comments.destroy')->middleware('checkPermission:comments.destroy');

    // Add names to other routes
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index')->middleware('checkPermission:categories.index');

    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store')->middleware('checkPermission:categories.store');
    Route::post('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update')->middleware('checkPermission:categories.update');
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy')->middleware('checkPermission:categories.destroy');

    Route::post('video-categories', [VideoCategoryController::class, 'store'])->name('video_categories.store')->middleware('checkPermission:video_categories.store');
    Route::get('video-categories/{videoCategory}', [VideoCategoryController::class, 'show'])->name('video_categories.show')->middleware('checkPermission:video_categories.show');
    Route::post('video-categories/{videoCategory}', [VideoCategoryController::class, 'update'])->name('video_categories.update')->middleware('checkPermission:video_categories.update');
    Route::delete('video-categories/{videoCategory}', [VideoCategoryController::class, 'destroy'])->name('video_categories.destroy')->middleware('checkPermission:video_categories.destroy');

    // Add names to other routes
    Route::get('/videos', [VideoController::class, 'index'])->name('videos.index')->middleware('checkPermission:videos.index');
    Route::post('/videos', [VideoController::class, 'store'])->name('videos.store')->middleware('checkPermission:videos.store');
    Route::post('/videos/{video}', [VideoController::class, 'update'])->name('videos.update')->middleware('checkPermission:videos.update');
    Route::get('/videos/{video}', [VideoController::class, 'show'])->name('videos.show')->middleware('checkPermission:videos.show');
    Route::delete('/videos/{video}', [VideoController::class, 'destroy'])->name('videos.destroy')->middleware('checkPermission:videos.destroy');

    // Add names to other routes
    Route::post('/social-links', [SocialLinkController::class, 'store'])->name('social_links.store')->middleware('checkPermission:social_links.store');
    Route::post('/social-links/{idOrPlatform}', [SocialLinkController::class, 'update'])->name('social_links.update')->middleware('checkPermission:social_links.update');
    Route::delete('/social-links/{socialLink}', [SocialLinkController::class, 'destroy'])->name('social_links.destroy')->middleware('checkPermission:social_links.destroy');

    // Add names to other routes
    Route::get('/pages', [PageController::class, 'index'])->name('pages.index')->middleware('checkPermission:pages.index');
    Route::post('/pages', [PageController::class, 'store'])->name('pages.store')->middleware('checkPermission:pages.store');
    Route::get('/pages/{page}', [PageController::class, 'show'])->name('pages.show')->middleware('checkPermission:pages.show');
    Route::post('/pages/{page}', [PageController::class, 'update'])->name('pages.update')->middleware('checkPermission:pages.update');
    Route::delete('/pages/{page}', [PageController::class, 'destroy'])->name('pages.destroy')->middleware('checkPermission:pages.destroy');

    Route::post('advertisements', [AdvertisementController::class, 'store'])->name('advertisements.store')->middleware('checkPermission:advertisements.store');
    Route::delete('advertisements/{slug}', [AdvertisementController::class, 'destroy'])->name('advertisements.destroy')->middleware('checkPermission:advertisements.destroy');

    // Add names to other routes
    Route::get('/live_videos', [LiveVideoController::class, 'index'])->name('live_videos.index')->middleware('checkPermission:live_videos.index');
    Route::post('/live_videos', [LiveVideoController::class, 'store'])->name('live_videos.store')->middleware('checkPermission:live_videos.store');
    Route::get('/live_videos/{id}', [LiveVideoController::class, 'show'])->name('live_videos.show')->middleware('checkPermission:live_videos.show');
    Route::post('/live_videos/{id}', [LiveVideoController::class, 'update'])->name('live_videos.update')->middleware('checkPermission:live_videos.update');
    Route::delete('/live_videos/{id}', [LiveVideoController::class, 'destroy'])->name('live_videos.destroy')->middleware('checkPermission:live_videos.destroy');

    Route::post('/live_video/last', [LiveVideoController::class, 'updateLastVideo'])->name('live_video.last')->middleware('checkPermission:live_video.last');

    Route::get('selected-articles', [SelectedArticleController::class, 'index'])->name('selected_articles.index')->middleware('checkPermission:selected_articles.index');
    Route::post('selected-articles', [SelectedArticleController::class, 'store'])->name('selected_articles.store')->middleware('checkPermission:selected_articles.store');
    Route::get('selected-articles/{id}', [SelectedArticleController::class, 'show'])->name('selected_articles.show')->middleware('checkPermission:selected_articles.show');
    Route::post('selected-articles/{id}', [SelectedArticleController::class, 'update'])->name('selected_articles.update')->middleware('checkPermission:selected_articles.update');
    Route::delete('selected-articles/{id}', [SelectedArticleController::class, 'destroy'])->name('selected_articles.destroy')->middleware('checkPermission:selected_articles.destroy');
    Route::delete('selected-article/delete-by-date', [SelectedArticleController::class, 'deleteByDate'])->name('selected_articles.delete_by_date')->middleware('checkPermission:selected_articles.delete_by_date');
    Route::get('selected-articles/{id}/related', [SelectedArticleController::class, 'relatedArticles'])->name('selected_articles.related')->middleware('checkPermission:selected_articles.related');

    Route::post('selected-article/update-multiple-by-date', [SelectedArticleController::class, 'updateMultipleByDate'])->name('selected_articles.update_multiple_by_date')->middleware('checkPermission:selected_articles.update_multiple_by_date');
    Route::get('selected-article/filter-by-date', [SelectedArticleController::class, 'filterByDate'])->name('selected_articles.filter_by_date')->middleware('checkPermission:selected_articles.filter_by_date');











});


Route::get('/weather', [WeatherController::class, 'show']);


Route::get('/get-mac', [MacController::class, 'getMacAddress']);
Route::get('/check-python', [MacController::class, 'checkPython']);


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







