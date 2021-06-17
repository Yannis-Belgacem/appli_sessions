<?php

namespace App\Controller;

use App\Entity\Stagiaire;
use App\Form\StagiaireFormType;
use App\Form\StagiaireSearchType;
use App\Repository\StagiaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StagiaireController extends AbstractController
{
    /**
     * @Route("/stagiaire", name="stagiaire")
     * @IsGranted("ROLE_USER")
     */
    
    public function index()
    {
        return $this->render('stagiaire/index.html.twig', [
            'controller_name' => 'StagiaireController',
        ]);
    }

    /**
     * @Route("/stagiaire_list", name="stagiaire_list")
     * @IsGranted("ROLE_USER")
     */
    public function listStagiaires(Request $request, PaginatorInterface $paginator)
    {
        $stagiaires = $this->getDoctrine() //
                      //appel de la classe Stagiaire
                    ->getRepository(Stagiaire::class) 
                    // appel d'une fonction personnalisée qui sélectionne tous les stagiaires,
                    // depuis  Repository/StagaireRepository.php
                    ->getAllStagiaires(); 
                    $stagiaires = $paginator->paginate(
                        // Requête contenant les données à paginer
                        $stagiaires, 
                        // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
                        $request->query->getInt('page', 1),
                        // Nombre de résultats par page
                        10
                    );
        // destination de la page à afficher
        return $this->render('stagiaire/listStagiaire.html.twig', [
            'stagiaires' => $stagiaires,
        ]);
    }

    /**
     * @Route("/stagiaire_list", name="stagiaire_list")
     * @IsGranted("ROLE_USER")
     */
    public function search(Request $request, StagiaireRepository $repo, PaginatorInterface $paginator) {
        // appel du formulaire de recherche requis
        $searchForm = $this->createForm(StagiaireSearchType::class);
        $searchForm->handleRequest($request);
        
        $donnees = $repo->findAll();
        // vérification de la validté du formulaire, avant son traitement
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            // appel de la fonction getNom() depuis l'entité Stagaire
            $name = $searchForm->getData()->getNom();
            $donnees = $repo->search($name);

            // message flash d'erreur si aucun nom ne correspond à la recherche
            if ($donnees == null) {
                $this->addFlash('erreur', 'Aucun stagiaire ne corrsepond au nom entré');
           
            }
            
    }

     // Paginate the results of the query
     $stagiaires = $paginator->paginate(
        // Doctrine Query, not results
        $donnees,
        // Define the page parameter
        $request->query->getInt('page', 1),
        // Items per page
        10
    );

        return $this->render('stagiaire/listStagiaire.html.twig',[
            'stagiaires' => $stagiaires,
            'searchForm' => $searchForm->createView()
        ]);
    }

    /**
     * @Route("/stagiaire_add", name="stagiaire_add", methods={"GET", "POST"})
     * @Route("/{id}/stagiaire_edit", name="stagiaire_edit", methods={"GET", "POST"})
     * @IsGranted("ROLE_USER")
     */
    public function addStagiaire(Stagiaire $stagiaire = null, Request $request, EntityManagerInterface $manager) {
        if(!$stagiaire){
            $stagiaire = new Stagiaire();
        }
        $form = $this->createForm(StagiaireFormType::class, $stagiaire);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            //$stagiaire = $form->getData();
            $manager->persist($stagiaire);
            $manager->flush();
            $this->addFlash('success', 'Stagiaire créé(e)');

            return $this->redirectToRoute('stagiaire_list');
        }
        return $this->render('stagiaire/addStagiaire.html.twig', [
           'stagiaireFormType' => $form->createView(),
           'editStagiaire' => $stagiaire->getID() !== null,
           $this->redirectToRoute('stagiaire_list')
        ]);
     }

    /**
     * @Route("/{id}/stagiaire_delete", name="stagiaire_delete")
     * @IsGranted("ROLE_USER")
     */
    public function deleteStagiaire(Stagiaire $stagiaire, EntityManagerInterface $manager){
        $manager->remove($stagiaire);
        $manager->flush();

        return $this->redirectToRoute('stagiaire_list');
    }

    /**
     * @Route("stagiaire/{id}", name="stagiaire_show", methods="GET")
     * @IsGranted("ROLE_USER")
     */
    public function showStagiaire(Stagiaire $stagiaire): Response {
        return $this->render('stagiaire/showStagiaire.html.twig', ['stagiaire' => $stagiaire]);
    }

}