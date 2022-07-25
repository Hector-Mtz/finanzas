<?php

use App\Http\Controllers\CecoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\OcController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\TipoController;
use App\Http\Controllers\VentaController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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
    return Inertia::render('Auth/Login', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});



Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::apiResource('/ventas', VentaController::class)->except('destroy', 'show');
    Route::apiResource('/ocs', OcController::class)->except('destroy', 'show');
    Route::get('/ocs/catalogos', [OcController::class, "catalogos"])->name("ocs.catalogos");


    Route::apiResource('/facturas', FacturaController::class)->except('destroy', 'show');
    Route::post('/facturas/{factura}/ocs', [FacturaController::class, "storeOc"])->name("facturas.ocs.store");
    Route::delete('/facturas/{factura}/ocs', [FacturaController::class, "destroyOc"])->name("facturas.ocs.destroy");


    Route::get('/cecos/catalogo', [CecoController::class, 'catalogo'])->name('cecos.catalogo');
    Route::get('/servicios/catalogo', [ServicioController::class, 'catalogo'])->name('servicios.catalogo');
    Route::get('/tipos/catalogo', [TipoController::class, 'catalogo'])->name('tipos.catalogo');
});
Route::apiResource('/clientes', ClienteController::class);
