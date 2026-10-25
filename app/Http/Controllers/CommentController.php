<?php

namespace App\Http\Controllers;

use App\Models\Operator;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Operator $operator)
    {
        $validated = $request->validate([
            'doctor_name' => ['required', 'string', 'min:2', 'max:20'],
            'body' => ['required', 'string', 'min:3', 'max:1000'],
        ]);

        session(['doctor_name' => $validated['doctor_name']]);

        $operator->comments()->create([
            'doctor_name' => $validated['doctor_name'],
            'body' => $validated['body'],
        ]);

        return redirect()
            ->route('operators.show', $operator)
            ->with('status', 'Komentar berhasil ditambahkan.');
    }
}
