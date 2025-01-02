<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Invoices\Presentation\Http\InvoiceController;

Route::prefix('invoices')->group(function () {
    Route::get('{id}', [InvoiceController::class, 'view'])->name('invoices.view');
    Route::post('/', [InvoiceController::class, 'create'])->name('invoices.create');
    Route::post('{id}/send', [InvoiceController::class, 'send'])->name('invoices.send');
});
