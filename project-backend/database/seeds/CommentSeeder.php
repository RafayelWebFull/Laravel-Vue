<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
class CommentSeeder extends Seeder
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
            DB::table('comments')->insert([
                    "comment_author" => $faker->firstName(),
                "comment" => $faker->paragraphs($nb = 1, $asText = true),
                "post_id" => random_int(1,25)
            ]);
                }
    }
}
