<?php

namespace App\Http\Controllers\Inventaris;

class InventarisGudangController extends BaseSubsystemInventarisController
{
    protected string $lokasi = 'Gudang';
    protected string $viewPrefix = 'gudang';
    protected string $routePrefix = 'administrasi.inventaris.gudang';
}
