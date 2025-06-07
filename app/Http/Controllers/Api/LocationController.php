<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MitraDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LocationController extends Controller
{
    public function nearby(Request $request)
    {
        Log::info('Location API called', [
            'lat' => $request->get('lat'),
            'lng' => $request->get('lng')
        ]);

        $lat = $request->get('lat');
        $lng = $request->get('lng');

        // Validate input
        if (!$lat || !$lng) {
            Log::error('Missing coordinates', [
                'lat' => $lat,
                'lng' => $lng
            ]);
            return response()->json([
                'error' => 'Latitude dan longitude diperlukan'
            ], 400);
        }

        try {
            // Get nearby mitra using Haversine formula
            $nearby = MitraDetail::select([
                'mitra_details.*',
                'users.name',
                DB::raw('(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance')
            ])
            ->join('users', 'mitra_details.user_id', '=', 'users.id')
            ->having('distance', '<', 10) // Only show mitra within 10km
            ->orderBy('distance', 'asc')
            ->setBindings([$lat, $lng, $lat])
            ->get();

            Log::info('Found nearby mitra', [
                'count' => $nearby->count()
            ]);

            return response()->json($nearby);
        } catch (\Exception $e) {
            Log::error('Error finding nearby mitra', [
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'error' => 'Terjadi kesalahan saat mencari mitra terdekat'
            ], 500);
        }
    }
}
