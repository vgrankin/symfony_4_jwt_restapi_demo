<?php

namespace App\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BaseTestCase extends KernelTestCase
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    public function setUp()
    {
        $this->client = new \GuzzleHttp\Client([
            'base_uri' => 'http://localhost:8000/api/',
            'exceptions' => false
        ]);

        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    protected function tearDown() {

        parent::tearDown();


        $em = $this->entityManager;

        $query = $em->createQuery('DELETE App:FootballTeam ft WHERE 1 = 1');
        $query->execute();

        $query = $em->createQuery('DELETE App:FootballLeague ft WHERE 1 = 1');
        $query->execute();

//        $query = $em->createQuery('DELETE App:User ft WHERE 1 = 1');
//        $query->execute();

        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null; // avoid memory leaks
    }
}