<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Frontend Only (Blade + Alpine.js + GSAP)
|--------------------------------------------------------------------------
*/

// Public routes
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/logout', function () {
    // Frontend-only logout - just redirect
    return redirect()->route('login');
})->name('logout');

// Protected routes (frontend auth via Alpine store)
Route::middleware('web')->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Facturación
    Route::prefix('facturacion')->name('facturacion.')->group(function () {
        Route::get('/', function () {
            return view('facturacion.index');
        })->name('index');

        Route::get('/create', function () {
            return view('facturacion.create');
        })->name('create');

        Route::get('/{id}', function ($id) {
            return view('facturacion.show', ['id' => $id]);
        })->name('show');

        Route::get('/{id}/edit', function ($id) {
            return view('facturacion.edit', ['id' => $id]);
        })->name('edit');
    });

    // Inventario
    Route::prefix('inventario')->name('inventario.')->group(function () {
        Route::get('/', function () {
            return view('inventario.index');
        })->name('index');

        Route::get('/create', function () {
            return view('inventario.create');
        })->name('create');

        Route::get('/{id}', function ($id) {
            return view('inventario.show', ['id' => $id]);
        })->name('show');

        Route::get('/{id}/edit', function ($id) {
            return view('inventario.edit', ['id' => $id]);
        })->name('edit');
    });

    // Vehículos
    Route::prefix('vehiculos')->name('vehiculos.')->group(function () {
        Route::get('/', function () {
            return view('vehiculos.index');
        })->name('index');

        Route::get('/entrada', function () {
            return view('vehiculos.entrada');
        })->name('entrada');

        Route::get('/salida/{placa}', function ($placa) {
            return view('vehiculos.salida', ['placa' => $placa]);
        })->name('salida');

        Route::get('/{id}', function ($id) {
            return view('vehiculos.show', ['id' => $id]);
        })->name('show');
    });

    // Lavados
    Route::prefix('lavados')->name('lavados.')->group(function () {
        Route::get('/', function () {
            return view('lavados.index');
        })->name('index');

        Route::get('/create', function () {
            return view('lavados.create');
        })->name('create');

        Route::get('/{id}', function ($id) {
            return view('lavados.show', ['id' => $id]);
        })->name('show');

        Route::get('/{id}/edit', function ($id) {
            return view('lavados.edit', ['id' => $id]);
        })->name('edit');
    });

    // Auditoría
    Route::prefix('auditoria')->name('auditoria.')->group(function () {
        Route::get('/', function () {
            return view('auditoria.index');
        })->name('index');

        Route::get('/{id}', function ($id) {
            return view('auditoria.show', ['id' => $id]);
        })->name('show');
    });

    // Seguridad (Admin only)
    Route::prefix('seguridad')->name('seguridad.')->group(function () {
        Route::get('/', function () {
            return view('seguridad.index');
        })->name('index');

        Route::get('/create', function () {
            return view('seguridad.create');
        })->name('create');

        Route::get('/{id}/edit', function ($id) {
            return view('seguridad.edit', ['id' => $id]);
        })->name('edit');
    });

    // Perfil y Ajustes
    Route::get('/perfil', function () {
        return view('perfil');
    })->name('perfil');

    Route::get('/ajustes', function () {
        return view('ajustes');
    })->name('ajustes');
});

// API routes for frontend data (optional - for future backend integration)
Route::prefix('api')->group(function () {
    Route::get('/facturas', function () {
        return response()->json([]);
    });

    Route::get('/vehiculos', function () {
        return response()->json([]);
    });

    Route::get('/lavados', function () {
        return response()->json([]);
    });

    Route::get('/inventario', function () {
        return response()->json([]);
    });

    Route::get('/auditoria', function () {
        return response()->json([]);
    });
});
