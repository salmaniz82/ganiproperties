<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'app@ganiproperties.co.uk')->first()
            ?? User::where('email', 'admin@ganipropertyservices.co.uk')->first();

        if ($user) {
            $user->update([
                'name' => 'Gani Property Admin',
                'email' => 'app@ganiproperties.co.uk',
                'phone' => '02086737778',
                'is_admin' => true,
            ]);

            return;
        }

        User::create([
            'name' => 'Gani Property Admin',
            'email' => 'app@ganiproperties.co.uk',
            'phone' => '02086737778',
            'password' => 'password',
            'is_admin' => true,
        ]);
    }
}
