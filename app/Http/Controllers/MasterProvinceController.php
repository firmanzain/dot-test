<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\MasterProvinces;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class MasterProvinceController extends Controller
{
    
    public function __construct() {

    }

    public function searchProvinces(Request $request) {
        $provinces = [];

        if (empty($request->input())) {
            $provinces = $this->searchProvincesFromDb($request);
        } else {
            $this->validationRequest($request);

            if ($request->input('id')) {
                $provinceId = $request->input('id');
                $provinces = $this->searchProvincesFromDb($request, $provinceId);
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
            return response()->json([
                'message' => $firstError[0],
                'errors' => $errorsResponse,
            ], 400);
        }
    }

    public function searchProvincesFromDb(Request $request, $id = '') {
        $searchFromDb = env('SEARCH_FROM_DB', true);
        if (!$searchFromDb) {
            return $this->searchFromThirdPartyApi($request, $id);
        }

        $provinces = MasterProvinces::all();
        if ($id != '') {
            $provinces = MasterProvinces::find($id);
            if (!$provinces) {
                return false;
            }
        }
        return $provinces;
    }

    public function searchFromThirdPartyApi(Request $request, $id = '') {
        $apiUrl = env('RAJAONGKIR_URL', 'https://api.rajaongkir.com/starter');
        $apiKey = env('RAJAONGKIR_KEY', '0df6d5bf733214af6c6644eb8717c92c');

        try {
            $client = new Client();
            $url = $apiUrl . '/province';
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

            return $data;

        } catch (ClientException $e) {
            if ($id != '') {
                return false;
            }
            return [];
        }
    }
}
