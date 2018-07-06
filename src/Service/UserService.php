<?php


namespace App\Service;


use App\Entity\FootballLeague;
use Doctrine\ORM\EntityManagerInterface;

class UserService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getUser($email)
    {
        $user = $this->em->getRepository('App:User')
            ->findOneBy(['email' => $email]);

        if ($user) {
            return $user;
        } else {
            return "No such user";
        }
    }
}