<?php

namespace App\Http\Controllers;

use App\Models\Preference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class PreferenceController extends Controller
{
    public function update(Request $request)
    {
        $data = $request->validate([
            'theme' => ['required', 'in:light,dark'],
            'weather_city' => ['required', 'string', 'max:100'],
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
        ]);

        Preference::updateOrCreate(
            ['user_id' => auth()->id()],
            $data
        );

        return back()
            ->with('success', 'Preferensi berhasil diperbarui.')
            ->cookie(
                'theme_preference',
                $data['theme'],
                60 * 24 * 30
            );
    }
}
