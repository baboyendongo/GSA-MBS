<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class CreateAdminCommand extends Command
{
    protected $signature = 'admin:create {email? : Adresse e-mail de l’administrateur}';

    protected $description = 'Créer le premier administrateur ou promouvoir un compte existant';

    public function handle(): int
    {
        $email = $this->argument('email') ?: $this->ask('Adresse e-mail de l’administrateur');

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Adresse e-mail invalide.');

            return self::FAILURE;
        }

        $user = User::where('email', $email)->first();
        if ($user) {
            if ($user->is_admin) {
                $this->info('Ce compte est déjà administrateur.');

                return self::SUCCESS;
            }

            $user->forceFill(['is_admin' => true])->save();
            $this->info('Le compte existant a été promu administrateur.');

            return self::SUCCESS;
        }

        $name = $this->ask('Nom de l’administrateur');
        $password = $this->secret('Mot de passe (12 caractères minimum)');
        $confirmation = $this->secret('Confirmer le mot de passe');

        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $confirmation,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:12', 'confirmed'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'is_admin' => true,
        ]);

        $this->info('Le compte administrateur a été créé.');

        return self::SUCCESS;
    }
}