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

    public function testAuthenticate____When_Password_is_Incorrect____Returns_Invalid_Credentials_Response()
    {
        $email = "rest@jwtrestapi.com";
        $password = "test123";

        $this->createTestUser($email, $password);

        $response = $this->client->post("authenticate", [
            'auth' => [$email, "testXXXXXXX"] // invalid password
        ]);

        $this->assertEquals(400, $response->getStatusCode());
    }
}
