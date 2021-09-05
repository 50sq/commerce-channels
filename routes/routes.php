<?php

use FiftySq\Commerce\Channels\Http\OrdersController;
use FiftySq\Commerce\Commerce;
use FiftySq\Commerce\Http\Controllers\CartController;
use FiftySq\Commerce\Http\Controllers\CheckoutController;
use FiftySq\Commerce\Http\Controllers\CheckoutSuccessController;
use FiftySq\Commerce\Http\Controllers\CustomerAddressesController;
use FiftySq\Commerce\Payments\Http\CheckoutSessionController;
use FiftySq\Commerce\Payments\Http\GatewayWebhookController;
use Illuminate\Support\Facades\Route;

Route::name('commerce.')->group(function () {
    Route::middleware(Commerce::middleware())
        ->group(function () {
            $enableViews = config('commerce.views', true);

            // Cart
            Route::prefix('cart')
                ->name('cart.')
                ->group(function () use ($enableViews) {
                    if ($enableViews) {
                        Route::get('/items', [CartController::class, 'index'])->name('index');
                    }

                    Route::put('/add-items', [CartController::class, 'addItems'])->name('add');
                    Route::put('/remove-items', [CartController::class, 'removeItems'])->name('remove');
                    Route::delete('/clear', [CartController::class, 'clearCart'])->name('clear');
                });

            // Addresses
            Route::prefix('addresses')
                ->name('addresses.')
                ->group(function () {
                    Route::post('/', [CustomerAddressesController::class, 'store'])->name('store');
                    Route::delete('/{address}', [CustomerAddressesController::class, 'destroy'])->name('delete');
                });

            // Checkout
            Route::prefix('checkout')
                ->name('checkout.')
                ->group(function () use ($enableViews) {
                    if ($enableViews) {
                        Route::put('/', [CheckoutController::class, 'update'])->name('update');
                        Route::get('/success', [CheckoutSuccessController::class, 'index'])->name('success');

                        // Checkout Sessions
                        Route::prefix('session')
                            ->name('session.')
                            ->group(function () use ($enableViews) {
                                Route::post('/', [CheckoutSessionController::class, 'store'])->name('store');
                                Route::get('/success', [CheckoutSessionController::class, 'success'])->name('success');
                                Route::get('/cancelled', [CheckoutSessionController::class, 'cancelled'])->name('cancelled');
                            });
                    }
                });

            // Orders
            Route::prefix('orders')
                ->name('orders.')
                ->group(function () {
                    Route::get('/{order}', [OrdersController::class, 'show'])->name('show');
                });
        });

    // Webhooks

    Route::middleware('api')->prefix('api')->group(function () {
        Route::post('webhooks/payments', [GatewayWebhookController::class, 'store'])->name('webhooks.payments');
    });
});
