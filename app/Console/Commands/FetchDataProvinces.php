<?php 

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use App\Models\MasterProvinces;

class FetchDataProvinces extends Command
{
    protected $name = 'fetch:data-provinces';
    
    public function handle()
    {
        $apiUrl = env('RAJAONGKIR_URL', 'https://api.rajaongkir.com/starter');
        $apiKey = env('RAJAONGKIR_KEY', '0df6d5bf733214af6c6644eb8717c92c');

        $client = new Client();
        $url = $apiUrl . '/province';
        $response = $client->get($url, [
            'headers' => [
                'key' => $apiKey,
                'Accept' => 'application/json',
            ],
        ]);
        $data = json_decode($response->getBody(), true);

        if (isset($data['rajaongkir']['results']) && sizeof($data['rajaongkir']['results']) > 0) {
            foreach ($data['rajaongkir']['results'] as $key => $value) {
                MasterProvinces::updateOrCreate(
                    ['id' => $value['province_id']],
                    ['name' => $value['province']]
                );
            }
        }

        $this->info('Data provinces successfully updated');
    }
}