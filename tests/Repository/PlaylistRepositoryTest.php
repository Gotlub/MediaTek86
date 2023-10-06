<?php

namespace App\Tests\Validations\Repository;

use App\Entity\Playlist;
use Doctrine\ORM\EntityManager;
use App\Repository\PlaylistRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

Class PlaylistRepositoryTest extends KernelTestCase
{
    private \Doctrine\ORM\EntityManager $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function recupRepository(): PlaylistRepository
    {
        self::bootKernel();
        $repository = self::getContainer()->get(PlaylistRepository::class);
        return $repository;
    }

    public function testNbPlaylist()
    {
        $repository = $this->recupRepository();
        $nbPLaylist = $repository->count([]);
        $this->assertEquals( 27, $nbPLaylist);
    }

    public function newPlaylist() : Playlist
    {
        $formation = (new Playlist())
            ->setName("Un nom");
        return $formation;
    }

    public function testAddPlaylist()
    {
        $repository = $this->recupRepository();
        $playlist = $this->newPlaylist();
        $nbPlaylist= $repository->count([]);
        
        $this->entityManager->persist($playlist);
        $this->entityManager->flush();
        $this->assertEquals($nbPlaylist + 1, $repository->count([]), "erreur lors de l'ajout");
        
    }

    public function testSupprPlaylist()
    {
        $repository = $this->recupRepository();
        
        $nbPlaylist = $repository->count([]);
        $playlist = $repository->findOneBy(['name' => "Un nom"]);
        $playlist = $this->entityManager->merge($playlist);
        $this->entityManager->remove($playlist);
        $this->entityManager->flush();
        $this->assertEquals($nbPlaylist - 1, $repository->count([]), "erreur lors de la suppresion");
    }

}