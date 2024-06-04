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


Route::get('/bookings', [BookingController::class, 'index']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/events', [EventController::class, 'index']);
Route::get('/faqs', [FaqController::class, 'index']);
Route::get('/promotions', [PromotionController::class, 'index']);
Route::get('/tickets', [TicketController::class, 'index']);
Route::get('/transactions', [TransactionController::class, 'index']);
Route::get('/users', [AuthController::class, 'index']);
Route::get('/venues', [VenueController::class, 'index']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);


Route::group(['middleware' => ['auth:sanctum']], function () {

    // bookings 
    Route::delete('/bookings/{id}', [BookingController::class, 'destroy']);
    Route::get('/bookings/{id}', [BookingController::class, 'show']);
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::put('/bookings/{id}', [BookingController::class, 'update']);

    // categories
    Route::delete('categories/{slug}', [CategoryController::class, 'destroy'])->middleware('isAdmin');
    Route::get('categories/{slug}', [CategoryController::class, 'show']);
    Route::post('categories', [CategoryController::class, 'store'])->middleware('isAdmin');
    Route::put('categories/{slug}', [CategoryController::class, 'update'])->middleware('isAdmin');

    // events
    Route::delete('/events/{slug}', [EventController::class, 'destroy'])->middleware('isEventOrganizer');
    Route::get('/events/{slug}/bookings', [EventController::class, 'bookings'])->middleware('isEventOrganizer');
    Route::get('/events/{slug}/tickets', [EventController::class, 'tickets']);
    Route::post('/events/{slug}/tickets', [EventController::class, 'attachTickets'])->middleware('isEventOrganizer');
    Route::get('/events/{slug}', [EventController::class, 'show']);
    Route::post('/events/{slug}/photos', [EventController::class, 'attachPhotos'])->middleware('isEventOrganizer');
    Route::post('/events', [EventController::class, 'store'])->middleware('isEventOrganizer');  
    Route::put('/events/{slug}', [EventController::class, 'update'])->middleware('isEventOrganizer');
    Route::post('/events/{slug}/faqs', [EventController::class, 'attachFaqs']);
    Route::post('/events/{slug}/promotions', [EventController::class, 'attachPromotions']);

    // faqs 
    Route::delete('/faqs/{slug}', [FaqController::class, 'destroy'])->middleware('isEventOrganizer');
    Route::post('/faqs/{slug}', [FaqController::class, 'store'])->middleware('isEventOrganizer');
    Route::put('/faqs/{slug}', [FaqController::class, 'update'])->middleware('isEventOrganizer');

    // transactions
    Route::delete('/transactions/{id}', [TransactionController::class, 'destroy']);
    Route::get('/transactions/{id}', [TransactionController::class, 'show']);
    Route::post('/transactions', [TransactionController::class, 'store']);
    Route::put('/transactions/{id}', [TransactionController::class, 'update']);

    // tickets 
    Route::delete('/tickets/{id}', [TicketController::class, 'destroy'])->middleware('isEventOrganizer');
    Route::get('/tickets/{id}', [TicketController::class, 'show']);
    Route::put('/tickets/{id}', [TicketController::class, 'update'])->middleware('isEventOrganizer');

    // user
    Route::delete('/user/{slug}', [AuthController::class, 'destroy']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/users/{slug}/image', [AuthController::class, 'uploadImage']);
    Route::put('/users/{slug}', [AuthController::class, 'update']);

    // venues
    Route::delete('/venues/{slug}', [VenueController::class, 'destroy'])->middleware('isVenueOwner');
    Route::get('/venues/{slug}', [VenueController::class, 'show']);
    Route::post('/venues/{slug}/photos', [VenueController::class, 'attachPhotos'])->middleware('isVenueOwner');
    Route::post('/venues/{slug}/amenities', [VenueController::class, 'attachAmenities'])->middleware('isVenueOwner');
    Route::get('/venues/{slug}/events', [VenueController::class, 'events'])->middleware('isVenueOwner');
    Route::post('/venues', [VenueController::class, 'store'])->middleware('isVenueOwner');
    Route::put('/venues/{slug}', [VenueController::class, 'update'])->middleware('isVenueOwner');
});
    