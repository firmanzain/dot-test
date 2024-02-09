<?php

use Laravel\Lumen\Testing\DatabaseTransactions;
use Laravel\Lumen\Testing\TestCase;

class MasterProvinceApiTest extends TestCase
{
    use DatabaseTransactions;

    private $accessToken;

    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $credentials = [
            'email' => 'admin@dot.co.id',
            'password' => 'password',
        ];

        $response = $this->call('POST', '/api/login', $credentials);
        $this->accessToken = $response->json()['access_token'];
    }

    public function testSearchProvinceWithAuthentication()
    {
        $this->refreshApplication();
        $response = $this->call('GET', '/api/search/provinces', [], [], [], [
            'HTTP_Authorization' => 'Bearer ' . $this->accessToken,
        ]);

        $this->assertEquals(200, $response->status());
        $this->assertIsArray($response->json());
    }

    public function testSearchProvinceWithoutAuthentication()
    {
        $this->refreshApplication();
        $response = $this->call('GET', '/api/search/provinces');

        $this->assertEquals(401, $response->status());
        $this->assertArrayHasKey('message', $response->json());
        $this->assertEquals('error.unauthorized', $response->json()['message']);
    }

    public function testSearchProvinceWithValidParams()
    {
        $this->refreshApplication();
        $response = $this->call('GET', '/api/search/provinces', [
            'id' => '1'
        ], [], [], [
            'HTTP_Authorization' => 'Bearer ' . $this->accessToken,
        ]);

        $this->assertEquals(200, $response->status());
        $this->assertIsArray($response->json());
    }

    public function testSearchProvinceWithInvalidParams()
    {
        $this->refreshApplication();
        $response = $this->call('GET', '/api/search/provinces', [
            'id' => '1a'
        ], [], [], [
            'HTTP_Authorization' => 'Bearer ' . $this->accessToken,
        ]);

        $this->assertEquals(400, $response->status());
        $this->assertArrayHasKey('message', $response->json());
        $this->assertEquals('error.invalid', $response->json()['message']);
    }

    public function testSearchProvinceNotFoundData()
    {
        $this->refreshApplication();
        $response = $this->call('GET', '/api/search/provinces', [
            'id' => '1234567890'
        ], [], [], [
            'HTTP_Authorization' => 'Bearer ' . $this->accessToken,
        ]);

        $this->assertEquals(404, $response->status());
        $this->assertArrayHasKey('message', $response->json());
        $this->assertEquals('error.not_found', $response->json()['message']);
    }
}
