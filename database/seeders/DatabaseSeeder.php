<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Seeders do sistema de escalas médicas
        $this->call([
            CidadeSeeder::class,
            UnidadeSeeder::class,
            SetorSeeder::class,
            TurnoSeeder::class,
            PlantonistaSeeder::class,
            VagaSeeder::class,
        ]);
    }
}
