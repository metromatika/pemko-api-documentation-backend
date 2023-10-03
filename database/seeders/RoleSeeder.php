<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $roles = [
            Role::ROLE_ADMINISTRATOR => Role::ROLE_ALIAS_ADMINISTRATOR,
            Role::ROLE_PROGRAMMER => Role::ROLE_ALIAS_PROGRAMMER,
            Role::ROLE_REGULAR_USER => Role::ROLE_ALIAS_REGULAR_USER
        ];

        foreach ($roles as $roleName => $roleAlias) {
            Role::factory()->create([
                'name' => $roleName,
                'alias' => $roleAlias
            ]);
        }
    }
}
