<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $usuarios = [
            ['name' => 'Luis',    'email' => 'luis@laminucia.test',    'password' => '0000'],
            ['name' => 'Gabriel', 'email' => 'gabriel@laminucia.test', 'password' => '1111'],
            ['name' => 'Sergio',  'email' => 'sergio@laminucia.test',  'password' => '2222'],
        ];

        foreach ($usuarios as $u) {
            User::updateOrCreate(
                ['email' => $u['email']],
                ['name' => $u['name'], 'password' => Hash::make($u['password'])]
            );
        }
    }
}
