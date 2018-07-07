<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Tests\BaseTestCase;

class AuthControllerTest extends BaseTestCase
{
    public function testAuthenticate____When_Email_and_Password_is_Provided____Returns_JWT_Token_In_Response()
    {
        $email = "rest@jwtrestapi.com";
        $password = "test123";

        $this->createTestUser($email, $password);

        $response = $this->client->post("authenticate", [
            'auth' => [$email, $password]
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getBody(), true);

        $this->assertArrayHasKey("data", $responseData);
        $this->assertArrayHasKey("token", $responseData['data']);
    }

    public function testAuthenticate____When_Email_Does_Not_Exist____Returns_No_Such_User_Response()
    {
        $response = $this->client->post("authenticate", [
            'auth' => ["nosuchuser@jwtrestapi.com", "test123"]
        ]);

        $responseData = json_decode($response->getBody(), true);

        $this->assertEquals(400, $response->getStatusCode());

        $this->assertArrayHasKey("error", $responseData);
        $this->assertArrayHasKey("code", $responseData['error']);
        $this->assertArrayHasKey("message", $responseData['error']);
        $this->assertEquals(400, $responseData['error']['code']);
        $this->assertEquals("No such user", $responseData['error']['message']);
    }

    public function testAuthenticate____When_Password_is_Incorrect____Returns_Invalid_Password_Response()
    {
        $email = "rest@jwtrestapi.com";
        $password = "test123";

        $this->createTestUser($email, $password);

        $response = $this->client->post("authenticate", [
            'auth' => [$email, "testXXXXXXX"] // invalid password
        ]);

        $responseData = json_decode($response->getBody(), true);

        $this->assertEquals(400, $response->getStatusCode());

        $this->assertArrayHasKey("error", $responseData);
        $this->assertArrayHasKey("code", $responseData['error']);
        $this->assertArrayHasKey("message", $responseData['error']);
        $this->assertEquals(400, $responseData['error']['code']);
        $this->assertEquals("Incorrect password", $responseData['error']['message']);
    }

    public function testAuthenticate____When_Incorrect_Credentials____Returns_Invalid_Credentials_Response()
    {
        $response = $this->client->post("authenticate");

        $responseData = json_decode($response->getBody(), true);

        $this->assertEquals(400, $response->getStatusCode());

        $this->assertArrayHasKey("error", $responseData);
        $this->assertArrayHasKey("code", $responseData['error']);
        $this->assertArrayHasKey("message", $responseData['error']);
        $this->assertEquals(400, $responseData['error']['code']);
        $this->assertEquals("Invalid credentials", $responseData['error']['message']);
    }
}
