<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DoctorController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'doctor_name' => ['required', 'string', 'min:2', 'max:20'],
        ]);

        session(['doctor_name' => $validated['doctor_name']]);

        return redirect()->back()->with('status', 'Halo, Doctor '.$validated['doctor_name'].'!');
    }
}
