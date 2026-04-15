<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $viewDashboardPermission = Permission::firstOrCreate(['name' => 'view dashboard']);
        $adminRole->givePermissionTo($viewDashboardPermission);

       // Utilisateur "admin"
        $admin = User::updateOrCreate([
            'email'    => 'admin@example.com',
        ], [
            'name'     => 'Admin',
            'password' => bcrypt('password'),
        ]);

        // Utilisateur "user"
        $user = User::updateOrCreate([
            'email'    => 'user@example.com',
        ], [
            'name'     => 'User',
            'password' => bcrypt('password'),
        ]);

        $admin->assignRole($adminRole);

        // Produits de l'admin
        Product::factory()->count(3)->create([
            'user_id' => $admin->id,
        ]);

        // Produits de l'utilisateur simple
        Product::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);
    }
}
