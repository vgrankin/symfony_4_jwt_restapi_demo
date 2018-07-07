<?php

namespace App\Tests\Controller;

use App\Controller\FootballTeamController;
use App\Tests\BaseTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class FootballTeamControllerTest extends BaseTestCase
{
    public function testCreateTeam____when_Creating_New_Team____Team_Is_Created_And_Returned_With_Correct_Response_Status()
    {
        $league = $this->createTestLeague();

        $teamName = "Team 1";
        $strip = "Strip 1";

        $data = [
            "name" => $teamName,
            "strip" => $strip,
            "league_id" => $league->getId()
        ];

        $response = $this->client->post("teams", [
            'body' => json_encode($data),
            'headers' => [
                'Authorization' => 'Bearer ' . $this->getValidToken()
            ]
        ]);

        $this->assertEquals(JsonResponse::HTTP_CREATED, $response->getStatusCode());
        $responseData = json_decode($response->getBody(), true);

        $this->assertArrayHasKey("data", $responseData);
        $this->assertArrayHasKey("name", $responseData['data']);
        $this->assertArrayHasKey("strip", $responseData['data']);
        $this->assertArrayHasKey("league_id", $responseData['data']);
        $this->assertEquals($teamName, $responseData['data']['name']);
        $this->assertEquals($strip, $responseData['data']['strip']);
        $this->assertEquals($league->getId(), $responseData['data']['league_id']);
    }

    public function testUpdateTeam____when_Updating_Team_With_Correct_Data____Team_Is_Updated_And_Returned_With_Correct_Response_Status()
    {
        $team = $this->createTestTeam();

        $newTeamName = "New Team Name";
        $newStrip = "New Strip";

        $data = [
            "name" => $newTeamName,
            "strip" => $newStrip,
            "league_id" => $team->getLeague()->getId()
        ];

        $response = $this->client->put("teams/{$team->getId()}", [
            'body' => json_encode($data),
            'headers' => [
                'Authorization' => 'Bearer ' . $this->getValidToken()
            ]
        ]);

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $responseData = json_decode($response->getBody(), true);

        $this->assertArrayHasKey("data", $responseData);
        $this->assertArrayHasKey("name", $responseData['data']);
        $this->assertArrayHasKey("strip", $responseData['data']);
        $this->assertArrayHasKey("league_id", $responseData['data']);
        $this->assertEquals($newTeamName, $responseData['data']['name']);
        $this->assertEquals($newStrip, $responseData['data']['strip']);
        $this->assertEquals($team->getLeague()->getId(), $responseData['data']['league_id']);
    }

    public function testUpdateTeam____when_Updating_Team_With_Strip_Field_Only____Team_Is_Updated_And_Success_Response_Is_Returned()
    {
        $team = $this->createTestTeam();

        $newStrip = "New Strip!";

        $data = [
            "strip" => $newStrip
        ];

        $response = $this->client->put("teams/{$team->getId()}", [
            'body' => json_encode($data),
            'headers' => [
                'Authorization' => 'Bearer ' . $this->getValidToken()
            ]
        ]);

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $responseData = json_decode($response->getBody(), true);

        $this->assertArrayHasKey("data", $responseData);
        $this->assertArrayHasKey("name", $responseData['data']);
        $this->assertArrayHasKey("strip", $responseData['data']);
        $this->assertArrayHasKey("league_id", $responseData['data']);
        $this->assertEquals($team->getName(), $responseData['data']['name']);
        $this->assertEquals($newStrip, $responseData['data']['strip']);
        $this->assertEquals($team->getLeague()->getId(), $responseData['data']['league_id']);
    }

    public function testUpdateTeam____when_Updating_Team_With_Existing_Team_Name____Team_Is_NOT_Updated_And_Error_Response_Is_Returned()
    {
        // create "existing" team
        $league = $this->createTestLeague("Existing League");
        $existingTeam = $this->createTestTeam("Existing Team", $league);

        // create "new" team
        $team = $this->createTestTeam();

        $data = [
            "name" => $existingTeam->getName(), // will try to update new team's name to already existing name
            "strip" => $team->getStrip(),
            "league_id" => $team->getLeague()->getId()
        ];

        $response = $this->client->put("teams/{$team->getId()}", [
            'body' => json_encode($data),
            'headers' => [
                'Authorization' => 'Bearer ' . $this->getValidToken()
            ]
        ]);

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
        $responseData = json_decode($response->getBody(), true);

        $responseData = json_decode($response->getBody(), true);
        $this->assertArrayHasKey("error", $responseData);
        $this->assertArrayHasKey("code", $responseData['error']);
        $this->assertArrayHasKey("message", $responseData['error']);
        $this->assertEquals(400, $responseData['error']['code']);
        $this->assertEquals("Team with given name already exists", $responseData['error']['message']);
    }

    public function testUpdateTeam____when_Updating_Team_With_Invalid_JSON____Team_Is_NOT_Updated_And_Error_Response_Is_Returned()
    {
        $team = $this->createTestTeam();

        $response = $this->client->put("teams/{$team->getId()}", [
            'body' => '{"notvalid"}',
            'headers' => [
                'Authorization' => 'Bearer ' . $this->getValidToken()
            ]
        ]);

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
        $responseData = json_decode($response->getBody(), true);

        $responseData = json_decode($response->getBody(), true);
        $this->assertArrayHasKey("error", $responseData);
        $this->assertArrayHasKey("code", $responseData['error']);
        $this->assertArrayHasKey("message", $responseData['error']);
        $this->assertEquals(400, $responseData['error']['code']);
        $this->assertEquals("Invalid JSON format", $responseData['error']['message']);
    }

    public function testUpdateTeam____when_Updating_Nonexistent_Team____Error_Response_Is_Returned()
    {
        $team = $this->createTestTeam();

        $response = $this->client->put("teams/7777777", [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->getValidToken()
            ]
        ]);

        $this->assertEquals(JsonResponse::HTTP_NOT_FOUND, $response->getStatusCode());
        $responseData = json_decode($response->getBody(), true);

        $responseData = json_decode($response->getBody(), true);
        $this->assertArrayHasKey("error", $responseData);
        $this->assertArrayHasKey("code", $responseData['error']);
        $this->assertArrayHasKey("message", $responseData['error']);
        $this->assertEquals(JsonResponse::HTTP_NOT_FOUND, $responseData['error']['code']);
        $this->assertEquals("Not Found", $responseData['error']['message']);
    }

    public function testUpdateTeam____when_Updating_With_Nonexistent_League____Error_Response_Is_Returned()
    {
        $team = $this->createTestTeam();

        $data = [
            "league_id" => 7777777 // will try to assign football team to nonexistent league
        ];

        $response = $this->client->put("teams/{$team->getId()}", [
            'body' => json_encode($data),
            'headers' => [
                'Authorization' => 'Bearer ' . $this->getValidToken()
            ]
        ]);

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
        $responseData = json_decode($response->getBody(), true);

        $responseData = json_decode($response->getBody(), true);
        $this->assertArrayHasKey("error", $responseData);
        $this->assertArrayHasKey("code", $responseData['error']);
        $this->assertArrayHasKey("message", $responseData['error']);
        $this->assertEquals(400, $responseData['error']['code']);
        $this->assertEquals("Unable to find league to update to", $responseData['error']['message']);
    }

    public function testDeleteTeam____when_Deleting_Existing_Team____Team_Is_Deleted_And_Status_204_Is_Returned()
    {
        $team = $this->createTestTeam();
        $token = $this->getValidToken();
        $response = $this->client->delete("teams/{$team->getId()}", [
            'headers' => [
                'Authorization' => 'Bearer ' . $token
            ]
        ]);

        $this->assertEquals(JsonResponse::HTTP_NO_CONTENT, $response->getStatusCode());
    }
}
