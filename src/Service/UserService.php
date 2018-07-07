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

    /**
     * @param $data
     *    $data = [
     *      'name' => (string) User name. Required.
     *      'password' => (string) User (plain) password. Required.
     *    ]
     * @return User|string User entity or string in case of error
     */
    public function createUser($data)
    {
        $email = $data['email'];
        $plainPassword = $data['password'];

        $user = new User();
        $user->setEmail($email);
        $encoded = password_hash($plainPassword, PASSWORD_DEFAULT);
        $user->setPassword($encoded);

        try {
            $this->em->persist($user);
            $this->em->flush();

            return $user;
        } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $ex) {
            return "User with given email already exists";
        } catch (\Exception $ex) {
            return "Unable to create user";
        }
    }
}