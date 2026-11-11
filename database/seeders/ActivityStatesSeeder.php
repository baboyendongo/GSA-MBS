<?php

namespace Database\Seeders;

use App\Models\ActivityState;
use Illuminate\Database\Seeder;

class ActivityStatesSeeder extends Seeder
{
    public function run(): void
    {
        foreach (config('activity_states.types', []) as $trackingType => $definition) {
            foreach ($definition['initial'] as $sortOrder => $label) {
                ActivityState::firstOrCreate(
                    [
                        'tracking_type' => $trackingType,
                        'label' => $label,
                    ],
                    [
                        'is_active' => true,
                        'sort_order' => $sortOrder,
                    ]
                );
            }
        }
    }
}
