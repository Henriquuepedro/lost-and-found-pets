<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UserSeeder::class);

        DB::table('admins')->insert([
            'name' => 'Pedro Henrique',
            'email' => 'admin@admin.com',
            'password' => Hash::make('12345678')
        ]);
    }
}
