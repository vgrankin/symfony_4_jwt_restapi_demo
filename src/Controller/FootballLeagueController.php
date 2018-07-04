<?php

namespace App\Controller;
use App\Entity\FootballLeague;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class FootballLeagueController
 * @package App\Controller
 */
class FootballLeagueController extends Controller
{
    /**
     * @Route("/api/league/new")
     */
    public function newLeague()
    {
        $league = new FootballLeague();
        $league->setName("League_" . rand(1,10000));

        $em = $this->getDoctrine()->getManager();
        $em->persist($league);
        $em->flush();

        return new JsonResponse("FootballLeague created!");
    }

    /**
     * @Route("/api/league/list")
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
}