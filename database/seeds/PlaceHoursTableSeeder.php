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
            //just hours of lil Deuce Scoop
            [//Monday
                'place_id'=>1,
                'day_of_week'=>0,
                'start'=>'11:00:00',
                'end'=>'21:00:00',
                'closed'=>false
            ],
            [//Tuesday
                'place_id'=>1,
                'day_of_week'=>1,
                'start'=>'11:00:00',
                'end'=>'21:00:00',
                'closed'=>false
            ],
            [//Wednesday
                'place_id'=>1,
                'day_of_week'=>2,
                'start'=>'11:00:00',
                'end'=>'21:00:00',
                'closed'=>false

            ],
            [//Thursday
                'place_id'=>1,
                'day_of_week'=>3,
                'start'=>'11:00:00',
                'end'=>'21:00:00',
                'closed'=>false
            ],
            [//Friday
                'place_id'=>1,
                'day_of_week'=>4,
                'start'=>'11:00:00',
                'end'=>'21:00:00',
                'closed'=>false
            ],
            [//Saturday
                'place_id'=>1,
                'day_of_week'=>5,
                'start'=>'12:00:00',
                'end'=>'21:00:00',
                'closed'=>false
            ],
            [//Sunday
                'place_id'=>1,
                'day_of_week'=>6,
                'start'=>null,
                'end'=>null,
                'closed'=>true
            ],
        ]);
    }
}
