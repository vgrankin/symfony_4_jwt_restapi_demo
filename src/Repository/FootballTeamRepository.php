<?php


namespace App\Repository;


use Doctrine\ORM\EntityRepository;

class FootballTeamRepository extends EntityRepository
{
    public function getTeamsByLeague(int $leagueId)
    {
//        return $this->findAll();

        return $teams = [
            ['id' => 1, 'team_A'],
            ['id' => 2, 'team_B'],
            ['id' => 3, 'team_C']
        ];
    }
}