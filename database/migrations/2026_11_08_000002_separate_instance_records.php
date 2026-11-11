<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('instances', function (Blueprint $table) {
            $table->string('ligne')->nullable()->change();
            $table->string('etat')->nullable()->change();
            $table->string('delai')->nullable()->change();
            $table->date('date_cloture')->nullable()->change();
            $table->text('observation')->nullable()->change();
        });

        $instanceNames = [
            'Changement sw Kiffa',
            'DRG FTTH',
            'Réclamation LS CNC',
            'SIP IBM',
            'Installation LLI BMCI',
            "UNFPA demande d'informations SIP",
            "Demande d'offre LS MPLS Agence Université",
            'Réunion à planifier',
            'Upgrade et migration vers FTTR',
            'Demande switch double alimentation',
            "Lignes FTTH à l'intérieur",
        ];

        $records = DB::table('instance_l_s')
            ->whereBetween('date', ['2026-10-20', '2026-11-04'])
            ->whereIn('evenement', $instanceNames)
            ->where('operation', '')
            ->whereNull('codeclient')
            ->whereNull('ville')
            ->get();

        foreach ($records as $record) {
            DB::table('instances')->updateOrInsert(
                [
                    'date' => $record->date,
                    'instance' => $record->evenement,
                    'client' => $record->clients,
                    'ligne' => $record->ligne,
                ],
                [
                    'responsable' => $record->responsable,
                    'etat' => $record->phase,
                    'delai' => $record->delai,
                    'date_cloture' => $record->date_cloture,
                    'observation' => $record->observation,
                ]
            );
        }

        if ($records->isNotEmpty()) {
            DB::table('instance_l_s')->whereIn('id', $records->pluck('id'))->delete();
        }
    }

    public function down(): void
    {
    }
};
