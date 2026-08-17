<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NewPatientController extends Controller
{
    protected $apiUrl = 'http://127.0.0.1:5001';

    protected function getSpecialties()
    {
        $response = Http::get($this->apiUrl . '/hospitals');
        $specialties = [];

        if ($response->successful()) {
            foreach ($response->json() as $hospital) {
                foreach (explode(',', $hospital['specialities']) as $item) {
                    $trimmed = trim($item);
                    if ($trimmed !== '') {
                        $specialties[$trimmed] = true;
                    }
                }
            }
            ksort($specialties);
        }

        return array_keys($specialties);
    }

    public function showForm()
    {
        return view('new', ['specialties' => $this->getSpecialties()]);
    }

    public function submit(Request $request)
    {
        $response = Http::post($this->apiUrl . '/predict-new', [
            'required_specialty' => $request->input('required_specialty'),
        ]);

        if (!$response->successful()) {
            return view('new', [
                'specialties' => $this->getSpecialties(),
                'error' => 'Unable to reach the referral system. Please make sure the API is running.',
            ]);
        }

        return view('new', [
            'specialties' => $this->getSpecialties(),
            'result' => $response->json(),
        ]);
    }
}