<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

class RegisterController extends Controller
{
    public function __invoke(Request $request)
    {
        DB::transaction(function () use ($request) {
            $tenant = Tenant::create([
                'name' => $request->tenant_name,
                'trial_ends_at' => now()->addDays(30),
            ]);

            $user = User::create([
                'tenant_id' => $tenant->id,
                'sequential_id' => 1,
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);

            DB::table('tenant_sequences')->insert([
                'tenant_id' => $tenant->id,
                'entity_type' => 'users',
                'last_sequence_value' => 1,
            ]);

            app(PermissionRegistrar::class)->setPermissionsTeamId($user->tenant_id);

            $adminRole = Role::firstOrCreate([
                'sequential_id' => 1,
                'name' => 'Administrador',
                'tenant_id' => $user->tenant_id,
            ]);

            DB::table('tenant_sequences')->insert([
                'tenant_id' => $user->tenant_id,
                'entity_type' => 'roles',
                'last_sequence_value' => 1,
            ]);

            $adminRole->syncPermissions(Permission::all());
            $user->assignRole($adminRole);

            Auth::login($user);
        });

        return response()->make('', 409, ['X-Inertia-Location' => route('home.index')]);
    }
}
