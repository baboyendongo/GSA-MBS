<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Validator;
use RuntimeException;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        foreach (config('seeded_accounts.accounts', []) as $account) {
            $email = trim((string) ($account['email'] ?? ''));
            $password = (string) ($account['password'] ?? '');

            if ($email === '' && $password === '') {
                continue;
            }

            $validator = Validator::make([
                'name' => $account['name'] ?? null,
                'email' => $email,
                'password' => $password,
            ], [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255'],
                'password' => ['required', 'string', 'min:12'],
            ]);

            if ($validator->fails()) {
                throw new RuntimeException('Les variables .env de comptes seed sont incomplètes ou invalides. Vérifiez ADMIN_* et SEED_USER_*.');
            }

            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $account['name'],
                    'password' => $password,
                    'is_admin' => (bool) ($account['is_admin'] ?? false),
                ]
            );

            if (($account['is_admin'] ?? false) && ! $user->is_admin) {
                $user->forceFill(['is_admin' => true])->save();
            }
        }
    }
}
