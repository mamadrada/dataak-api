<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiLoginTest extends TestCase
{
    public function testApiRegister()
    {
        $body = [
            'first_name' => 'Mohammad',
            'last_name' => 'Radaei',
            'mobile' => '09193700623',
            'email' => 'mradaei7@gmail.com',
            'image' => '',
            'active' => '1',
            'password' => '123456'
        ];
        $response = $this->json('POST', '/api/register', $body, ['Accept' => 'application/json'])
            ->assertStatus(200);
        $this->assertArrayHasKey('token', $response->json());

    }

    public function testApiLogin()
    {
        $body = [
            'username' => '09193700623',
            'password' => '123456'
        ];
        $response = $this->json('POST', '/api/login', $body, ['Accept' => 'application/json'])
            ->assertStatus(200);
        $this->assertArrayHasKey('token', $response->json());
    }
}
