<?php

namespace App\Tests\Validations\Repository;

use App\Entity\Formation;
use Doctrine\ORM\EntityManager;
use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

Class FormationRepositoryTest extends KernelTestCase
{
    private \Doctrine\ORM\EntityManager $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function recupRepository(): FormationRepository
    {
        self::bootKernel();
        $repository = self::getContainer()->get(FormationRepository::class);
        return $repository;
    }

    public function testNbFormations()
    {
        $repository = $this->recupRepository();
        $nbFormation = $repository->count([]);
        $this->assertEquals( 237, $nbFormation);
    }

    public function newFormation() : Formation
    {
        $formation = (new Formation())
            ->setTitle("Un titre")
            ->setDescription("Descrition blabla")
            ->setPublishedAt(new \DateTime("yesterday"));
        return $formation;
    }

    public function testAddFormation()
    {
        $repository = $this->recupRepository();
        $formation = $this->newFormation();
        $nbFormation = $repository->count([]);
        
        $this->entityManager->persist($formation);
        $this->entityManager->flush();
        $this->assertEquals($nbFormation + 1, $repository->count([]), "erreur lors de l'ajout");
        
    }

    public function testSupprFormation()
    {
        $repository = $this->recupRepository();
        
        $nbFormation = $repository->count([]);
        $formation = $repository->findOneBy(['title' => "Un titre"]);
        $formation = $this->entityManager->merge($formation);
        $this->entityManager->remove($formation);
        $this->entityManager->flush();
        $this->assertEquals($nbFormation - 1, $repository->count([]), "erreur lors de la suppresion");
    }

}