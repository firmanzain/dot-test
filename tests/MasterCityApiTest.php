<?php

use Laravel\Lumen\Testing\DatabaseTransactions;
use Laravel\Lumen\Testing\TestCase;

class MasterCityApiTest extends TestCase
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

    public function testSearchCityWithAuthentication()
    {
        $this->refreshApplication();
        $response = $this->call('GET', '/api/search/cities', [], [], [], [
            'HTTP_Authorization' => 'Bearer ' . $this->accessToken,
        ]);

        $this->assertEquals(200, $response->status());
        $this->assertIsArray($response->json());
    }

    public function testSearchCityWithoutAuthentication()
    {
        $this->refreshApplication();
        $response = $this->call('GET', '/api/search/cities');

        $this->assertEquals(401, $response->status());
        $this->assertArrayHasKey('message', $response->json());
        $this->assertEquals('error.unauthorized', $response->json()['message']);
    }

    public function testSearchCityWithValidParams()
    {
        $this->refreshApplication();
        $response = $this->call('GET', '/api/search/cities', [
            'id' => '1'
        ], [], [], [
            'HTTP_Authorization' => 'Bearer ' . $this->accessToken,
        ]);

        $this->assertEquals(200, $response->status());
        $this->assertIsArray($response->json());
    }

    public function testSearchCityWithInvalidParams()
    {
        $this->refreshApplication();
        $response = $this->call('GET', '/api/search/cities', [
            'id' => '1a'
        ], [], [], [
            'HTTP_Authorization' => 'Bearer ' . $this->accessToken,
        ]);

        $this->assertEquals(400, $response->status());
        $this->assertArrayHasKey('message', $response->json());
        $this->assertEquals('error.invalid', $response->json()['message']);
    }

    public function testSearchCityNotFoundData()
    {
        $this->refreshApplication();
        $response = $this->call('GET', '/api/search/cities', [
            'id' => '1234567890'
        ], [], [], [
            'HTTP_Authorization' => 'Bearer ' . $this->accessToken,
        ]);

        $this->assertEquals(404, $response->status());
        $this->assertArrayHasKey('message', $response->json());
        $this->assertEquals('error.not_found', $response->json()['message']);
    }
}
