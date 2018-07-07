<?php


namespace App\Controller;

use App\Entity\FootballTeam;
use App\Service\FootballLeagueService;
use App\Service\FootballTeamService;
use App\Service\ResponseErrorDecoratorService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class FootballTeamController extends Controller implements TokenAuthenticatedController
{
    /**
     * Creates new team by given name (if not exists) and stripe IF league given team assigned to exists
     *
     * @Route("/api/teams")
     * @Method("POST")
     * @param Request $request
     * @param FootballLeagueService $leagueService
     * @param FootballTeamService $teamService
     * @param ResponseErrorDecoratorService $errorDecorator
     * @return JsonResponse
     */
    public function createTeam(
        Request $request,
        FootballLeagueService $leagueService,
        FootballTeamService $teamService,
        ResponseErrorDecoratorService $errorDecorator
    )
    {
        $body = $request->getContent();
        $data = json_decode($body, true);

        if (is_null($data) || !isset($data['name'], $data['strip'], $data['league_id'])) {
            $status = JsonResponse::HTTP_BAD_REQUEST;
            $data = $errorDecorator->decorateError(
                JsonResponse::HTTP_BAD_REQUEST, "Invalid JSON format"
            );

            return new JsonResponse($data, $status);
        }

        $result = $teamService->createTeam($data);
        if ($result instanceof FootballTeam) {
            $status = JsonResponse::HTTP_CREATED;
            $data = [
                'data' => [
                    'id' => $result->getId(),
                    'name' => $result->getName(),
                    'strip' => $result->getStrip(),
                    'league_id' => $result->getLeague()->getId()
                ]
            ];
        } else {
            $status = JsonResponse::HTTP_BAD_REQUEST;
            $data = $errorDecorator->decorateError($status, $result);
        }

        return new JsonResponse($data, $status);
    }

    /**
     * @Route("/api/teams/{id}")
     * @Method("PUT")
     */
    public function updateTeam(
        FootballTeam $team,
        Request $request,
        FootballTeamService $teamService,
        ResponseErrorDecoratorService $errorDecorator
    )
    {
        $body = $request->getContent();
        $data = json_decode($body, true);

        if (is_null($data)) {
            $status = JsonResponse::HTTP_BAD_REQUEST;
            $data = $errorDecorator->decorateError(
                JsonResponse::HTTP_BAD_REQUEST, "Invalid JSON format"
            );

            return new JsonResponse($data, $status);
        }

        $result = $teamService->updateTeam($team, $data);
        if ($result instanceof FootballTeam) {
            $status = JsonResponse::HTTP_OK;
            $data = [
                'data' => [
                    'id' => $result->getId(),
                    'name' => $result->getName(),
                    'strip' => $result->getStrip(),
                    'league_id' => $result->getLeague()->getId()
                ]
            ];
        } else {
            $status = JsonResponse::HTTP_BAD_REQUEST;
            $data = $errorDecorator->decorateError($status, $result);
        }

        return new JsonResponse($data, $status);
    }

    /**
     * @Route("/api/teams/{id}")
     * @Method("DELETE")
     * @param FootballTeam $team
     * @param FootballTeamService $teamService
     * @param ResponseErrorDecoratorService $errorDecorator
     * @return JsonResponse
     */
    public function deleteTeam(
        FootballTeam $team,
        FootballTeamService $teamService,
        ResponseErrorDecoratorService $errorDecorator
    )
    {
        $result = $teamService->deleteTeam($team);
        if ($result === true) {
            $status = JsonResponse::HTTP_NO_CONTENT;
            $data = null;
        } else {
            $status = JsonResponse::HTTP_BAD_REQUEST;
            $data = $errorDecorator->decorateError($status, $result);
        }

        return new JsonResponse($data, $status);
    }
}