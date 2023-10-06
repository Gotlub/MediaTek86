<?php

namespace App\Controller\Admin;

use App\Entity\Playlist;
use App\Form\PlaylistType;
use App\Repository\PlaylistRepository;
use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminPlaylistsController extends AbstractController
{
    const PAGE_PLAYLISTS = "pages/admin/playlists.html.twig";

    const PAGE_PLAYLIST = "pages/admin/playlist.html.twig";

    /**
     *
     * @var PlaylistRepository
     */
    private $playlistRepository;
    
    /**
     *
     * @var FormationRepository
     */
    private $formationRepository;
    
    /**
     *
     * @var CategorieRepository
     */
    private $categorieRepository;
    
    public function __construct(PlaylistRepository $playlistRepository,
            CategorieRepository $categorieRepository,
            FormationRepository $formationRepository) {
        $this->playlistRepository = $playlistRepository;
        $this->categorieRepository = $categorieRepository;
        $this->formationRepository = $formationRepository;
    }
    
    /**
     * @Route("/admin/playlists", name="admin.playlists")
     * @return Response
     */
    public function index(): Response{
        $playlists = $this->playlistRepository->findAllOrderByName('ASC');
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::PAGE_PLAYLISTS, [
            'playlists' => $playlists,
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/admin/playlists/tri/{champ}/{ordre}", name="admin.playlists.sort")
     * @param type $champ
     * @param type $ordre
     * @return Response
     */
    public function sort($champ, $ordre): Response{
        if($champ == "name"){
            $playlists = $this->playlistRepository->findAllOrderByName($ordre);
        }
        if($champ == "nombre"){
            $playlists = $this->playlistRepository->findAllOrderByAmount($ordre);
        }
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::PAGE_PLAYLISTS, [
            'playlists' => $playlists,
            'categories' => $categories
        ]);
    }
    
    /**
     * @Route("/admin/playlists/recherche/{champ}/{table}", name="admin.playlists.findallcontain")
     * @param type $champ
     * @param Request $request
     * @param type $table
     * @return Response
     */
    public function findAllContain($champ, Request $request, $table=""): Response{
        $valeur = $request->get("recherche");
        $playlists = $this->playlistRepository->findByContainValue($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::PAGE_PLAYLISTS, [
            'playlists' => $playlists,
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => $table
        ]);
    }

    /**
     * @Route("/admin/playlist/suppr/{id}", name="admin.playlist.suppr")
     * @param Playlist $playlist
     * @return Response
     */
    public function suppr(Playlist $playlist): Response
    {
        if(count($playlist->getFormations()) > 0){
            $this->addFlash(
                'alert',
                'Vous ne pouvez pas supprimer la playlist ' . $playlist->getName() . ' car elle contient des formations'
            );
            return $this->redirectToRoute('admin.playlists');
        }
        
        $this->playlistRepository->remove($playlist, true);
        $this->addFlash(
            'alert',
            'Suppresion de la playlist ' . $playlist->getName() . " prise en compte"
        );
        return $this->redirectToRoute('admin.playlists');
    }

    /**
     * @Route("/admin/playlist/edit/{id}", name="admin.playlist.edit")
     * @param Playlist $playlist
     * @param Request $request
     * @return Response
     */
    public function edit(Playlist $playlist, Request $request): Response
    {
        $formationsIni = $playlist->getFormations()->toArray();
        $formPlaylist = $this->createForm(PlaylistType::class, $playlist);
        $formPlaylist->handleRequest($request);
        if ($formPlaylist->isSubmitted() && $formPlaylist->isValid()){
            $formations = $playlist->getFormations()->toArray();
            foreach($formations as $formation) {
                if(!in_array($formation, $formationsIni)){
                    $formation->setPlaylist($playlist);
                    $this->formationRepository->add($formation, true);
                }
            }
            foreach($formationsIni as $formation){
                if(!in_array($formation, $formations)){
                    $formation->setPlaylist(null);
                    $this->formationRepository->add($formation , true);
                }
            }
            $this->playlistRepository->add($playlist, true);
            $this->addFlash(
                'success',
                'Modification de la playlist ' . $playlist->getName() . " prise en compte"
            );
            return $this->redirectToRoute('admin.playlists');
        }

        return $this->render(self::PAGE_PLAYLIST, [
            'playlist' => $playlist,
            'formPlaylist' => $formPlaylist->createView()
        ]);
    }

    /**
     * @Route("/admin/playlist/ajout", name="admin.playlist.ajout")
     * @param Request $request
     * @return Response
     */
    public function ajout(Request $request): Response
    {
        $playlist = new Playlist();
        $formPlaylist = $this->createForm(PlaylistType::class, $playlist);

        $formPlaylist->handleRequest($request);
        if ($formPlaylist->isSubmitted() && $formPlaylist->isValid()){
            $this->playlistRepository->add($playlist, true);
            $formations = $playlist->getFormations()->toArray();
            foreach($formations as $formation) {
                $formation->setPlaylist($playlist);
                $this->formationRepository->add($formation, true);
            }
            $this->addFlash(
                'success',
                'Ajout de la playlist ' . $playlist->getName() . " prise en compte"
            );
            return $this->redirectToRoute('admin.playlists');
        }

        return $this->render(self::PAGE_PLAYLIST, [
            'playlist' => $playlist,
            'formPlaylist' => $formPlaylist->createView()
        ]);
    }
}
