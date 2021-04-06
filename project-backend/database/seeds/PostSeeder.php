<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Carbon\Carbon;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        for($i = 0;$i<25;$i++) {
            DB::table('posts')->insert([
                'image' => "default.png",
                'title' => $faker->jobTitle,
                'description' => $faker->paragraphs($nb = 2, $asText = true)  ,
                'author' => $faker->firstName ,
                "status" => $faker->randomElement(["active","drafted"]),
                "category_id" => $faker->randomElement(['1', '2', '3', '4', '5']),
                "created_time" => Carbon::now()->format("Y-m-d  "),
                "status_created" => $faker->randomElement(["new",null,"blocked"])
            ]);
        }
    }
}
