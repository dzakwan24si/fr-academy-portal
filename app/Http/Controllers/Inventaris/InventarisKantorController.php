<?php

namespace App\Http\Controllers\Inventaris;

class InventarisKantorController extends BaseSubsystemInventarisController
{
    protected string $lokasi = 'Kantor';
    protected string $viewPrefix = 'kantor';
    protected string $routePrefix = 'administrasi.inventaris.kantor';
}
