<?php


namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass="App\Repository\FootballLeagueRepository")
 * @ORM\Table(name="football_league")
 */
class FootballLeague
{
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\FootballTeam", mappedBy="league")
     */
    private $teams;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }


}