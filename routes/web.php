<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('admin', 'DashboardController@index');


// Confide routes
// Route::get('users/create', 'UsersController@create');
// Route::post('users', 'UsersController@store');
// Route::get('admin/login', 'UsersController@login');
// Route::post('users/login', 'UsersController@doLogin');
// Route::get('users/confirm/{code}', 'UsersController@confirm');
// Route::get('users/forgot_password', 'UsersController@forgotPassword');
// Route::post('users/forgot_password', 'UsersController@doForgotPassword');
// Route::get('users/reset_password/{token}', 'UsersController@resetPassword');
// Route::post('users/reset_password', 'UsersController@doResetPassword');
// Route::get('admin/logout', ['as' => 'admin.logout', 'uses' => 'UsersController@logout']);

Route::group(['before' => 'auth'], function () {
    Route::get('admin/hot-manga', ['as' => 'admin.manga.hot', 'uses' => 'MangaController@hotManga']);
    Route::post('autoMangaInfo', ['as' => 'admin.manga.autoMangaInfo', 'uses' => 'MangaController@autoMangaInfo']);
    Route::post('updateHotManga', 'MangaController@updateHotManga');
    
    Route::get('admin/options-manga', ['as' => 'admin.manga.options', 'uses' => 'MangaController@mangaOptions']);
    Route::post('saveMangaOptions', ['as' => 'admin.manga.options.save', 'uses' => 'MangaController@saveMangaOptions']);
    Route::get('admin/filterMangaList', 'MangaController@filterList');
    Route::resource('admin/manga', 'MangaController');
    
    Route::get('admin/manga/{manga}/chapter/scraper', ['as' => 'admin.manga.chapter.scraper', 'uses' => 'WebScraperController@scraper']);
    Route::post('startScraper', 'WebScraperController@startScraper');
    Route::post('getTotalChapters', 'WebScraperController@getTotalChapters');
    Route::post('getChapter', 'WebScraperController@getChapter');
    Route::post('abort', 'WebScraperController@abort');
    Route::post('resume', 'WebScraperController@resume');

    
    Route::delete('admin/destroyChapters/{mangaId}', ['as' => 'admin.manga.chapter.destroyChapters', 'uses' => 'ChapterController@destroyChapters']);
    Route::post('notifyUsers', 'ChapterController@notifyUsers');
    Route::resource('admin/manga.chapter', 'ChapterController');

    Route::delete('admin/destroyPages/{mangaId}/{chapterId}', ['as' => 'admin.manga.chapter.page.destroyPages', 'uses' => 'PageController@destroyPages']);
    Route::post('downloadImageFromUrl', 'PageController@downloadImageFromUrl');
    Route::post('uploadZIPFile', 'PageController@uploadZIPFile');
    Route::post('createExternalPages', 'PageController@createExternalPages');
    Route::post('movePage', 'PageController@movePage');
    Route::resource('admin/manga.chapter.page', 'PageController');

    Route::resource('admin/category', 'Communs\CategoryController');

    // CKeditor upload image
    Route::post('admin/uploadPostImage', ['as' => 'admin.posts.uploadImage', 'uses' => 'PostController@uploadImage']);
    Route::get('admin/uploadBrowseImage', ['as' => 'admin.posts.browseImage', 'uses' => 'PostController@browseImage']);
    Route::post('admin/deletePostImage', ['as' => 'admin.posts.deletePostImage', 'uses' => 'PostController@deletePostImage']);

    Route::resource('admin/comictype', 'ComicTypeController');
    Route::resource('admin/posts', 'PostController');

    // Settings
    Route::get('admin/general', ['as' => 'admin.settings.general', 'uses' => 'Settings\SettingsController@general']);
    Route::post('admin/general', ['as' => 'admin.settings.general.save', 'uses' => 'Settings\SettingsController@saveGeneral']);

    Route::get('admin/seo', ['as' => 'admin.settings.seo', 'uses' => 'Settings\SettingsController@seo']);
    Route::post('admin/seo', ['as' => 'admin.settings.seo.save', 'uses' => 'Settings\SettingsController@saveSeo']);

    Route::get('admin/profile', ['as' => 'admin.settings.profile', 'uses' => 'Settings\SettingsController@profile']);
    Route::post('admin/profile', ['as' => 'admin.settings.profile.save', 'uses' => 'Settings\SettingsController@saveProfile']);

    Route::get('admin/theme', ['as' => 'admin.settings.theme', 'uses' => 'Settings\SettingsController@theme']);
    Route::post('admin/theme', ['as' => 'admin.settings.theme.save', 'uses' => 'Settings\SettingsController@saveTheme']);

    Route::get('admin/widgets', ['as' => 'admin.settings.widgets', 'uses' => 'Settings\SettingsController@widgets']);
    Route::post('admin/widgets', ['as' => 'admin.settings.widgets.save', 'uses' => 'Settings\SettingsController@saveWidgets']);

    Route::get('admin/cache', ['as' => 'admin.settings.cache', 'uses' => 'Settings\SettingsController@cache']);
    Route::post('admin/cache', ['as' => 'admin.settings.cache.save', 'uses' => 'Settings\SettingsController@saveCache']);
    Route::post('admin/clear-cache', ['as' => 'admin.settings.cache.clear', 'uses' => 'Settings\SettingsController@clearCache']);
    Route::post('admin/clear-downloads', ['as' => 'admin.settings.downloads.clear', 'uses' => 'Settings\SettingsController@clearDownloads']);

    // Google
    /*Route::get('admin/gdrive', ['as' => 'admin.settings.gdrive', 'uses' => 'Cyberziko\Gdrive\Controllers\GoogleController@index']);
    Route::post('admin/gdrive/reset', ['as' => 'admin.settings.gdrive.reset', 'uses' => 'Cyberziko\Gdrive\Controllers\GoogleController@resetGdrive']);
    Route::post('admin/gdrive', ['as' => 'admin.settings.gdrive.save', 'uses' => 'Cyberziko\Gdrive\Controllers\GoogleController@saveGdrive']);*/

    Route::post('saveSubscription', ['as' => 'admin.users.subsciption', 'uses' => 'ManageUserController@saveSubscription']);
    Route::get('admin/permissions', ['as' => 'admin.users.permissions', 'uses' => 'ManageUserController@permissions']);
    Route::resource('admin/role', 'RoleController');
    Route::resource('admin/user', 'ManageUserController');

    Route::post('uploadMangaCover', 'Utils\FileUploadController@uploadMangaCover');
    Route::post('uploadMangaChapterPage', 'Utils\FileUploadController@uploadMangaChapterPage');
    Route::post('deleteImage', 'Utils\FileUploadController@deleteImage');
    Route::post('deleteCover', 'Utils\FileUploadController@deleteCover');
    Route::post('uploadAvatar', 'Utils\FileUploadController@uploadAvatar');
    Route::post('deleteAvatar', 'Utils\FileUploadController@deleteAvatar');

    Route::post('admin/ads/storePlacements', ['as' => 'admin.ads.storePlacements', 'uses' => 'ManageAdsController@storePlacements']);
    Route::resource('admin/ads', 'ManageAdsController');

    Route::resource('admin', 'DashboardController');
    
    Route::resource('bookmark', 'BookmarkController');
    Route::get('loadTabData', 'BookmarkController@loadTabData');
    Route::post('changeStatus', 'BookmarkController@changeStatus');
    Route::post('deleteChecked', 'BookmarkController@deleteChecked');
    Route::post('saveNotificationOption', 'BookmarkController@saveNotificationOption');
    
    // my profil
    Route::get('/user/{user}/edit', ['as' => 'user.profil.edit', 'uses' => 'MySpaceController@editUserProfil']);
    Route::post('/user/{user}/save', ['as' => 'user.profil.save', 'uses' => 'MySpaceController@saveUserProfil']);
    
});

Route::get('/user/{user}', ['as' => 'user.profil.index', 'uses' => 'MySpaceController@userProfil']);

Route::get('socialCounts', 'FrontController@socialCounts');
Route::get('/', ['as' => 'front.index', 'uses' => 'FrontController@index']);



Route::group(array('before' => 'cache', 'after' => 'cache'), function() {
    Route::get('/manga/{manga}/{chapter}/{page?}', ['as' => 'front.manga.reader', 'uses' => 'ReaderController@reader'])->where('page', '^[1-9][0-9]*');
});

Route::get('/manga/{manga}', ['as' => 'front.manga.show', 'uses' => 'FrontController@show']);
Route::get('/news/{post}', ['as' => 'front.news', 'uses' => 'FrontController@news']);
Route::get('/latest-release', ['as' => 'front.manga.latestRelease', 'uses' => 'FrontController@latestRelease']);
Route::get('/latest-news', ['as' => 'front.manga.latestNews', 'uses' => 'FrontController@latestNews']);
Route::get('/random', ['as' => 'front.manga.random', 'uses' => 'FrontController@randomManga']);
Route::get('/contact-us', ['as' => 'front.manga.contactUs', 'uses' => 'FrontController@contactUs']);
Route::post('/contact-us', ['as' => 'front.manga.sendMessage', 'uses' => 'FrontController@sendMessage']);
Route::post('/report-bug', ['as' => 'front.manga.reportBug', 'uses' => 'ReaderController@reportBug']);
Route::get('search', 'FrontController@search');

Route::get('filter', 'FrontController@filter');
Route::get('filterList', 'FrontController@filterList');
Route::get('changeMangaList', 'FrontController@changeMangaList');
Route::get('/manga-list/{type?}/{archive?}', ['as' => 'front.manga.list', 'uses' => 'FrontController@mangalist']);
Route::get('/manga-list', ['as' => 'front.manga.list', 'uses' => 'FrontController@mangalist']);

Route::get('/install', ['as' => 'install.index', 'uses' => 'InstallerController@index']);
Route::post('startInstall', 'InstallerController@startInstall');

Route::get('/robots.txt', 'FrontController@robots');
Route::get('/sitemap.xml', 'FrontController@sitemap');
Route::get('/feed', 'FrontController@feed');

// Route::when('manga/*', 'manga.view_throttle');

Route::get('/download/{mangaSlug}/{chapterId}', ['as' => 'front.manga.download', 'uses' => 'FrontController@downloadChapter']);

Route::get('/advanced-search', ['as' => 'front.advSearch', 'uses' => 'FrontController@advSearch']);
Route::post('advSearchFilter', 'FrontController@advSearchFilter');

// comment
Route::get('/api/comments/{type}/{id}', ['as' => 'api.comments.index', 'uses' => 'CommentController@index']);
Route::group(array('prefix' => 'api'), function() {
    Route::resource('comments', 'CommentController', array('except' => array('create', 'edit', 'update')));
});


Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
