<?php


namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @UniqueEntity("name")
 * @ORM\Entity(repositoryClass="App\Repository\FootballTeamRepository")
 * @ORM\Table(name="football_team")
 */
class FootballTeam
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\FootballLeague", inversedBy="teams")
     * @ORM\JoinColumn(nullable=false)
     */
    private $league;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=191, unique=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string")
     */
    private $strip;

    /**
     * @param mixed $league
     */
    public function setLeague($league): void
    {
        $this->league = $league;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @param mixed $strip
     */
    public function setStrip($strip): void
    {
        $this->strip = $strip;
    }

    /**
     * @return FootballLeague
     */
    public function getLeague()
    {
        return $this->league;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getStrip()
    {
        return $this->strip;
    }
}