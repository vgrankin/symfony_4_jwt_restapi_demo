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

/**
 * Class FootballLeagueController
 * @package App\Controller
 */
class FootballLeagueController extends Controller
{
    /**
     * @Route("/api/do")
     * @Method("GET")
     */
    public function doAction__TOREMOVE__(FootballLeagueService $leagueService, ResponseErrorDecoratorService $responseDecorator)
    {
        $data = ['name' => 'League 1'];
        throw new Exception('Nooooooooooooooo!');

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
            $data = $responseDecorator->decorateError($status, $result);
        }

        return new JsonResponse($data, $status);
    }

    /**
     * Creates new league by given name (if not exists)
     *
     * @Route("/api/leagues")
     * @Method("POST")
     * @param Request $request
     * @param FootballLeagueService $leagueService
     * @param ResponseErrorDecoratorService $responseDecorator
     * @return JsonResponse
     */
    public function newLeague(
        Request $request,
        FootballLeagueService $leagueService,
        ResponseErrorDecoratorService $responseDecorator
    )
    {
        $body = $request->getContent();
        $data = json_decode($body, true);

        if (is_null($data) || !isset($data['name'])) {
            $status = JsonResponse::HTTP_BAD_REQUEST;
            $data = $responseDecorator->decorateError(
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
            $data = $responseDecorator->decorateError($status, $result);
        }

        return new JsonResponse($data, $status);
    }

    /**
     * @Route("/api/leagues")
     * @Method("GET")
     */
    public function listLeagues()
    {
        $em = $this->getDoctrine()->getManager();
        $leagues = $em->getRepository("App:FootballLeague")
            ->findAll();

        $data = [
            'leagues' => $leagues
        ];

        return new JsonResponse($data);
    }

    /**
     * @Route("/api/leagues/{id}/teams") "{id}" part of the route will be "magically" converted to property
     *                                of a $league entity, so Symfony will try to find corresponding (by-id) league.
     *                                In case it cannot find the league, 404 will be issued.
     * @Method("GET")
     * @param FootballLeague $league Symfony will find league entity by {id} and will assign it to $league
     * @return JsonResponse List of football teams for the given league
     */
    public function getTeams(FootballLeague $league)
    {
        $teams = $league->getTeams();
        var_dump($teams);

        $data = [
            'teams' => $teams
        ];

        return new JsonResponse($data);
    }
}