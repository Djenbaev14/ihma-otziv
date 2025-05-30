<?php

use App\Models\Branch;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect('/admin');
});


Route::get('/download-qr/{branch}', function (Branch $branch) {
    $path = $branch->qr_code_path;

    if (!$path || !Storage::disk('public')->exists($path)) {
        abort(404);
    }

    return Storage::disk('public')->download($path, 'qr-code.png');
})->name('download.qr');