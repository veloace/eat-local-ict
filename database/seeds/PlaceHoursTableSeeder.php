<?php

use Illuminate\Database\Seeder;

class PlaceHoursTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('place_hours')->insert([
            [//scoop
                'place_id'=>1,
                'day_of_week'=>0,
                'open_time'=>'11:00:00',
                'close_time'=>'20:00:00'
            ],            [
                'place_id'=>1,
                'day_of_week'=>1,
                'open_time'=>'11:00:00',
                'close_time'=>'20:00:00'
            ],            [
                'place_id'=>1,
                'day_of_week'=>2,
                'open_time'=>'11:00:00',
                'close_time'=>'20:00:00'
            ],            [
                'place_id'=>1,
                'day_of_week'=>3,
                'open_time'=>'11:00:00',
                'close_time'=>'20:00:00'
            ],            [
                'place_id'=>1,
                'day_of_week'=>4,
                'open_time'=>'11:00:00',
                'close_time'=>'21:00:00'
            ],            [
                'place_id'=>1,
                'day_of_week'=>5,
                'open_time'=>'11:00:00',
                'close_time'=>'21:00:00'
            ],            [
                'place_id'=>1,
                'day_of_week'=>6,
                'open_time'=>'11:00:00',
                'close_time'=>'15:00:00'
            ],


            [// donut
                'place_id'=>2,
                'day_of_week'=>0,
                'open_time'=>'5:30:00',
                'close_time'=>'22:00:00'
            ],            [
                'place_id'=>2,
                'day_of_week'=>1,
                'open_time'=>'5:30:00',
                'close_time'=>'22:00:00'
            ],            [
                'place_id'=>2,
                'day_of_week'=>2,
                'open_time'=>'5:30:00',
                'close_time'=>'22:00:00'
            ],            [
                'place_id'=>2,
                'day_of_week'=>3,
                'open_time'=>'5:30:00',
                'close_time'=>'22:00:00'
            ],            [
                'place_id'=>2,
                'day_of_week'=>4,
                'open_time'=>'5:30:00',
                'close_time'=>'00:00:00'
            ],            [
                'place_id'=>2,
                'day_of_week'=>5,
                'open_time'=>'5:30:00',
                'close_time'=>'00:00:00'
            ],            [
                'place_id'=>2,
                'day_of_week'=>6,
                'open_time'=>'5:30:00',
                'close_time'=>'22:00:00'
            ],     
            
            
            [// district
                'place_id'=>3,
                'day_of_week'=>0,
                'open_time'=>'11:00:00',
                'close_time'=>'22:00:00'
            ],            [
                'place_id'=>3,
                'day_of_week'=>1,
                'open_time'=>'11:00:00',
                'close_time'=>'22:00:00'
            ],            [
                'place_id'=>3,
                'day_of_week'=>2,
                'open_time'=>'11:00:00',
                'close_time'=>'22:00:00'
            ],            [
                'place_id'=>3,
                'day_of_week'=>3,
                'open_time'=>'11:00:00',
                'close_time'=>'22:00:00'
            ],            [
                'place_id'=>3,
                'day_of_week'=>4,
                'open_time'=>'11:00:00',
                'close_time'=>'22:00:00'
            ],            [
                'place_id'=>3,
                'day_of_week'=>5,
                'open_time'=>'11:00:00',
                'close_time'=>'22:00:00'
            ],
            ]);
    }
}
