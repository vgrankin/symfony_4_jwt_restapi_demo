<?php


namespace App\Service;


use App\Entity\FootballLeague;
use Doctrine\ORM\EntityManagerInterface;

class FootballLeagueService
{
    private $_em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->_em = $em;
    }

    public function createLeague($data)
    {
        $leagueName = $data['name'];

        $league = $this->_em->getRepository("App:FootballLeague")
            ->findOneBy(['name' => $leagueName]);

        if ($league == null) {

            $league = new FootballLeague();
            $league->setName($leagueName);

            $this->_em->persist($league);
            $this->_em->flush();

            return $league;
        } else {
            return false;
        }
    }
}