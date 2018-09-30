<?php

use Illuminate\Database\Seeder;

class PlacesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('places')->insert([
            [
                'name'=>'Lil\' Deuce Scoop',
                'summary'=>'Offers Super Premium Ice Cream, sweets, and nostalgic candies. All in a fun and friendly atmosphere.',
                'address'=>'110 W Main St',
                'city'=>'Mulvane',
                'state_code'=>'KS',
                'phone_number'=>'3165583853',
                'email_address'=>null,
                'menu_link'=>null,
                'website_url'=>null,
                'facebook_link'=>'https://www.facebook.com/lildeucescoop/',
                'instagram_link'=>'https://www.instagram.com/lildeucescoop/',
                'google_place_id'=>'ChIJpXaYO7LtuocRWz1Hdr69of8'

            ],
            [
                'name'=>'The Donut Whole',
                'summary'=>'Funky cafe & gallery serving an eclectic menu of coffee drinks, donuts & boba tea in an artsy space.',
                'address'=>'1720 E. Douglas',
                'city'=>'Wichita',
                'state_code'=>'KS',
                'phone_number'=>'3162623700',
                'email_address'=>'info@donutwhole.com',
                'menu_link'=>'http://thedonutwhole.com/donuts/',
                'website_url'=>'http://thedonutwhole.com/',
                'facebook_link'=>'https://www.facebook.com/thedonutwhole/?ref=bookmarks',
                'instagram_link'=>'https://www.instagram.com/thedonutwhole/',
                'google_place_id'=>'ChIJ3fFi9pjjuocR15t_QEzXbuw'


            ],
            [
                'name'=>'District Taqueria',
                'summary'=>'Tacos & other street-style Mexican eats, plus tequila & beer, served in a hip, brick-lined space.',
                'address'=>'917 E Douglas Ave',
                'city'=>'Wichita',
                'state_code'=>'KS',
                'phone_number'=>'3168328155',
                'email_address'=>null,
                'menu_link'=>'http://www.districttaqueria.com/public/menu/DistrictTaqueria_Menu.pdf',
                'website_url'=>'http://www.districttaqueria.com/',
                'facebook_link'=>'https://www.facebook.com/districttaqueria/',
                'instagram_link'=>'https://www.instagram.com/districttaqueria/',
                'google_place_id'=>'ChIJw-BIopXjuocRKvXuM__386Y'
            ],
            [
                'name'=>'Sente Games & Refreshments',
                'summary'=>'A unique coffee shop in Wichita, KS that specializes in gourmet coffee, snacks and board games.',
                'address'=>'132 E Douglas Ave',
                'city'=>'Wichita',
                'state_code'=>'KS',
                'phone_number'=>'3162608636',
                'email_address'=>null,
                'menu_link'=>null,
                'website_url'=>'http://www.senteict.com/',
                'facebook_link'=>'https://www.facebook.com/senteICT/',
                'instagram_link'=>'https://www.instagram.com/sente_ict/',
                'google_place_id'=>'ChIJe_7ZxOnjuocRVmyXQV9491Q'
            ]

        ]);

    }
}
