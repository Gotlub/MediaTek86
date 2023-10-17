<?php

namespace App\Tests\Validations\Repository;

use App\Entity\Categorie;
use Doctrine\ORM\EntityManager;
use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

Class CategorieRepositoryTest extends KernelTestCase
{
    private \Doctrine\ORM\EntityManager $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function recupRepository(): CategorieRepository
    {
        self::bootKernel();
        $repository = self::getContainer()->get(CategorieRepository::class);
        return $repository;
    }

    public function testNbCategorie()
    {
        $repository = $this->recupRepository();
        $nbCategorie = $repository->count([]);
        $this->assertEquals( 9, $nbCategorie);
    }

    public function newCategorie() : Categorie
    {
        $categorie = (new Categorie())
            ->setName("Un nom");
        return $categorie;
    }

    public function testAddCategorie()
    {
        $repository = $this->recupRepository();
        $categorie = $this->newCategorie();
        $nbCategorie = $repository->count([]);
        
        $this->entityManager->persist($categorie);
        $this->entityManager->flush();
        $this->assertEquals($nbCategorie + 1, $repository->count([]), "erreur lors de l'ajout");
        
    }

    public function testSupprCategorie()
    {
        $repository = $this->recupRepository();
        
        $nbCategorie = $repository->count([]);
        $categorie = $repository->findOneBy(['name' => "Un nom"]);
        $categorie = $this->entityManager->merge($categorie);
        $this->entityManager->remove($categorie);
        $this->entityManager->flush();
        $this->assertEquals($nbCategorie - 1, $repository->count([]), "erreur lors de la suppresion");
    }

}