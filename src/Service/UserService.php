<?php


namespace App\Service;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService
{
    private $em;
    private $encoder;

    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $encoder)
    {
        $this->em = $em;
        $this->encoder = $encoder;
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

    public function createUser($data)
    {
        $email = $data['email'];
        $plainPassword = $data['password'];

        $user = new User();
        $user->setEmail($email);
        $encoded = password_hash($plainPassword, PASSWORD_DEFAULT);
        $user->setPassword($encoded);

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }
}