<?php

use Illuminate\Database\Seeder;

class PlaceTagsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('place_tags')->insert([
            [
                'place_id'=>1,
                'tag_id'=>16
            ],            [
                'place_id'=>1,
                'tag_id'=>17
            ],            [
                'place_id'=>1,
                'tag_id'=>19
            ],           [
                'place_id'=>2,
                'tag_id'=>2
            ],
                      [
                'place_id'=>2,
                'tag_id'=>4
            ],
            [
                'place_id'=>3,
                'tag_id'=>5
            ],
            [
                'place_id'=>3,
                'tag_id'=>6
            ],
            [
                'place_id'=>3,
                'tag_id'=>7
            ],
        ]);
    }
}
