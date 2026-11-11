<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    private array $tables = ['instance_l_s', 'instances', 'project_b2_b_s'];

    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->string('delai')->nullable()->change();
            });

            $this->normalizeDeadlineValues($tableName);

            Schema::table($tableName, function (Blueprint $table) {
                $table->date('delai')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->string('delai')->nullable()->change();
            });

            foreach (DB::table($tableName)->whereNotNull('delai')->get(['id', 'delai']) as $row) {
                DB::table($tableName)->where('id', $row->id)->update([
                    'delai' => Carbon::parse($row->delai)->format('d/m/Y'),
                ]);
            }
        }

        DB::table('project_b2_b_s')->whereNull('delai')->update(['delai' => '']);
        Schema::table('project_b2_b_s', function (Blueprint $table) {
            $table->string('delai')->nullable(false)->change();
        });
    }

    private function normalizeDeadlineValues(string $tableName): void
    {
        DB::table($tableName)->where('delai', '')->update(['delai' => null]);

        foreach (DB::table($tableName)->whereNotNull('delai')->get(['id', 'delai']) as $row) {
            $value = trim((string) $row->delai);
            $normalized = null;

            foreach (['Y-m-d', 'd/m/Y'] as $format) {
                try {
                    $date = Carbon::createFromFormat('!'.$format, $value);
                } catch (Throwable) {
                    $date = null;
                }

                if ($date && $date->format($format) === $value) {
                    $normalized = $date->toDateString();
                    break;
                }
            }

            if ($normalized === null) {
                throw new RuntimeException("Invalid deadline value in {$tableName}: {$value}");
            }

            DB::table($tableName)->where('id', $row->id)->update(['delai' => $normalized]);
        }
    }
};
