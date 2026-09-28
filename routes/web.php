<?php

use App\Http\Controllers\VideoClipController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

Route::get('/', [VideoClipController::class, 'index'])
    ->name('clipper.index');

Route::post('/clip', [VideoClipController::class, 'store'])
    ->name('clipper.store');

Route::get('/clip/status/{token}', [VideoClipController::class, 'status'])
    ->name('clipper.status');

Route::get('/clip/download/{token}', [VideoClipController::class, 'download'])
    ->name('clipper.download');

Route::get('/shutdown', function () {
    $runtimeDir = storage_path('app/private/ytclip-runtime');

    if (!is_dir($runtimeDir)) {
        mkdir($runtimeDir, 0755, true);
    }

    $path = $runtimeDir . DIRECTORY_SEPARATOR . 'stop.flag';

    file_put_contents($path, 'stop');

    return response()->view('shutdown');
});
