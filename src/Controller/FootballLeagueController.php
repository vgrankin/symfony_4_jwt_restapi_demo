<?php

namespace App\Controller;

use App\Entity\FootballLeague;
use App\Service\FootballLeagueService;
use App\Service\ResponseErrorDecoratorService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
    public function doAction(FootballLeagueService $leagueService, ResponseErrorDecoratorService $responseDecorator)
    {
        $data = ['name' => 'League 1'];

        $league = $leagueService->createLeague($data);

        if ($league) {
            $status = 201;
            $data = [
                'data' => [
                    'id' => $league->getId(),
                    'name' => $league->getName()
                ]
            ];
        } else {
            $status = 400;
            $data = $responseDecorator->decorateError($status, "League with given name already exists.");
        }

        return new JsonResponse($data, $status);
    }

    /**
     * Creates new league by given name (if not exists)
     *
     * @Route("/api/leagues")
     * @Method("POST")
     */
    public function newLeague(
        Request $request,
        FootballLeagueService $leagueService,
        ResponseErrorDecoratorService $responseDecorator
    )
    {
        $body = $request->getContent();
        $data = json_decode($body, true);

        $league = $leagueService->createLeague($data);

        if ($league) {
            $status = 201;
            $data = [
                'data' => [
                    'id' => $league->getId(),
                    'name' => $league->getName()
                ]
            ];
        } else {
            $status = 400;
            $data = $responseDecorator->decorateError($status, "League with given name already exists.");
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