<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            $adminRole = Role::firstOrCreate(['name' => 'admin']);
            $userRole = Role::firstOrCreate(['name' => 'user']);

            $admin = User::updateOrCreate(
                ['email' => 'admin@test.com'],
                [
                    'name' => 'Admin',
                    'password' => Hash::make('p4ssw0rd'),
                ]
            );
            $this->assignRoleIfMissing($admin, $adminRole);

            $user = User::updateOrCreate(
                ['email' => 'user@test.com'],
                [
                    'name' => 'User',
                    'password' => Hash::make('p4ssw0rd'),
                ]
            );
            $this->assignRoleIfMissing($user, $userRole);
        });
    }

    /**
     * Assign role if user does not already have it.
     */
    protected function assignRoleIfMissing(User $user, Role $role): void
    {
        if (! $user->hasRole($role->name)) {
            $user->assignRole($role);
        }
    }
}
