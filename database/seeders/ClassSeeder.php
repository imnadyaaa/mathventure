<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SchoolClass;

class ClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SchoolClass::create([
            'name' => 'Kelas X',
        ]);

        SchoolClass::create([
            'name' => 'Kelas XI',
        ]);

        SchoolClass::create([
            'name' => 'Kelas XII',
        ]);
    }
}