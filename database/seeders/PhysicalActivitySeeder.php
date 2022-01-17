<?php

namespace Database\Seeders;

use App\Models\PhysicalActivity;
use Illuminate\Database\Seeder;

class PhysicalActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PhysicalActivity::factory(10)->create();
    }
}
