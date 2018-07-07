<?php


namespace App\Service;

use App\Entity\FootballTeam;
use Doctrine\ORM\EntityManagerInterface;

class FootballTeamService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Create football team by given data
     *
     * @param $data array which contains information about team
     *    $data = [
     *      'name' => (string) Team name. Required.
     *      'strip' => (string) Team strip. Required.
     *      'league_id' => (int) League id. Required.
     *    ]
     * @return FootballTeam|string FootballTeam or error message
     */
    public function createTeam($data)
    {
        if (empty($data['name']) || empty($data['strip']) || empty((int)$data['league_id'])) {
            return "Name, strip and league id must be provided to create new football team";
        }

        try {
            $league = $this->em
                ->getRepository('App:FootballLeague')
                ->find($data['league_id']);

            if ($league) {
                $team = new FootballTeam();
                $team->setName($data['name']);
                $team->setStrip($data['strip']);
                $team->setLeague($league);

                $this->em->persist($team);
                $this->em->flush();

                return $team;
            } else {
                return "Unable to find league";
            }

        } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $ex) {
            return "Team with given name already exists";
        } catch (\Exception $ex) {
            return "Unable to create team";
        }
    }

    /**
     * Update football team by given data
     *
     * @param $data array which contains information about team
     *    $data = [
     *      'name' => (string) Team name. Optional.
     *      'strip' => (string) Team strip. Optional.
     *      'league_id' => (int) League id. Optional.
     *    ]
     * @return FootballTeam|string FootballTeam or error message
     */
    public function updateTeam(FootballTeam $team, array $data)
    {
        try {
            if (isset($data['name'])) {
                $team->setName($data['name']);
            }

            if (isset($data['strip'])) {
                $team->setStrip($data['strip']);
            }

            $league = $team->getLeague();
            if (isset($data['league_id'])) {
                if ($league->getId() != $data['league_id']) {
                    $league = $this->em
                        ->getRepository('App:FootballLeague')
                        ->find($data['league_id']);

                    if (!$league) {
                        return "Unable to find league to update to";
                    }
                }
            }

            $team->setLeague($league);

            $this->em->persist($team);
            $this->em->flush();

            return $team;
        } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $ex) {
            return "Team with given name already exists";
        } catch (\Exception $ex) {
            return "Unable to update team";
        }
    }

    /**
     * @param FootballTeam $team
     * @return bool|string True if team was successfully deleted, error message otherwise
     */
    public function deleteTeam(FootballTeam $team)
    {
        try {
            $this->em->remove($team);
            $this->em->flush();
        } catch (\Exception $ex) {
            return "Unable to remove league";
        }

        return true;
    }
}