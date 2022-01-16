<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        Role::insert(
            [
                [
                    'name' => 'user',
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],
                [
                    'name' => 'admin',
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ]
            ]);

        User::insert(
            [
                [
                    'name' => 'ahmed raed siam',
                    'email' => 'ahmedraedsiam@hotmail.com',
                    'password' => Hash::make('123456789++'),
                    'phone_number' => '+970599853199',
                    'avatar_url' => '/ahmed_raed_siam/me.jpg',
                    'role_id' => 2,
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],
                [
                    'name' => 'rinodo7371',
                    'email' => 'rinodo7371@wiicheat.com',
                    'password' => Hash::make('rinodo7371@wiicheat.com'),
                    'phone_number' => '+9705996857199',
                    'avatar_url' => '',
                    'role_id' => 1,
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],
            ]);

        $this->call([
            UserSeeder::class,
            ServiceSeeder::class,
            ServiceTypeSeeder::class,
            ProductSeeder::class,
            PhysicalActivitySeeder::class,
        ]);

    }
}
