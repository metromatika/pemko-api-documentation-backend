<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $administrator = User::factory()->create([
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password'),
            'role_id' => Role::firstWhere('name', '=', 'administrator')->id
        ]);

        $programmer = User::factory()->create([
            'name' => 'Programmer',
            'username' => 'programmer',
            'email' => 'programmer@gmail.com',
            'password' => bcrypt('password'),
            'role_id' => Role::firstWhere('name', '=', 'programmer')->id
        ]);
    }
}
