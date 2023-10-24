<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
          RoleSeeder::class,
            OrganizationSeeder::class,
            RenacytSeeder::class,
            PeopleSeeder::class,
            EmailSeeder::class,
            UserSeeder::class,
            RTypeSeeder::class,
            JournalSeeder::class,
            PlanSeeder::class,
            CategorySeeder::class,
            ResearchStatesSeeder::class,
            ResearchSeeder::class,
            ResearchAuthorSeeder::class,
            OutcomeSeeder::class,
            ResearchLogSeeder::class, 
            FinalSeeder::class,
            DocumentSeeder::class,
        ]);
    }
}
