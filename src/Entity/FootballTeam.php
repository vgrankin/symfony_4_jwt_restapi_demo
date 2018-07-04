<?php


namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
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
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\Column(type="string")
     */
    private $strip;
}