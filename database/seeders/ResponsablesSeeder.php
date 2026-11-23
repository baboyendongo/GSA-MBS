<?php

namespace Database\Seeders;

use App\Models\Responsable;
use Illuminate\Database\Seeder;

class ResponsablesSeeder extends Seeder
{
    public function run(): void
    {
        foreach (config('responsables.mbs', []) as $sortOrder => $name) {
            Responsable::firstOrCreate(
                ['name' => $name],
                ['is_active' => true, 'sort_order' => $sortOrder]
            );
        }
    }
}
