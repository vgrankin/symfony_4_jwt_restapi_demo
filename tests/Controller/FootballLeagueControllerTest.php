<?php

namespace App\Tests;

use App\Controller\FootballLeagueController;
use Symfony\Component\HttpFoundation\Response;

class FootballLeagueControllerTest extends BaseTestCase
{
    public function testCreateLeague____when_Creating_New_League____League_Is_Created_And_Returned_With_Correct_Response_Status()
    {
        $leagueName = "Test League 1";

        $data = [
            "name" => $leagueName
        ];

        $token = $this->getValidToken();
        $response = $this->client->post("leagues", [
            'body' => json_encode($data),
            'headers' => [
                'Authorization' => 'Bearer ' . $token
            ]
        ]);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $responseData = json_decode($response->getBody(), true);

        $this->assertArrayHasKey("data", $responseData);
        $this->assertArrayHasKey("name", $responseData['data']);
        $this->assertEquals($leagueName, $responseData['data']['name']);
    }

    public function testCreateLeague____when_Creating_New_League_With_Existing_Name____League_Is_NOT_Created_And_Error_Response_Is_Returned()
    {
        // Create new league
        $leagueName = "Test League 1";

        $data = [
            "name" => $leagueName
        ];

        $token = $this->getValidToken();
        $response = $this->client->post("leagues", [
            'body' => json_encode($data),
            'headers' => [
                'Authorization' => 'Bearer ' . $token
            ]
        ]);

        // Now try to create new league with same name
        $token = $this->getValidToken();
        $response = $this->client->post("leagues", [
            'body' => json_encode($data),
            'headers' => [
                'Authorization' => 'Bearer ' . $token
            ]
        ]);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());

        $responseData = json_decode($response->getBody(), true);
        $this->assertArrayHasKey("error", $responseData);
        $this->assertArrayHasKey("code", $responseData['error']);
        $this->assertArrayHasKey("message", $responseData['error']);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $responseData['error']['code']);
        $this->assertEquals("League with given name already exists", $responseData['error']['message']);
    }

    public function testCreateLeague____when_Creating_New_League_With_Blank_Name____League_Is_NOT_Created_And_Error_Response_Is_Returned()
    {
        $leagueName = "";

        $data = [
            "name" => $leagueName
        ];

        $token = $this->getValidToken();
        $response = $this->client->post("leagues", [
            'body' => json_encode($data),
            'headers' => [
                'Authorization' => 'Bearer ' . $token
            ]
        ]);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());

        $responseData = json_decode($response->getBody(), true);
        $this->assertArrayHasKey("error", $responseData);
        $this->assertArrayHasKey("code", $responseData['error']);
        $this->assertArrayHasKey("message", $responseData['error']);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $responseData['error']['code']);
        $this->assertEquals("League name must not be empty!", $responseData['error']['message']);
    }

    public function testCreateLeague____when_Creating_New_League_With_Invalid_JSON____League_Is_NOT_Created_And_Error_Response_Is_Returned()
    {
        $leagueName = "";

        $data = [
            "name" => $leagueName
        ];

        $token = $this->getValidToken();
        $response = $this->client->post("leagues", [
            'body' => '{"notvalid"}',
            'headers' => [
                'Authorization' => 'Bearer ' . $token
            ]
        ]);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());

        $responseData = json_decode($response->getBody(), true);
        $this->assertArrayHasKey("error", $responseData);
        $this->assertArrayHasKey("code", $responseData['error']);
        $this->assertArrayHasKey("message", $responseData['error']);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $responseData['error']['code']);
        $this->assertEquals("Invalid JSON format", $responseData['error']['message']);
    }

    public function testDeleteLeague____when_Deleting_Existing_League_Having_No_Teams____League_Is_Deleted_And_Status_204_Is_Returned()
    {
        $league = $this->createTestLeague();
        $token = $this->getValidToken();
        $response = $this->client->delete("leagues/{$league->getId()}", [
            'headers' => [
                'Authorization' => 'Bearer ' . $token
            ]
        ]);

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function testDeleteLeague____when_Deleting_Existing_League_Having_Team_Assigned____League_Is_NOT_Deleted_And_Error_Is_Returned()
    {
        $team = $this->createTestTeam();

        $token = $this->getValidToken();
        $response = $this->client->delete("leagues/{$team->getLeague()->getId()}", [
            'headers' => [
                'Authorization' => 'Bearer ' . $token
            ]
        ]);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());

        $responseData = json_decode($response->getBody(), true);
        $this->assertArrayHasKey("error", $responseData);
        $this->assertArrayHasKey("code", $responseData['error']);
        $this->assertArrayHasKey("message", $responseData['error']);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $responseData['error']['code']);
        $this->assertEquals("Can't delete league. There are teams assigned to it. Remove them first!", $responseData['error']['message']);
    }

    public function testGetLeagueTeams____when_Getting_Existing_League_Having_Team_Assigned____Success_Response_Is_Returned_With_Data()
    {
        $team = $this->createTestTeam();

        $token = $this->getValidToken();
        $response = $this->client->get("/api/leagues/{$team->getLeague()->getId()}/teams", [
            'headers' => [
                'Authorization' => 'Bearer ' . $token
            ]
        ]);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $responseData = json_decode($response->getBody(), true);

        $this->assertArrayHasKey("data", $responseData);
        $this->assertArrayHasKey("league", $responseData['data']);
        $this->assertArrayHasKey("teams", $responseData['data']);

        $this->assertArrayHasKey("id", $responseData['data']['league']);
        $this->assertArrayHasKey("name", $responseData['data']['league']);
        $this->assertEquals($team->getLeague()->getId(), $responseData['data']['league']['id']);
        $this->assertEquals($team->getLeague()->getName(), $responseData['data']['league']['name']);

        $this->assertArrayHasKey(0, $responseData['data']['teams']);
        $this->assertArrayHasKey("id", $responseData['data']['teams'][0]);
        $this->assertArrayHasKey("name", $responseData['data']['teams'][0]);
        $this->assertArrayHasKey("strip", $responseData['data']['teams'][0]);
        $this->assertArrayHasKey("league_id", $responseData['data']['teams'][0]);
        $this->assertEquals($team->getId(), $responseData['data']['teams'][0]['id']);
        $this->assertEquals($team->getName(), $responseData['data']['teams'][0]['name']);
        $this->assertEquals($team->getStrip(), $responseData['data']['teams'][0]['strip']);
        $this->assertEquals($team->getLeague()->getId(), $responseData['data']['teams'][0]['league_id']);
    }

    public function testGetLeagueTeams____when_Getting_Nonexistent_League____Error_Response_Is_Returned()
    {
        $team = $this->createTestTeam();

        $token = $this->getValidToken();
        $response = $this->client->get("/api/leagues/7777777/teams", [
            'headers' => [
                'Authorization' => 'Bearer ' . $token
            ]
        ]);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $responseData = json_decode($response->getBody(), true);

        $responseData = json_decode($response->getBody(), true);
        $this->assertArrayHasKey("error", $responseData);
        $this->assertArrayHasKey("code", $responseData['error']);
        $this->assertArrayHasKey("message", $responseData['error']);
        $this->assertEquals(Response::HTTP_NOT_FOUND, $responseData['error']['code']);
        $this->assertEquals("Not Found", $responseData['error']['message']);
    }
}