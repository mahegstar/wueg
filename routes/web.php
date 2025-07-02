<?php

use App\Http\Controllers\BadgeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContestController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SubcategoryController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect('/login');
});

Auth::routes();

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard-year/{year}', [DashboardController::class, 'getYearForMonthChart']);
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password');
    
    // Users
    Route::get('/users', [DashboardController::class, 'users'])->name('users');
    Route::get('/battle-statistics/{id}', [DashboardController::class, 'battleStatistics']);
    
    // Categories
    Route::get('/main-category', [CategoryController::class, 'index'])->name('category.index');
    Route::post('/main-category', [CategoryController::class, 'store'])->name('category.store');
    Route::put('/main-category/{id}', [CategoryController::class, 'update'])->name('category.update');
    Route::delete('/delete_category', [CategoryController::class, 'destroy'])->name('category.destroy');
    Route::post('/category-order', [CategoryController::class, 'updateOrder'])->name('category.order');
    Route::post('/get-category-slug', [CategoryController::class, 'getSlug']);
    Route::post('/verify-category-slug', [CategoryController::class, 'verifySlug']);
    
    // Subcategories
    Route::get('/sub-category', [SubcategoryController::class, 'index'])->name('subcategory.index');
    Route::post('/sub-category', [SubcategoryController::class, 'store'])->name('subcategory.store');
    Route::put('/sub-category/{id}', [SubcategoryController::class, 'update'])->name('subcategory.update');
    Route::delete('/delete_subcategory', [SubcategoryController::class, 'destroy'])->name('subcategory.destroy');
    Route::post('/get-subcategory-slug', [SubcategoryController::class, 'getSlug']);
    Route::post('/verify-subcategory-slug', [SubcategoryController::class, 'verifySlug']);
    
    // Questions
    Route::get('/create-questions', [QuestionController::class, 'index'])->name('questions.index');
    Route::post('/create-questions', [QuestionController::class, 'store'])->name('questions.store');
    Route::get('/create-questions/{id}', [QuestionController::class, 'edit'])->name('questions.edit');
    Route::put('/create-questions/{id}', [QuestionController::class, 'update'])->name('questions.update');
    Route::delete('/delete_questions', [QuestionController::class, 'destroy'])->name('questions.destroy');
    Route::get('/manage-questions', [QuestionController::class, 'manage'])->name('questions.manage');
    Route::get('/question-reports', [QuestionController::class, 'reports'])->name('questions.reports');
    Route::get('/question-reports/{id}', [QuestionController::class, 'editReport'])->name('questions.edit-report');
    Route::delete('/delete_question_report', [QuestionController::class, 'deleteReport'])->name('questions.delete-report');
    Route::get('/import-questions', [QuestionController::class, 'import'])->name('questions.import');
    Route::post('/import-questions', [QuestionController::class, 'importProcess'])->name('questions.import-process');
    
    // Contests
    Route::get('/contest', [ContestController::class, 'index'])->name('contest.index');
    Route::post('/contest', [ContestController::class, 'store'])->name('contest.store');
    Route::put('/contest/{id}', [ContestController::class, 'update'])->name('contest.update');
    Route::delete('/delete_contest', [ContestController::class, 'destroy'])->name('contest.destroy');
    Route::post('/contest-status', [ContestController::class, 'updateStatus'])->name('contest.status');
    Route::get('/contest-prize/{id}', [ContestController::class, 'contestPrize'])->name('contest.prize');
    Route::post('/contest-prize', [ContestController::class, 'storeContestPrize'])->name('contest.prize.store');
    Route::put('/contest-prize/{id}', [ContestController::class, 'updateContestPrize'])->name('contest.prize.update');
    Route::delete('/delete_contest_prize', [ContestController::class, 'destroyContestPrize'])->name('contest.prize.destroy');
    Route::get('/contest-leaderboard/{id}', [ContestController::class, 'contestLeaderboard'])->name('contest.leaderboard');
    Route::get('/contest-prize-distribute/{id}', [ContestController::class, 'contestPrizeDistribute'])->name('contest.prize.distribute');
    
    // AJAX routes
    Route::post('/get_categories_of_language', [DashboardController::class, 'getCategoriesOfLanguage']);
    Route::post('/get_subcategories_of_category', [DashboardController::class, 'getSubcategoriesOfCategory']);
    Route::post('/get_subcategories_of_language', [DashboardController::class, 'getSubcategoriesOfLanguage']);
    Route::post('/delete_multiple', [DashboardController::class, 'deleteMultiple']);
    Route::post('/removeImage', [DashboardController::class, 'removeImage']);
});