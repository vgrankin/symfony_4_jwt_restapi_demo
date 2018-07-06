<?php


namespace App\Service;


use App\Entity\FootballLeague;
use Doctrine\DBAL\Exception\InvalidFieldNameException;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Util\Json;
use Symfony\Component\HttpFoundation\JsonResponse;

class FootballLeagueService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Create league by given data
     *
     * @param $data Array which contains information about league
     * @return FootballLeague|string FootballLeague or error message
     */
    public function createLeague($data)
    {
        $leagueName = $data['name'];
        if (empty($leagueName)) {
            return "League name must not be empty!";
        }

        try {
            $league = new FootballLeague();
            $league->setName($leagueName);

            $this->em->persist($league);
            $this->em->flush();

            return $league;
        } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $ex) {
            return "League with given name already exists.";
        } catch (\Exception $ex) {
            return "Unable to create league.";
        }
    }
}