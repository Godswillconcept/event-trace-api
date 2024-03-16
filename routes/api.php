<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\VenueController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function () {

    // users
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::get('/users', [AuthController::class, 'index']);
    Route::put('/users/{slug}', [AuthController::class, 'update']);
    Route::post('/users/{slug}/image', [AuthController::class, 'uploadImage']);
    Route::delete('/user/{slug}', [AuthController::class, 'destroy']);

    // bookings 
    Route::get('/bookings', [BookingController::class, 'index']);
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::get('/bookings/{id}', [BookingController::class, 'show']);
    Route::put('/bookings/{id}', [BookingController::class, 'update']);
    Route::delete('/bookings/{id}', [BookingController::class, 'destroy']);

    // promotions 
    Route::get('/promotions', [PromotionController::class, 'index']);
    Route::post('/promotions', [PromotionController::class, 'store']);
    Route::get('/promotions/{id}', [PromotionController::class, 'show']);
    Route::put('/promotions/{id}', [PromotionController::class, 'update']);
    Route::delete('/promotions/{id}', [PromotionController::class, 'destroy']);

    // transactions
    Route::get('/transactions', [TransactionController::class, 'index']);
    Route::post('/transactions', [TransactionController::class, 'store']);
    Route::get('/transactions/{id}', [TransactionController::class, 'show']);
    Route::put('/transactions/{id}', [TransactionController::class, 'update']);
    Route::delete('/transactions/{id}', [TransactionController::class, 'destroy']);

    // events
    Route::get('/events', [EventController::class, 'index']);
    Route::get('/events/{slug}', [EventController::class, 'show']);
    Route::get('/events/{slug}/tickets', [EventController::class, 'tickets']);
    Route::post('/events', [EventController::class, 'store'])->middleware('isEventOrganizer');
    Route::put('/events/{slug}', [EventController::class, 'update'])->middleware('isEventOrganizer');
    Route::get('/events/{slug}/bookings', [EventController::class, 'bookings'])->middleware('isEventOrganizer');
    Route::post('/events/{slug}/photos', [EventController::class, 'attachPhotos'])->middleware('isEventOrganizer');
    Route::delete('/events/{slug}', [EventController::class, 'destroy'])->middleware('isEventOrganizer');

    // faqs 
    Route::get('/faqs', [FaqController::class, 'index']);
    Route::post('/faqs/{slug}', [FaqController::class, 'store'])->middleware('isEventOrganizer');
    Route::put('/faqs/{slug}', [FaqController::class, 'update'])->middleware('isEventOrganizer');
    Route::delete('/faqs/{slug}', [FaqController::class, 'destroy'])->middleware('isEventOrganizer');

    // tickets 
    Route::get('/tickets', [TicketController::class, 'index']);
    Route::get('/tickets/{id}', [TicketController::class, 'show']);
    Route::get('/tickets', [TicketController::class, 'index'])->middleware('isEventOrganizer');
    Route::post('/tickets', [TicketController::class, 'store'])->middleware('isEventOrganizer');
    Route::put('/tickets/{id}', [TicketController::class, 'update'])->middleware('isEventOrganizer');
    Route::delete('/tickets/{id}', [TicketController::class, 'destroy'])->middleware('isEventOrganizer');


    // venues
    Route::get('/venues', [VenueController::class, 'index']);
    Route::get('/venues/{slug}', [VenueController::class, 'show']);
    Route::post('/venues', [VenueController::class, 'store'])->middleware('isVenueOwner');
    Route::put('/venues/{slug}', [VenueController::class, 'update'])->middleware('isVenueOwner');
    Route::put('/venues/{slug}/amenities', [VenueController::class, 'attachAmenities'])->middleware('isVenueOwner');
    Route::post('/venues/{slug}/photos', [VenueController::class, 'attachPhotos'])->middleware('isVenueOwner');
    Route::get('/venues/{slug}/events', [VenueController::class, 'events'])->middleware('isVenueOwner');
    Route::delete('/venues/{slug}', [VenueController::class, 'destroy'])->middleware('isVenueOwner');

    // categories
    Route::get('categories', [CategoryController::class, 'index']);
    Route::get('categories/{slug}', [CategoryController::class, 'show']);

    // admin routes extensions 
    Route::prefix('admin')->group(function () {
        // users
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('user', [AuthController::class, 'user']);
        Route::get('users', [AuthController::class, 'index']);
        Route::put('users/{slug}', [AuthController::class, 'update']);
        Route::post('users/{slug}/image', [AuthController::class, 'uploadImage']);
        Route::delete('user/{slug}', [AuthController::class, 'destroy']);
        // categories
        Route::get('categories', [CategoryController::class, 'index'])->middleware('isAdmin');
        Route::post('categories', [CategoryController::class, 'store'])->middleware('isAdmin');
        Route::get('categories/{slug}', [CategoryController::class, 'show'])->middleware('isAdmin');
        Route::put('categories/{slug}', [CategoryController::class, 'update'])->middleware('isAdmin');
        Route::delete('categories/{slug}', [CategoryController::class, 'destroy'])->middleware('isAdmin');
        // events
        Route::get('events', [EventController::class, 'index']);
        Route::get('events/{slug}', [EventController::class, 'show']);
        Route::get('events/{slug}/tickets', [EventController::class, 'tickets']);
        Route::get('events/{slug}/faqs', [EventController::class, 'faqs']);
        // faqs 
        Route::get('faqs', [FaqController::class, 'index']);
        // venues 
        Route::get('venues', [VenueController::class, 'index']);
        Route::get('venues/{slug}', [VenueController::class, 'show']);
        Route::get('venues/{slug}/events', [VenueController::class, 'events']);
        // tickets 
        Route::get('tickets', [TicketController::class, 'index']);
        Route::get('tickets/{id}', [TicketController::class, 'show']);
    });
});
