<?php


namespace App\Controller;

use App\Entity\FootballLeague;
use App\Entity\FootballTeam;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class FootballTeamController extends Controller
{
    /**
     * @Route("/api/team/{id}")
     */
    public function getTeam(FootballTeam $team)
    {
        $data = [
            'team' => $team
        ];

        return new JsonResponse($data);
    }

    /**
     * @Route("/api/team/{id}/list") "{id}" part of the route will be "magically" converted to property
     *                                of a $league entity, so Symfony will try to find corresponding (by-id) league.
     *                                In case it cannot find the league, 404 will be issued.
     * @Method("GET")
     * @param FootballLeague $league Symfony will find league entity by {id} and will assign it to $league
     * @return JsonResponse List of football teams for the given league
     */
    public function getTeams(FootballLeague $league)
    {
        dump($league);
        die();

        $em = $this->getDoctrine()->getManager();
        $teams = $em->getRepository("App:FootballTeam")
            ->getTeamsByLeague($leagueId);

        $data = [
            'teams' => $teams
        ];

        return new JsonResponse($data);
    }
}