<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Operator;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function toggle(Request $request, Operator $operator)
    {
        $doctor = session('doctor_name');

        if (! $doctor) {
            return redirect()
                ->back()
                ->with('error', 'Tentukan nama Doctor kamu terlebih dahulu di menu atas.');
        }

        $existing = Favorite::where('doctor_name', $doctor)
            ->where('operator_id', $operator->id)
            ->first();

        if ($existing !== null) {
            $existing->delete();
            $status = "{$operator->name} dihapus dari favorit.";
        } else {
            Favorite::create([
                'doctor_name' => $doctor,
                'operator_id' => $operator->id,
            ]);
            $status = "{$operator->name} ditambahkan ke favorit.";
        }

        return redirect()->back()->with('status', $status);
    }
}
