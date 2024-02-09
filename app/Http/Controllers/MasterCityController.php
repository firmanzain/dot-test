<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\MasterCities;

class MasterCityController extends Controller
{
    
    public function __construct()
    {

    }

    public function searchCities(Request $request) {

        $cities = MasterCities::with('province')->get();

        if (!empty($request->input())) {
            $validator = Validator::make($request->all(), [
                'id' => ['nullable', 'numeric'],
            ],
            [
                'id.numeric' => 'error.invalid'
            ]);
     
            if ($validator->fails()) {
                $errors = $validator->errors()->toArray();
                $firstError = reset($errors);
                return response()->json([
                    'message' => $firstError[0],
                    'errors' => $errors,
                ], 400);
            }

            if ($request->input('id')) {
                $provinceId = $request->input('id');
                $cities = MasterCities::with('province')->find($provinceId);
                if (!$cities) {
                    return response()->json(['message' => 'error.not_found'], 404);
                }
            }
        }

        $response = [
            'data' => $cities
        ];
        return response()->json($response);
    }
}
