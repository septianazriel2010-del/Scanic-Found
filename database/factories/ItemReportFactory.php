<?php

namespace Database\Factories;

use App\Models\ItemReport;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemReportFactory extends Factory
{
    protected $model = ItemReport::class;

    public function definition(): array
    {
        $categories = ['Elektronik', 'Dompet', 'Alat Tulis', 'Pakaian', 'Kartu Pelajar', 'Buku'];
        $locations = ['Kantin', 'Lapangan', 'Perpustakaan', 'Lab Komputer', 'Musala', 'Ruang Kelas'];

        return [
            'user_id' => User::factory(),
            'type' => fake()->randomElement(['lost', 'found']),
            'title' => fake()->words(3, true),
            'description' => fake()->sentence(15),
            'category' => fake()->randomElement($categories),
            'location' => fake()->randomElement($locations),
            'incident_date' => fake()->dateTimeBetween('-1 month', 'now'),
            'status' => ItemReport::STATUS_OPEN,
        ];
    }
}
