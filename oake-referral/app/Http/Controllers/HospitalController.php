<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class HospitalController extends Controller
{
    protected $apiUrl = 'http://127.0.0.1:5001';

    public function index()
    {
        $response = Http::get($this->apiUrl . '/hospitals');
        $hospitals = $response->successful() ? $response->json() : [];

        // Sort by tier, then name, for a sensible default order
        usort($hospitals, function ($a, $b) {
            return $a['hospital_tier'] <=> $b['hospital_tier']
                ?: strcmp($a['hospital_name'], $b['hospital_name']);
        });

        return view('hospitals', [
            'hospitals' => $hospitals,
            'apiError' => !$response->successful(),
        ]);
    }
}