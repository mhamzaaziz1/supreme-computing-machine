<?php

/*
|--------------------------------------------------------------------------
| Field operations endpoints
|--------------------------------------------------------------------------
|
| JSON behind the drawers, modals and popovers. Loaded from web.php inside
| the authenticated group under the /ops prefix, so every route here already
| has the session, business and permission middleware the rest of the app
| runs on.
|
*/

use App\Http\Controllers\Ops;
use Illuminate\Support\Facades\Route;

Route::get('outlets/{id}', [Ops\OutletController::class, 'show'])->whereNumber('id')->name('outlets.show');
Route::patch('outlets/{id}/credit', [Ops\OutletController::class, 'updateCredit'])->whereNumber('id')->name('outlets.credit');

// Geofence check-in
Route::post('checkin', [Ops\CheckInController::class, 'store'])->name('checkin');

// Credit hold
Route::post('credit/check', [Ops\CreditController::class, 'check'])->name('credit.check');
Route::post('credit/override', [Ops\CreditController::class, 'override'])->name('credit.override');
Route::post('credit/request', [Ops\CreditController::class, 'request'])->name('credit.request');

// Approvals inbox
Route::get('approvals', [Ops\ApprovalController::class, 'index'])->name('approvals.index');
Route::get('approvals/count', [Ops\ApprovalController::class, 'count'])->name('approvals.count');
Route::get('approvals/{id}', [Ops\ApprovalController::class, 'status'])->whereNumber('id')->name('approvals.status');
Route::post('approvals/{id}/decide', [Ops\ApprovalController::class, 'decide'])->whereNumber('id')->name('approvals.decide');

// Collections and the post-dated cheque register
Route::get('outlets/{id}/collect', [Ops\CollectController::class, 'openItems'])->whereNumber('id')->name('collect.items');
Route::post('collect', [Ops\CollectController::class, 'store'])->name('collect.store');
Route::get('cheques', [Ops\ChequeController::class, 'index'])->name('cheques.index');
Route::post('cheques/{id}/status', [Ops\ChequeController::class, 'updateStatus'])->whereNumber('id')->name('cheques.status');
