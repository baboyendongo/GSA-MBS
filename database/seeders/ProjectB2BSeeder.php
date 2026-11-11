<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProjectB2BSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['2026-10-21', 'CRM B2B', 'Renseignement document de cadrage', 'MBS', 'En cours', '2026-10-23', null, null],
            ['2026-10-20', 'SD-WAN', 'Avancement commande Fortinet/Exclusive Network', 'B2B SI', 'En cours', null, null, 'En attente de paiement'],
            ['2026-10-20', 'SD-WAN', 'Proposition Fortinet (Formation, FortiManager et Boitiers SFP)', 'B2B SI', 'En cours', null, null, 'En attente retour Casanet'],
            ['2026-10-20', 'SD-WAN', 'Séance avec Fortinet, tarification et pricing offres', 'B2B SI', 'En cours', '2026-10-22', null, null],
            ['2026-10-20', 'Geo Localisation', 'Réunion avec TGTE', 'MBS Comm', 'Achevée', null, '2026-10-20', 'Une réunion de présentation de la solution sera planifiée'],
            ['2026-10-19', 'UC', 'Demande de POC solution Grandstream/GoIT', 'MBS Tech', 'En cours', null, null, 'En attente retour GoIT'],
            ['2026-10-19', 'Geo Localisation', 'Présentation solution MauriTrack', 'MBS Tech', 'En cours', null, null, null],
            ['2026-10-19', 'SD-WAN', 'POC SD-WAN pour cas P2P / Internet', 'B2B Tech / DSI', 'En cours', null, null, 'Agence GC est sélectionnée pour le POC'],
            ['2026-10-15', 'SD-WAN', 'POC MSSP Casanet', 'MBS Tech', 'En cours', null, null, 'En attente de retour Casanet'],
            ['2026-10-15', 'SD-WAN', 'Proposition Casanet (Formation, FortiManager et Boitiers SFP)', 'MBS Tech', 'En cours', null, null, 'En attente de retour Casanet'],
            ['2026-10-15', 'SD-WAN', 'Point avec Fortinet', 'B2B SI', 'Achevée', null, '2026-10-20', null],
            ['2026-10-14', 'CRM B2B', 'Deuxième séance pour analyse du besoin', 'B2B SI', 'Achevée', null, '2026-10-21', null],
            ['2026-10-12', 'CRM B2B', 'Première séance pour analyse du besoin', 'B2B SI', 'Achevée', null, '2026-10-14', null],
            ['2026-10-08', 'SD-WAN', 'POC SD-WAN Casanet', 'MBS Tech', 'Achevée', null, '2026-10-12', "POC concluant pour le cas d'un mono site pour accès internet"],
            ['2026-10-08', 'UC', 'POC UC solution Yeastar/Moon', 'MBS Tech', 'En cours', null, null, 'Agence GC est sélectionnée pour le POC'],
            ['2026-10-08', 'UC', 'Présentation solution UC Grandstream avec partenaire GoIT', 'MBS Tech', 'Achevée', null, '2026-10-15', null],
            ['2026-10-02', 'UC', 'Présentation solution UC Yeastar avec partenaire Moon', 'MBS Tech', 'Achevée', null, '2026-10-08', null],
        ];

        foreach ($rows as [$date, $project, $action, $responsable, $etat, $delai, $dateCloture, $observation]) {
            DB::table('project_b2_b_s')->updateOrInsert(
                [
                    'date' => $date,
                    'project' => $project,
                    'Action' => $action,
                ],
                [
                    'client' => '',
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