<?php 

use Laravel\Lumen\Testing\DatabaseTransactions;
use Laravel\Lumen\Testing\TestCase;

class LoginApiTest extends TestCase
{
    use DatabaseTransactions;

    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }

    public function testLoginWithValidCredentials()
    {
        $credentials = [
            'email' => 'admin@dot.co.id',
            'password' => 'password',
        ];

        $this->refreshApplication();
        $response = $this->call('POST', '/api/login', $credentials);

        $this->assertEquals(200, $response->status());
        $this->assertArrayHasKey('access_token', $response->json());
    }

    public function testLoginWithInvalidRequest()
    {
        $credentials = [
            'email' => 'admin',
            'password' => 'pass',
        ];

        $this->refreshApplication();
        $response = $this->call('POST', '/api/login', $credentials);

        $this->assertEquals(400, $response->status());
    }

    public function testLoginWithInvalidCredentials()
    {
        $credentials = [
            'email' => 'invalid@example.com',
            'password' => 'invalidpassword',
        ];

        $this->refreshApplication();
        $response = $this->call('POST', '/api/login', $credentials);

        $this->assertEquals(401, $response->status());
        $this->assertArrayHasKey('message', $response->json());
        $this->assertEquals('error.unauthorized', $response->json()['message']);
    }
}
