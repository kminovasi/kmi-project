<?php

namespace Database\Seeders;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NewEventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Event::create([
            'event_name' => 'SISI Innovation Award',
            'company_code' => 'G300',
            'date_start' => Carbon::create(2024, 3, 15),
            'date_end' => Carbon::create(2024, 3, 16),
            'status' => 'active',
            'year' => 2024,
            'description' => 'Konferensi tahunan tentang teknologi terbaru.',
            'type' => 'AP',
        ]);
    }
}
