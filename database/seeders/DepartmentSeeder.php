<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $roots = [
            ['name' => 'Executive Office', 'display_order' => 0],
            ['name' => 'Operations', 'display_order' => 10],
            ['name' => 'Finance & Accounts', 'display_order' => 20],
            ['name' => 'Human Resources', 'display_order' => 30],
            ['name' => 'Information Technology', 'display_order' => 40],
        ];

        foreach ($roots as $r) {
            Department::firstOrCreate(['name' => $r['name']], [
                'is_active' => true,
                'display_order' => $r['display_order'],
            ]);
        }

        $ops = Department::where('name', 'Operations')->first();
        if ($ops) {
            foreach ([
                ['name' => 'Field Operations', 'display_order' => 0],
                ['name' => 'Logistics', 'display_order' => 10],
            ] as $child) {
                Department::firstOrCreate(
                    ['name' => $child['name']],
                    ['parent_id' => $ops->id, 'is_active' => true, 'display_order' => $child['display_order']],
                );
            }
        }
    }
}
