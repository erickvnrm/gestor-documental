<?php

use App\Http\Controllers\ActosController;
use App\Models\Actos;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;

Route::get('/', function () {
    return redirect(route('filament.admin.auth.login'));
});

Route::get('/login', function () {
    return redirect(route('filament.admin.auth.login'));
})->name('login');

Route::get('/get-next-number', [ActosController::class, 'getNextNumber'])->name('get-next-number');

Route::get('/actos/download/{acto}', function (Actos $acto) {
    $acto = Actos::withTrashed()->find($acto->id);

    if (!$acto || !$acto->archivo_url || !Storage::disk('public')->exists($acto->archivo_url)) {
        return abort(404);
    }

    $filePath = $acto->archivo_url;
    $fileName = $acto->getDownloadFileName();

    return Storage::disk('public')->download($filePath, $fileName);
})->name('actos.download');




