<?php

namespace App\Controller\Admin;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCategoriesController extends AbstractController
{
    const PAGE_CATEGORIES = "pages/admin/categories.html.twig";

    const PAGE_CATEGORIE = "pages/admin/categorie.html.twig";
    
    /**
     *
     * @var CategorieRepository
     */
    private $categorieRepository;
    
    public function __construct(CategorieRepository $categorieRepository) {
        $this->categorieRepository = $categorieRepository;
    }
    
    /**
     * @Route("/admin/categories", name="admin.categories")
     * @return Response
     */
    public function index(): Response{
        $categories = $this->categorieRepository->findAllOrderByName('ASC');
        return $this->render(self::PAGE_CATEGORIES, [
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/admin/categories/tri/{champ}/{ordre}", name="admin.categories.sort")
     * @param type $champ
     * @param type $ordre
     * @return Response
     */
    public function sort($champ, $ordre): Response{
        if($champ == "name"){
            $categories = $this->categorieRepository->findAllOrderByName($ordre);
        }
        if($champ == "nombre"){
            $categories = $this->categorieRepository->findAllOrderByAmount($ordre);
        }
        return $this->render(self::PAGE_CATEGORIES, [
            'categories' => $categories
        ]);
    }
    
    /**
     * @Route("/admin/categories/recherche/{champ}/{table}", name="admin.categories.findallcontain")
     * @param type $champ
     * @param Request $request
     * @param type $table
     * @return Response
     */
    public function findAllContain($champ, Request $request, $table=""): Response{
        $valeur = $request->get("recherche");
        $categories = $this->categorieRepository->findByContainValue($champ, $valeur, $table);
        return $this->render(self::PAGE_CATEGORIES, [
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => $table
        ]);
    }

    /**
     * @Route("/admin/categorie/suppr/{id}", name="admin.categorie.suppr")
     * @param Playlist $playlist
     * @return Response
     */
    public function suppr(Categorie $categorie): Response
    {
        if(count($categorie->getFormations()) > 0){
            $this->addFlash(
                'alert',
                'Vous ne pouvez pas supprimer la categorie ' . $categorie->getName() 
                . ' car elle contient des formations'
            );
            return $this->redirectToRoute('admin.categories');
        }
        
        $this->categorieRepository->remove($categorie, true);
        $this->addFlash(
            'alert',
            'Suppresion de la categorie ' . $categorie->getName() . " prise en compte"
        );
        return $this->redirectToRoute('admin.categories');
    }

    /**
     * @Route("/admin/categorie/edit/{id}", name="admin.categorie.edit")
     * @param Categorie $categorie
     * @param Request $request
     * @return Response
     */
    public function edit(Categorie $categorie, Request $request): Response
    {
        $formationsIni = $categorie->getFormations()->toArray();
        $formCategorie = $this->createForm(CategorieType::class, $categorie);
        $formCategorie->handleRequest($request);
        if ($formCategorie->isSubmitted() && $formCategorie->isValid()){
            $this->categorieRepository->add($categorie, true);
            $formations = $categorie->getFormations()->toArray();
            foreach($formations as $formation){
                if(!in_array($formation, $formationsIni)){
                    $this->categorieRepository->addFormationCategorie($formation->getId(), $categorie->getId());
                }
            }
            foreach($formationsIni as $formation){
                if(!in_array($formation, $formations)){
                    $this->categorieRepository->delFormationCategorie($formation->getId(), $categorie->getId());
                }
            }
            $this->addFlash(
                'success',
                'Modification de la categorie ' . $categorie->getName() . " prise en compte"
            );
            return $this->redirectToRoute('admin.categories');
        }

        return $this->render(self::PAGE_CATEGORIE, [
            'categorie' => $categorie,
            'formCategorie' => $formCategorie->createView()
        ]);
    }

    /**
     * @Route("/admin/categorie/ajout", name="admin.categorie.ajout")
     * @param Request $request
     * @return Response
     */
    public function ajout(Request $request): Response
    {
        $categorie = new Categorie();
        $formCategorie = $this->createForm(CategorieType::class, $categorie);

        $formCategorie->handleRequest($request);
        if ($formCategorie->isSubmitted() && $formCategorie->isValid()){
            $this->categorieRepository->add($categorie, true);
            $formations = $categorie->getFormations()->toArray();
            foreach($formations as $formation) {
                $this->categorieRepository->addFormationCategorie($formation->getId(), $categorie->getId());
            }
            $this->addFlash(
                'success',
                'Ajout de la categorie ' . $categorie->getName() . " prise en compte"
            );
            return $this->redirectToRoute('admin.categories');
        }

        return $this->render(self::PAGE_CATEGORIE, [
            'categorie' => $categorie,
            'formCategorie' => $formCategorie->createView()
        ]);
    }
}
