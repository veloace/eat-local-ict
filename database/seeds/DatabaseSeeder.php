<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        $this->call(TagsTableSeeder::class);
        $this->call(PlacesTableSeeder::class);
        $this->call(PlaceTagsTableSeeder::class);
        $this->call(PlaceHoursTableSeeder::class);
    }
}
