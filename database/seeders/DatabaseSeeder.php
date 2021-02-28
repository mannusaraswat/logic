<?php

namespace Database\Seeders;

use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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
        $faker = Faker::create();
        // creating product seed
        foreach (range(1, 10) as $index) {
            DB::table('products')->insert([
                'name' => $faker->company,
                'description' => $faker->text
            ]);
        }

        // creating user seed with product id
        foreach (range(1, 10) as $index) {
            DB::table('users')->insert([
                'name' => $faker->name,
                'email' => $faker->email,
                'password' => Hash::make('password'),
                'product_id' => rand(1, 10),
            ]);
        }

    }
}
