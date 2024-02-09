<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\MasterCities;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class MasterCityController extends Controller
{
    
    public function __construct() {

    }

    public function searchCities(Request $request) {
        $cities = [];

        if (empty($request->input())) {
            $cities = $this->searchCitiesFromDb($request);
        } else {
            $valid = $this->validationRequest($request);
            if (!$valid['status']) {
                return response()->json($valid['errors'], 400);
            }

            if ($request->input('id')) {
                $cityId = $request->input('id');
                $cities = $this->searchCitiesFromDb($request, $cityId);
                if (!$cities) {
                    return response()->json([
                        'message' => 'error.not_found',
                        'errors' => [
                            'id' => 'error.not_found',
                        ]
                    ], 404);
                }
                $cities = [$cities];
            }
        }

        $response = [
            'data' => $cities
        ];
        return response()->json($response);
    }

    private function validationRequest(Request $request) {
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

            return [
                'status' => false,
                'errors' => [
                    'message' => $firstError[0],
                    'errors' => $errorsResponse,
                ]
            ];
        }

        return ['status' => true];
    }

    public function searchCitiesFromDb(Request $request, $id = '') {
        $searchFromDb = env('SEARCH_FROM_DB', true);
        if (!$searchFromDb) {
            return $this->searchFromThirdPartyApi($request, $id);
        }

        $cities = MasterCities::with('province')->get();
        if ($id != '') {
            $cities = MasterCities::with('province')->find($id);
            if (!$cities) {
                return false;
            }
        }
        return $cities;
    }

    public function searchFromThirdPartyApi(Request $request, $id = '') {
        $apiUrl = env('RAJAONGKIR_URL', 'https://api.rajaongkir.com/starter');
        $apiKey = env('RAJAONGKIR_KEY', '0df6d5bf733214af6c6644eb8717c92c');

        try {
            $client = new Client();
            $url = $apiUrl . '/city';
            if ($id != '') {
                $url .= '?id='.$id;
            }
            $response = $client->get($url, [
                'headers' => [
                    'key' => $apiKey,
                    'Accept' => 'application/json',
                ],
            ]);
            $data = json_decode($response->getBody(), true);
            if (!isset($data['rajaongkir']['results'])) {
                if ($id != '') {
                    return false;
                }
                return [];
            }

            $data = $data['rajaongkir']['results'];
            if ($id != '') {
                $data = [
                    "id" => $data["city_id"],
                    "province_id" => $data["province_id"],
                    "type" => $data["type"],
                    "name" => $data["city_name"],
                    "postal_code" => $data["postal_code"],
                    "province" => [
                        "id" => $data["province_id"],
                        "name" => $data["province"]
                    ]
                    ];
            } else {
                $data = array_map(function ($result) {
                    return [
                        "id" => $result["city_id"],
                        "province_id" => $result["province_id"],
                        "type" => $result["type"],
                        "name" => $result["city_name"],
                        "postal_code" => $result["postal_code"],
                        "province" => [
                            "id" => $result["province_id"],
                            "name" => $result["province"]
                        ]
                    ];
                }, $data);
            }

            return $data;

        } catch (ClientException $e) {
            if ($id != '') {
                return false;
            }
            return [];
        }
    }
}
