<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ReferralController extends Controller
{
    protected $apiUrl = 'http://127.0.0.1:5001';

    public function showForm()
    {
        $response = Http::get($this->apiUrl . '/mdc-categories');
        $mdcCategories = $response->successful() ? $response->json() : [];

        return view('existing', ['mdcCategories' => $mdcCategories]);
    }

    public function submit(Request $request)
    {
        $ageMap = ['0-17' => 0, '18-29' => 1, '30-49' => 2, '50-69' => 3, '70+' => 4];
        $severityMap = ['Minor' => 1, 'Moderate' => 2, 'Major' => 3, 'Extreme' => 4];
        $riskMap = ['Minor' => 0, 'Moderate' => 1, 'Major' => 2, 'Extreme' => 3];

        $payload = [
            'age_group' => $ageMap[$request->input('age_group')] ?? 1,
            'gender' => $request->input('gender'),
            'admission_type' => $request->input('admission_type'),
            'length_of_stay' => (float) $request->input('length_of_stay'),
            'mdc_code' => (int) $request->input('mdc_code'),
            'severity_code' => $severityMap[$request->input('severity')] ?? 1,
            'risk_of_mortality_code' => $riskMap[$request->input('risk_of_mortality')] ?? 0,
            'medical_surgical' => $request->input('medical_surgical'),
            'emergency_department' => $request->input('admission_type') === 'Emergency',
            'current_hospital_name' => $request->input('current_hospital'),
        ];

        $response = Http::post($this->apiUrl . '/predict-existing', $payload);

        $mdcResponse = Http::get($this->apiUrl . '/mdc-categories');
        $mdcCategories = $mdcResponse->successful() ? $mdcResponse->json() : [];

        if (!$response->successful()) {
            return view('existing', [
                'mdcCategories' => $mdcCategories,
                'error' => 'Unable to reach the referral system. Please make sure the API is running.',
            ]);
        }

        return view('existing', [
            'mdcCategories' => $mdcCategories,
            'result' => $response->json(),
        ]);
    }
}