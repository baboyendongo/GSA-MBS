<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_b2_b_s', function (Blueprint $table) {
            $table->date('date_cloture')->nullable()->change();
            $table->string('observation')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('project_b2_b_s', function (Blueprint $table) {
            $table->date('date_cloture')->nullable(false)->change();
            $table->string('observation')->nullable(false)->change();
        });
    }
};
