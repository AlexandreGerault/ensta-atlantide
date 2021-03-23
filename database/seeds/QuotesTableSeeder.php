<?php

use Illuminate\Database\Seeder;

class QuotesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('quotes')->insert([
            [
                'content' => "Quand un poulpe est retiré de sa coquille, une infinité de petites pierres s'attachent à ses bras.",
                'author' => 'Jean Racine',
				'creator_id' => 1,
            ],
        ]);
    }
}
