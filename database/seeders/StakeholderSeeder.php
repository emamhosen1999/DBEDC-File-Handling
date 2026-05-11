<?php

namespace Database\Seeders;

use App\Models\Stakeholder;
use Illuminate\Database\Seeder;

class StakeholderSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            ['code' => 'IE',  'name' => 'Implementing Entity',  'color' => '#6366F1'],
            ['code' => 'JV',  'name' => 'Joint Venture',         'color' => '#10B981'],
            ['code' => 'RHD', 'name' => 'Roads & Highways Dept', 'color' => '#F59E0B'],
            ['code' => 'GOV', 'name' => 'Government Agency',     'color' => '#EF4444'],
            ['code' => 'DON', 'name' => 'Donor Agency',          'color' => '#8B5CF6'],
            ['code' => 'INT', 'name' => 'Internal',              'color' => '#6B7280'],
        ];

        foreach ($defaults as $d) {
            Stakeholder::firstOrCreate(['code' => $d['code']], [
                'name' => $d['name'],
                'color' => $d['color'],
                'is_active' => true,
            ]);
        }
    }
}
