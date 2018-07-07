<?php

namespace App\Controller;

use App\Entity\FootballLeague;
use App\Service\FootballLeagueService;
use App\Service\ResponseErrorDecoratorService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Controller\TokenAuthenticatedController;

/**
 * Class FootballLeagueController
 * @package App\Controller
 */
class FootballLeagueController extends Controller implements TokenAuthenticatedController
{
    /**
     * Creates new league by given name (if not exists)
     *
     * @Route("/api/leagues")
     * @Method("POST")
     * @param Request $request
     * @param FootballLeagueService $leagueService
     * @param ResponseErrorDecoratorService $errorDecorator
     * @return JsonResponse
     */
    public function createLeague(
        Request $request,
        FootballLeagueService $leagueService,
        ResponseErrorDecoratorService $errorDecorator
    )
    {
        $body = $request->getContent();
        $data = json_decode($body, true);

        if (is_null($data) || !isset($data['name'])) {
            $status = JsonResponse::HTTP_BAD_REQUEST;
            $data = $errorDecorator->decorateError(
                JsonResponse::HTTP_BAD_REQUEST, "Invalid JSON format"
            );

            return new JsonResponse($data, $status);
        }

        $result = $leagueService->createLeague($data);
        if ($result instanceof FootballLeague) {
            $status = JsonResponse::HTTP_CREATED;
            $data = [
                'data' => [
                    'id' => $result->getId(),
                    'name' => $result->getName()
                ]
            ];
        } else {
            $status = JsonResponse::HTTP_BAD_REQUEST;
            $data = $errorDecorator->decorateError($status, $result);
        }

        return new JsonResponse($data, $status);
    }

    /**
     * @Route("/api/leagues/{id}/teams") "{id}" part of the route will be "magically" converted to property
     *                                of a $league entity, so Symfony will try to find corresponding (by-id) league.
     *                                In case it cannot find the league, 404 will be issued.
     * @Method("GET")
     * @param FootballLeague $league Symfony will find league entity by {id} and will assign it to $league
     * @return JsonResponse List of football teams for the given league
     */
    public function getLeagueTeams(FootballLeague $league)
    {
        $teams = $league->getTeams();
        $teamsArr = [];
        foreach ($teams as $team) {
            $teamsArr[] = [
                'id' => $team->getId(),
                'name' => $team->getName(),
                'strip' => $team->getStrip(),
                'league_id' => $league->getId()
            ];
        }

        $status = JsonResponse::HTTP_OK;
        $data = [
            'data' => [
                'league' => [
                    'id' => $league->getId(),
                    'name' => $league->getName()
                ],
                'teams' => $teamsArr
            ]
        ];

        return new JsonResponse($data, $status);
    }

    /**
     * @Route("/api/leagues/{id}")
     * @Method("DELETE")
     * @param FootballLeague $league
     * @param FootballLeagueService $leagueService
     * @param ResponseErrorDecoratorService $errorDecorator
     * @return JsonResponse
     */
    public function deleteLeague(
        FootballLeague $league,
        FootballLeagueService $leagueService,
        ResponseErrorDecoratorService $errorDecorator
    )
    {
        $result = $leagueService->deleteLeague($league);
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