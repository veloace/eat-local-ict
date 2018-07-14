<?php

use Illuminate\Database\Seeder;

class TagsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('tags')->insert([
            [
                'name'=>'burgers'
            ],
            [
                'name'=>'donuts'
            ],
            [
                'name'=>'coffee'
            ],
            [
                'name'=>'vegan'
            ],
            [
                'name'=>'alcohol'
            ],
            [
                'name'=>'taco'
            ],
            [
                'name'=>'mexican'
            ],
            [
                'name'=>'Italian'
            ],
            [
                'name'=>'pizza'
            ],
            [
                'name'=>'Japanese'
            ],
            [
                'name'=>'Asian'
            ],
            [
                'name'=>'Chinese'
            ],
            [
                'name'=>'Vietnamese'
            ],
            [
                'name'=>'German'
            ],
            [
                'name'=>'French'
            ],
            [
                'ice cream'
            ],
            [
                'milkshake'
            ],
            [
                'shaved ice'
            ],
            [
                'cakes'
            ]

        ]);
    }
}
