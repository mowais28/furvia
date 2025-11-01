<?php

namespace Database\Seeders;

use App\Models\ListCertification;
use App\Models\ListDegree;
use App\Models\ListService;
use App\Models\ListSkill;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ListSeeder extends Seeder
{
    public function run()
    {

        ListDegree::insert([
            ['name' => 'Matric'],
            ['name' => 'Intermediate'],
            ['name' => 'Bachelors'],
            ['name' => 'Graduated'],
            ['name' => 'Masters'],
            ['name' => 'PhD'],
        ]);

        ListCertification::insert([
            ['name' => 'Animal Care Certified'],
            ['name' => 'ISO 9001 Certified'],
            ['name' => 'First Aid Certified'],
            ['name' => 'Pet Grooming Certified'],
        ]);

        ListSkill::insert([
            ['name' => 'Pet Grooming'],
            ['name' => 'Vaccination'],
            ['name' => 'Surgery'],
            ['name' => 'Animal Behavior'],
            ['name' => 'Communication'],
        ]);

        ListService::insert([
            ['name' => 'Pet Checkup'],
            ['name' => 'Vaccination Service'],
            ['name' => 'Grooming Service'],
            ['name' => 'Surgery Assistance'],
            ['name' => 'Consultation'],
        ]);
    }
}
