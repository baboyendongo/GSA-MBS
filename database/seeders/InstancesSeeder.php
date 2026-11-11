<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InstancesSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['2026-11-04', 'Changement sw Kiffa', 'SNDE', null, 'MBS Tech', null, null, null, null],
            ['2026-11-04', 'DRG FTTH', 'SNDE', '011791', 'MBS Tech', null, null, null, null],
            ['2026-11-04', 'DRG FTTH', 'BNM', '005087', 'MBS Tech', null, null, null, null],
            ['2026-11-02', 'Réclamation LS CNC', 'CNC', null, 'Sidi Babe', null, null, null, null],
            ['2026-10-28', 'SIP IBM', 'IBM', null, 'Hacen', null, null, '2026-11-02', "* Reste numéro à router NGN\n* Service rétabli après inter client, 04/11\n* Connectivité OK, attente retour client, 31/10"],
            ['2026-11-02', 'Installation LLI BMCI', 'BMCI', '10443', 'Sidi Babe', null, null, null, null],
            ['2026-10-28', "UNFPA demande d'informations SIP", 'UNFPA', null, 'MBS Tech', null, null, null, 'DID, SIP'],
            ['2026-10-27', "Demande d'offre LS MPLS Agence Université", 'BNM', null, 'MBS Comm', null, null, null, 'LS P2P de 2Mbps'],
            ['2026-10-30', 'Réunion à planifier', 'UN', null, 'MBS Tech', null, null, null, null],
            ['2026-10-26', 'Upgrade et migration vers FTTR', 'Ecole Iman', null, 'MBS Tech', null, null, null, null],
            ['2026-10-26', 'Demande switch double alimentation', 'Huawei', null, 'MBS Tech', null, null, null, null],
            ['2026-10-20', "Lignes FTTH à l'intérieur", 'BMCI', null, 'MBS Tech', null, null, null, null],
        ];

        foreach ($rows as [$date, $instance, $client, $ligne, $responsable, $etat, $delai, $dateCloture, $observation]) {
            DB::table('instances')->updateOrInsert(
                [
                    'date' => $date,
                    'instance' => $instance,
                    'client' => $client,
                    'ligne' => $ligne,
                ],
                [
                    'responsable' => $responsable,
                    'etat' => $etat,
                    'delai' => $delai,
                    'date_cloture' => $dateCloture,
                    'observation' => $observation,
                ]
            );
        }
    }
}