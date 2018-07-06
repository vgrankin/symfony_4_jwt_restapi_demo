<?php

namespace App\Tests;

use App\Entity\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class BaseTestCase extends KernelTestCase
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    public function setUp()
    {
        $this->client = new \GuzzleHttp\Client([
            'base_uri' => 'http://localhost:8000/api/',
            'exceptions' => false
        ]);

        $kernel = self::bootKernel();

        $this->em = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    protected function createTestUser($email, $password)
    {
        $container = $this->getPrivateContainer();
        $userService = $container
            ->get('App\Service\UserService');

        return $userService->createUser([
            'email' => $email,
            'password' => $password
        ]);
    }

    private function getPrivateContainer()
    {
        self::bootKernel();

        // returns the real and unchanged service container
        $container = self::$kernel->getContainer();

        // gets the special container that allows fetching private services
        $container = self::$container;

        return $container;
    }

    protected function tearDown()
    {

        parent::tearDown();


        $em = $this->em;

        $query = $em->createQuery('DELETE App:FootballTeam ft WHERE 1 = 1');
        $query->execute();

        $query = $em->createQuery('DELETE App:FootballLeague fl WHERE 1 = 1');
        $query->execute();

        $query = $em->createQuery('DELETE App:User u WHERE 1 = 1');
        $query->execute();

        parent::tearDown();

        $this->em->close();
        $this->em = null; // avoid memory leaks
    }
}