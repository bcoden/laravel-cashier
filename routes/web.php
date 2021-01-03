<?php

use Illuminate\Http\Request;
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

Route::middleware(['auth:sanctum', 'verified'])->group(function() {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/charge', function () {
        return view('charge');
    })->name('charge');

    Route::post('/charge', function(Request $request) {
        if (isset($request['paymentMethod'])) {
            auth()->user()->createAsStripeCustomer();
            auth()->user()->updateDefaultPaymentMethod($request['paymentMethod']);
            auth()->user()->invoiceFor('One time charge', 1000);
        }
        return redirect('/dashboard');
    })->name('charge.post');

    Route::get('/invoices', function() {
        return view('invoices', [
            'invoices' => auth()->user()->invoices()
        ]);
    })->name('invoices');

    Route::get('/user/invoice/{invoice}', function (Request $request, $invoiceId) {
        return $request->user()->downloadInvoice($invoiceId, [
            'vendor' => config('store.name'),
            'product' => config('store.description'),
        ]);
    });
});

// subscription routes
Route::middleware(['auth:sanctum', 'verified', 'subscriber'])->group(function() {
    Route::get('/subscribe', function () {
        return view('subscribe', [
            'intent' => auth()->user()->createSetupIntent(),
        ]);
    })->name('subscribe');

    Route::post('/subscribe', function(Request $request) {
        auth()->user()->newSubscription('cashier', $request->plan)->create($request->paymentMethod);
        return redirect('/dashboard');
    })->name('subscribe.post');

});


// members route
Route::middleware(['auth:sanctum', 'verified', 'subscribe'])->get('/members', function (Request $request) {
    return view('members');
})->name('members');
