<?php

use Illuminate\Database\Seeder;

class TravellerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Traveler::class, 100)->create();
    }
}
