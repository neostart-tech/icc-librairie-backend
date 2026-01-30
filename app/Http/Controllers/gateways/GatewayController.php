<?php

namespace App\Http\Controllers\gateways;

use App\Http\Controllers\Controller;
use App\Http\Resources\GatewayResource;
use App\Models\Gateway;
use App\Services\CashPayService;
use Carbon\Carbon;

class GatewayController extends Controller
{
    public function index(CashPayService $cashPay)
    {
        $lastSync = Gateway::max('updated_at');

        if (
            Gateway::count() === 0 ||
            !$lastSync ||
            Carbon::parse($lastSync)->diffInHours(now()) >= 24
        ) {
            $cashPay->syncGateways();
        }

        return GatewayResource::collection(
            Gateway::where('actif', true)->get()
        );
    }
}
