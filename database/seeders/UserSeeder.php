<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Spend;
use App\Models\SpendDetail;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'username' => 'admin',
            'fullname' => 'admin admin',
            'email' => 'admin'.'@gmail.com',
            'password' => Hash::make('asdasd123'),
        ]);

        $user2 = User::create([
            'username' => 'admin2',
            'fullname' => 'admin admin',
            'email' => 'admin2'.'@gmail.com',
            'password' => Hash::make('asdasd123'),
        ]);

        Spend::factory()
            ->has(SpendDetail::factory()->count(3), 'details')
            ->count(3)
            ->for($user)
            ->create();

        Spend::factory()
            ->has(SpendDetail::factory()->count(3), 'details')
            ->count(3)
            ->for($user2)
            ->create();
    }
}
