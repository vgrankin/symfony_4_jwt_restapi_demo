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
}