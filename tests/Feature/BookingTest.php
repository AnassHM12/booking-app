<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Service;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_double_booking_blocked(): void
    {
        $customer = User::create(['name' => 'C', 'email' => 'c@example.com', 'password' => 'password', 'role' => 'customer']);
        $service = Service::create(['name' => 'Haircut', 'duration_minutes' => 60, 'price_cents' => 1000]);
        $staff = Staff::create(['name' => 'Alex']);

        Appointment::create([
            'service_id' => $service->id, 'staff_id' => $staff->id, 'customer_id' => $customer->id,
            'starts_at' => '2030-01-01 10:00:00', 'ends_at' => '2030-01-01 11:00:00',
            'status' => 'confirmed', 'payment_status' => 'paid',
        ]);

        $this->assertTrue(Appointment::staffConflict($staff->id, '2030-01-01 10:30:00', '2030-01-01 11:30:00'));
        $this->assertFalse(Appointment::staffConflict($staff->id, '2030-01-01 11:00:00', '2030-01-01 12:00:00'));
    }

    public function test_customer_cannot_view_others_booking(): void
    {
        $a = User::create(['name' => 'A', 'email' => 'a@example.com', 'password' => 'password', 'role' => 'customer']);
        $b = User::create(['name' => 'B', 'email' => 'b@example.com', 'password' => 'password', 'role' => 'customer']);
        $service = Service::create(['name' => 'S', 'duration_minutes' => 30, 'price_cents' => 500]);
        $staff = Staff::create(['name' => 'Sam']);
        $appt = Appointment::create([
            'service_id' => $service->id, 'staff_id' => $staff->id, 'customer_id' => $a->id,
            'starts_at' => '2030-02-01 10:00:00', 'ends_at' => '2030-02-01 10:30:00',
        ]);

        $this->actingAs($b)->get('/appointments/' . $appt->id)->assertForbidden();
        $this->actingAs($a)->get('/appointments/' . $appt->id)->assertOk();
    }
}
