<?php

use Illuminate\Database\Seeder;
use Faker\Factory As Faker;
class CommentReplySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker= Faker::create();
        for($i = 0;$i < 20;$i++){
            DB::table("comments_replies")->insert([
               "comment_reply" => $faker->paragraphs($nb = 1, $asText = true),
                "comment_reply_author" => $faker->firstName(),
                "comment_id" => random_int(1,24)

            ]);
        }
    }
}
