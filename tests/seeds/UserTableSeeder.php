<?php

namespace Tests\Seeds;

use Illuminate\Database\Seeder;
use Tests\Models\User;

class UserTableSeeder extends Seeder
{
    public function run()
    {
        User::factory()
            ->count(50)
            ->hasTags(5)
            ->hasProfile()
            ->create();
    }
}
