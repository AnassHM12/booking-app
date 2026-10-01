<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(['email' => 'admin@example.com'], ['name' => 'Admin', 'password' => 'password', 'role' => 'admin']);
        $staffUser = User::firstOrCreate(['email' => 'staff@example.com'], ['name' => 'Staff One', 'password' => 'password', 'role' => 'staff']);
        User::firstOrCreate(['email' => 'customer@example.com'], ['name' => 'Customer', 'password' => 'password', 'role' => 'customer']);

        Service::firstOrCreate(['name' => 'Haircut'], ['duration_minutes' => 30, 'price_cents' => 2500]);
        Service::firstOrCreate(['name' => 'Consultation'], ['duration_minutes' => 60, 'price_cents' => 5000]);
        Staff::firstOrCreate(['name' => 'Alex']);
        Staff::firstOrCreate(['name' => 'Sam'], ['user_id' => $staffUser->id]);
        Staff::firstOrCreate(['name' => 'Jordan']);
        Staff::firstOrCreate(['name' => 'Taylor']);
    }
}
