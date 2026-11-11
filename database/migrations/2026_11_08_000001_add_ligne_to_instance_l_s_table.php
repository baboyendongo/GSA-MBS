<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('instance_l_s', function (Blueprint $table) {
            $table->string('ligne')->nullable()->after('operation');
        });
    }

    public function down(): void
    {
        Schema::table('instance_l_s', function (Blueprint $table) {
            $table->dropColumn('ligne');
        });
    }
};