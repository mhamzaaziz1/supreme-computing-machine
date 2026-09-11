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
Route::get('outlets/{id}/pattern', [Ops\OutletController::class, 'pattern'])->whereNumber('id')->name('outlets.pattern');

// Field app: queued offline actions arriving
Route::post('field/sync', [Ops\FieldSyncController::class, 'sync'])->name('field.sync');

// Ctrl-K record search
Route::get('search', [Ops\SearchController::class, 'index'])->name('search');

// At the outlet: visit, damage, used oil, returns
Route::get('outlets/{id}/visit', [Ops\VisitController::class, 'form'])->whereNumber('id')->name('visits.form');
Route::post('visits', [Ops\VisitController::class, 'store'])->name('visits.store');
Route::post('visits/report', [Ops\VisitController::class, 'report'])->name('visits.report');
Route::post('visits/return', [Ops\VisitController::class, 'requestReturn'])->name('visits.return');

// Oil-change bay: due list, vehicle history, owner reminders
Route::get('service-due', [Ops\ServiceDueController::class, 'index'])->name('service.due');
Route::post('service-due/remind', [Ops\ServiceDueController::class, 'remind'])->name('service.remind');
Route::get('vehicles/{id}', [Ops\ServiceDueController::class, 'vehicle'])->whereNumber('id')->name('vehicles.show');

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

// Trade schemes
Route::get('schemes', [Ops\SchemeController::class, 'index'])->name('schemes.index');
Route::post('schemes', [Ops\SchemeController::class, 'store'])->name('schemes.store');
Route::post('schemes/evaluate', [Ops\SchemeController::class, 'evaluate'])->name('schemes.evaluate');
Route::put('schemes/{id}', [Ops\SchemeController::class, 'update'])->whereNumber('id')->name('schemes.update');
Route::post('schemes/{id}/toggle', [Ops\SchemeController::class, 'toggle'])->whereNumber('id')->name('schemes.toggle');
Route::delete('schemes/{id}', [Ops\SchemeController::class, 'destroy'])->whereNumber('id')->name('schemes.destroy');

// Routes: day plan, rules, live map, cost to serve
Route::get('routes/map', [Ops\RouteController::class, 'map'])->name('routes.map');
Route::get('routes/economics', [Ops\RouteEconomicsController::class, 'index'])->name('routes.economics');
Route::get('routes/{id}/plan', [Ops\RouteController::class, 'plan'])->whereNumber('id')->name('routes.plan');
Route::put('routes/{id}/sequence', [Ops\RouteController::class, 'sequence'])->whereNumber('id')->name('routes.sequence');
Route::patch('routes/{id}/rules', [Ops\RouteController::class, 'rules'])->whereNumber('id')->name('routes.rules');

// Principal: monthly secondary-sales file, targets, pack sizes
Route::get('principal', [Ops\PrincipalController::class, 'index'])->name('principal.index');
Route::get('principal/export', [Ops\PrincipalController::class, 'export'])->name('principal.export');
Route::post('principal/targets', [Ops\PrincipalController::class, 'storeTarget'])->name('principal.targets.store');
Route::delete('principal/targets/{id}', [Ops\PrincipalController::class, 'destroyTarget'])->whereNumber('id')->name('principal.targets.destroy');
Route::patch('products/{id}/pack', [Ops\PrincipalController::class, 'packSize'])->whereNumber('id')->name('products.pack');

// Vans: load out in the morning, settle back at night
Route::get('vans', [Ops\VanController::class, 'index'])->name('vans.index');
Route::get('vans/{id}/load', [Ops\VanController::class, 'loadForm'])->whereNumber('id')->name('vans.loadForm');
Route::post('vans/{id}/load', [Ops\VanController::class, 'load'])->whereNumber('id')->name('vans.load');
Route::get('vans/{id}/settle', [Ops\VanController::class, 'settleForm'])->whereNumber('id')->name('vans.settleForm');
Route::post('vans/{id}/settle', [Ops\VanController::class, 'settle'])->whereNumber('id')->name('vans.settle');
Route::get('vans/settlements/{id}/slip', [Ops\VanController::class, 'slip'])->whereNumber('id')->name('vans.slip');
