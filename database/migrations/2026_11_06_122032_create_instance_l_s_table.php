<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('instance_l_s', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('evenement');
            $table->string('operation');
            $table->string('codeclient')->nullable();
            $table->string('clients')->nullable();
            $table->string('ville')->nullable();
            $table->string('phase')->nullable();
            $table->string('responsable')->nullable();
            $table->string('delai')->nullable();
            $table->date('date_cloture')->nullable();
            $table->string('observation')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instance_l_s');
    }
};
