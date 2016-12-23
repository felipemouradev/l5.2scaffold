<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //$this->call(Modules\Client\Database\Seeders\LeadTableSeeder::class);
        $this->call(Modules\Client\Database\Seeders\LeadAgregadoTableSeeder::class);
    }
}
