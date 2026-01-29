<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CashPayService;
use App\Models\Gateway;

class SyncSemoaGateways extends Command
{
    protected $signature = 'semoa:sync-gateways';
    protected $description = 'Synchronise les gateways Semoa';

    public function handle(CashPayService $cashpay)
    {
        $gateways = $cashpay->getGateways();

        foreach ($gateways as $g) {
            Gateway::updateOrCreate(
                ['semoa_id' => $g['id']],
                [
                    'reference' => $g['reference'],
                    'libelle' => $g['libelle'],
                    'psp' => $g['psp']['libelle'] ?? null,
                    'psp_logo' => $g['psp']['logo_url'] ?? null,
                    'methode' => $g['methode'],
                    'logo_url' => $g['logo_url'],
                    'actif' => true,
                ]
            );
        }

        $this->info('Gateways synchronisés avec succès.');
    }
}

