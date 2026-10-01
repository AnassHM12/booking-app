<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Staff;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function services()
    {
        $p = Service::orderBy('name')->paginate(15);
        return Inertia::render('Services', [
            'services' => $p->items(),
            'meta' => ['current_page' => $p->currentPage(), 'last_page' => $p->lastPage()],
        ]);
    }

    public function storeService(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:255', 'duration_minutes' => 'required|integer|min:15', 'price_cents' => 'required|integer|min:0']);
        Service::create($data);
        return back()->with('success', 'Service saved.');
    }

    public function staff()
    {
        $imgs = [
            'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=200&q=60',
            'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=200&q=60',
            'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?auto=format&fit=crop&w=200&q=60',
        ];
        $p = Staff::orderBy('name')->paginate(15);
        return Inertia::render('Staff', [
            'staff' => collect($p->items())->map(fn ($s, $i) => ['id' => $s->id, 'name' => $s->name, 'img' => $imgs[$s->id % count($imgs)]]),
            'meta' => ['current_page' => $p->currentPage(), 'last_page' => $p->lastPage()],
        ]);
    }

    public function storeStaff(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:255']);
        Staff::create($data);
        return back()->with('success', 'Staff saved.');
    }
}
