<?php


namespace App\Service;


use App\Entity\FootballLeague;
use Doctrine\ORM\EntityManagerInterface;

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
     * @param $data array which contains information about league
     *    $data = [
     *      'name' => (string) League name. Required.
     *    ]
     * @return FootballLeague|string FootballLeague or error message
     */
    public function createLeague(array $data)
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
            return "League with given name already exists";
        } catch (\Exception $ex) {
            return "Unable to create league";
        }
    }

    /**
     * @param FootballLeague $league
     * @return bool|string True if league was successfully deleted, error message otherwise
     */
    public function deleteLeague(FootballLeague $league)
    {
        try {
            $this->em->remove($league);
            $this->em->flush();
        } catch (\Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException $ex) {
            return "Can't delete league. There are teams assigned to it. Remove them first!";
        } catch (\Exception $ex) {
            return "Unable to remove league";
        }

        return true;
    }
}