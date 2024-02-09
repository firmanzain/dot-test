<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\MasterProvinces;

class MasterProvinceController extends Controller
{
    
    public function __construct()
    {

    }

    public function searchProvinces(Request $request) {

        $provinces = MasterProvinces::all();

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
                $errorsResponse = [];
                foreach ($errors as $key => $value) {
                    $errorsResponse[$key] = $value[0];
                }
                return response()->json([
                    'message' => $firstError[0],
                    'errors' => $errorsResponse,
                ], 400);
            }

            if ($request->input('id')) {
                $provinceId = $request->input('id');
                $provinces = MasterProvinces::find($provinceId);
                if (!$provinces) {
                    return response()->json([
                        'message' => 'error.not_found',
                        'errors' => [
                            'id' => 'error.not_found',
                        ]
                    ], 404);
                }
                $provinces = [$provinces];
            }
        }

        $response = [
            'data' => $provinces
        ];
        return response()->json($response);
    }
}
