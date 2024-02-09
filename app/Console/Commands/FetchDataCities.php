<?php 

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use App\Models\MasterCities;

class FetchDataCities extends Command
{
    protected $name = 'fetch:data-cities';
    
    public function handle()
    {
        $apiUrl = env('RAJAONGKIR_URL', 'https://api.rajaongkir.com/starter');
        $apiKey = env('RAJAONGKIR_KEY', '0df6d5bf733214af6c6644eb8717c92c');

        $client = new Client();
        $url = $apiUrl . '/city';
        $response = $client->get($url, [
            'headers' => [
                'key' => $apiKey,
                'Accept' => 'application/json',
            ],
        ]);
        $data = json_decode($response->getBody(), true);

        if (isset($data['rajaongkir']['results']) && sizeof($data['rajaongkir']['results']) > 0) {
            foreach ($data['rajaongkir']['results'] as $key => $value) {
                MasterCities::updateOrCreate(
                    ['id' => $value['city_id']],
                    [
                        'province_id' => $value['province_id'],
                        'type' => $value['type'],
                        'name' => $value['city_name'],
                        'postal_code' => $value['postal_code'],
                    ]
                );
            }
        }

        $this->info('Data cities successfully updated');
    }
}