<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Ambang Batas Stok Menipis
    |--------------------------------------------------------------------------
    |
    | Digunakan oleh fitur notifikasi stok menipis pada Dashboard.
    | Jika stok barang <= nilai ini, barang akan ditandai sebagai "stok menipis".
    |
    */
    'low_stock_threshold' => env('LOW_STOCK_THRESHOLD', 5),
];
