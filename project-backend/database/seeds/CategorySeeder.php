<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        $array = ["Csgo","Medal of Honor","Dota2","Call of Duty","Minecraft"];
        for($i = 0;$i<5;$i++){
            DB::table('categories')->insert([
                'category_name' => $array[$i]
            ]);
        }

    }
}
