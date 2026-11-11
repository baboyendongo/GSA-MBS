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
        Schema::create('project_b2_b_s', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('project');
            $table->string('client');
            $table->string('Action');
            $table->string('responsable');
            $table->string('etat');
            $table->string('delai');
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
        Schema::dropIfExists('project_b2_b_s');
    }
};
