<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create(['name' => 'adm']);
        Role::create(['name' => 'sub-adm']);
        Role::create(['name' => 'client']);

        Permission::create(['name' => 'criar documentos']);
        Permission::create(['name' => 'ver detalhes']);
    }
}
