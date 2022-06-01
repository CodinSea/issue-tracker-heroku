<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProjectsController;
use App\Http\Controllers\TicketsController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\UserAuthController;
use App\Http\Livewire\ProjectTeam;

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
Route::post('/directDemoUser', [UserAuthController::class, 'directDemoUser'])->name('directDemoUser');

Route::post('/createUser', [UserAuthController::class, 'createUser'])->name('createUser');

Route::post('/checkUser', [UserAuthController::class, 'checkUser'])->name('checkUser');

Route::get('/logout', [UserAuthController::class, 'logout'])->name('logout');

Route::get('/forgotPassword', [UserAuthController::class, 'forgotPassword'])->name('forgotPassword');

Route::post('/forgotPassword', [UserAuthController::class, 'sendPasswordResetLink'])->name('sendPasswordResetLink');

Route::get('/resetPassword/{token}', [UserAuthController::class, 'resetPasswordForm'])->name('resetPasswordForm');

Route::post('/resetPassword', [UserAuthController::class, 'resetPassword'])->name('resetPassword');

Route::group(['middleware'=>['AuthCheck']], function(){
	Route::get('/demoUser', [UserAuthController::class, 'demoUser'])->name('demoUser');

	Route::get('/login', [UserAuthController::class, 'login'])->name('login');

	Route::get('/register', [UserAuthController::class, 'register'])->name('register');

	Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

	Route::get('/roleAssignment', [UserAuthController::class, 'index'])->name('roleAssignment')->middleware('admin');

	Route::post('/assignRole', [UserAuthController::class, 'assignRole'])->name('assignRole');

	Route::get('/projectAssignment', [ProjectsController::class, 'projectAssignment'])->name('projectAssignment')->middleware('admin_or_project_manager');

	Route::get('/projectAssignment/{projectId}', [ProjectsController::class, 'projectAssignmentWithProjectId'])->name('projectAssignmentWithProjectId')->middleware('admin_or_project_manager');

	Route::post('/assignProject', [ProjectsController::class, 'assignProject'])->name('assignProject');

	Route::get('/projects', [ProjectsController::class, 'index'])->name('projects');

	Route::get('/projects/{id}', [ProjectsController::class, 'show'])->name('projects.show');

	//Route::get('/projects/editForm/{id}', [ProjectsController::class, 'editProjectForm'])->name('projects.editProjectForm');

	Route::post('/projects/edit/{id}', [ProjectsController::class, 'editProject'])->name('editProject');

	Route::post('/createNewProject', [ProjectsController::class, 'createNewProject'])->name('createNewProject');

	Route::get('/tickets', [TicketsController::class, 'index'])->name('tickets');

	Route::get('/tickets/{id}', [TicketsController::class, 'show'])->name('tickets.show');

	//Route::get('/tickets/editForm/{id}', [TicketsController::class, 'editTicketForm'])->name('tickets.editTicketForm');

	Route::post('/tickets/edit/{id}', [TicketsController::class, 'editTicket'])->name('editTicket');

	Route::post('/createNewTicket', [TicketsController::class, 'createNewTicket'])->name('createNewTicket');

	Route::post('/addComment', [TicketsController::class, 'addComment'])->name('addComment');

	Route::post('/uploadAttachment', [TicketsController::class, 'uploadAttachment'])->name('uploadAttachment');

	Route::get('/downloadAttachment/{id}', [TicketsController::class, 'downloadAttachment'])->name('downloadAttachment');

	Route::get('/userProfile', [UserAuthController::class, 'userProfile'])->name('userProfile');

	Route::post('/editUserProfile', [UserAuthController::class, 'editUserProfile'])->name('editUserProfile');

	Route::post('/uploadPicture', [UserAuthController::class, 'uploadPicture'])->name('uploadPicture');
});

/*
Route::get('/signup', [UsersController::class, 'index']);

Route::post('/signUpUser', [UsersController::class, 'signUpUser'])->name('signUpUser');
*/