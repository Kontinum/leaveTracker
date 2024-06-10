<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

//        User::factory()->create([
//            'name' => 'Test User',
//            'email' => 'test@example.com',
//        ]);

        DB::table('user_types')->insert([
            [
                'type' => 'admin',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'type' => 'manager',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'type' => 'regular',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        DB::table('leave_types')->insert([
            [
                'type' => 'annual',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'type' => 'day_off',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);

        DB::table('leave_statuses')->insert([
            [
                'status' => 'approved',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'status' => 'rejected',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'status' => 'day_off',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}
