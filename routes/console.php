<?php

use App\Models\Borrowing;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    Borrowing::where('status', Borrowing::STATUS_DIPINJAM)
        ->whereNotNull('due_date')
        ->whereDate('due_date', '<', now())
        ->update(['status' => Borrowing::STATUS_TERLAMBAT]);
})->daily()->name('mark-overdue-borrowings');
