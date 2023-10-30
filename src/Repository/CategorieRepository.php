<?php

namespace App\Repository;

use App\Entity\Categorie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Categorie>
 *
 * @method Categorie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Categorie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Categorie[]    findAll()
 * @method Categorie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategorieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Categorie::class);
    }

    public function add(Categorie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Categorie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    

    /**
     * Retourne toutes les categories triées sur le nom de la categories
     * @param type $ordre
     * @return Categorie[]
     */
    public function findAllOrderByName($ordre): array{
        return $this->createQueryBuilder('c')
                ->leftjoin('c.formations', 'f')
                ->orderBy('c.name', $ordre)
                ->getQuery()
                ->getResult();
    }
    
    /**
     * Retourne la liste des catégories des formations d'une playlist
     * @param type $idPlaylist
     * @return array
     */
    public function findAllForOnePlaylist($idPlaylist): array{
        return $this->createQueryBuilder('c')
                ->join('c.formations', 'f')
                ->join('f.playlist', 'p')
                ->where('p.id=:id')
                ->setParameter('id', $idPlaylist)
                ->orderBy('c.name', 'ASC')   
                ->getQuery()
                ->getResult();        
    }    

    /**
     * Enregistrements dont un champ contient une valeur
     * ou tous les enregistrements si la valeur est vide
     * @param type $champ
     * @param type $valeur
     * @return Categorie[]
     */
    public function findByContainValue($champ, $valeur): array{
        if($valeur==""){
            return $this->findAllOrderByName('ASC');
        }
        return $this->createQueryBuilder('c')
                ->where('c.'.$champ.' LIKE :valeur')
                ->orderBy('c.name', 'DESC')
                ->setParameter('valeur', '%'.$valeur.'%')
                ->getQuery()
                ->getResult();
    }

    /**
     * Retourne toutes les categories triées sur la quantité de formation
     * @param type $ordre
     * @return Categorie[]
     */
    public function findAllOrderByAmount($ordre): array{
        return $this->createQueryBuilder('c')
                ->leftjoin('c.formations', 'f')
                ->groupBy('c.id')
                ->orderBy('count(f.id)', $ordre)
                ->getQuery()
                ->getResult();
    }

    /**
     * Ajoute la formation à la catégorie
     *
     * @param type $id_formation
     * @param type $id_categorie
     * @return void
     */
    public function addFormationCategorie($id_formation, $id_categorie): void{
        $query = "INSERT INTO formation_categorie (formation_id, categorie_id)
         VALUE ($id_formation, $id_categorie);";
        try {
            $conn = $this->getEntityManager()->getConnection();
            $conn->executeQuery($query);
        } catch(\Exception $e){
            dd( $e);
        }
    }

    /**
     * Supprime la formation de la catégorie
     *
     * @param [type] $id_formation
     * @param [type] $id_categorie
     * @return void
     */
    public function delFormationCategorie($id_formation, $id_categorie): void{
        $query = "DELETE FROM  formation_categorie
         WHERE formation_id = $id_formation and categorie_id = $id_categorie;";
        try {
            $conn = $this->getEntityManager()->getConnection();
            $conn->executeQuery($query);
        } catch(\Exception $e){
            dd( $e);
        }
    }

}
