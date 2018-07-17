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
            [//1
                'name'=>'burgers'
            ],
            [//2
                'name'=>'donuts'
            ],
            [//3
                'name'=>'coffee'
            ],
            [//4
                'name'=>'vegan'
            ],
            [//5
                'name'=>'alcohol'
            ],
            [//6
                'name'=>'taco'
            ],
            [//7
                'name'=>'mexican'
            ],
            [//8
                'name'=>'Italian'
            ],
            [//9
                'name'=>'pizza'
            ],
            [//10
                'name'=>'Japanese'
            ],
            [//11
                'name'=>'Asian'
            ],
            [//12
                'name'=>'Chinese'
            ],
            [//13
                'name'=>'Vietnamese'
            ],
            [//14
                'name'=>'German'
            ],
            [//15
                'name'=>'French'
            ],
            [//16
                'ice cream'
            ],
            [//17
                'milkshake'
            ],
            [//18
                'shaved ice'
            ],
            [//19
                'cakes'
            ]

        ]);
    }
}
