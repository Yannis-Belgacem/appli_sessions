<?php

namespace App\Controller;

use App\Entity\Session;
use App\Entity\Programme;
use App\Entity\Stagiaire;
use App\Form\SessionFormType;
use App\Form\ProgrammeFormType;
use App\Form\StagiaireSessionFormType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class SessionController extends AbstractController
{


    /**
     * @Route("/session_list", name="session_list")
     * @IsGranted("ROLE_USER")
     */
    public function listSessions(Request $request, PaginatorInterface $paginator)
    {
        $sessions = $this->getDoctrine()
                    ->getRepository(Session::class)
                    ->getAllSessions();
                    $sessions = $paginator->paginate(
                        $sessions, // Requête contenant les données à paginer 
                        $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
                        6 // Nombre de résultats par page
                    );
        return $this->render('session/listSession.html.twig', [
            'sessions' => $sessions,
        ]);
    }

    /**
     * @Route("/session_add", name="session_add")
     * @Route("/{id}/session_edit", name="session_edit")
     * @IsGranted("ROLE_ADMIN")
     */
    public function addSession(Session $session = null, Request $request, EntityManagerInterface $manager) {
        if(!$session){
            $session = new Session();
        }
        $form = $this->createForm(SessionFormType::class, $session);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            //$session = $form->getData();
            $manager->persist($session);
            $manager->flush();
            $this->addFlash('success', 'Session créée');

            return $this->redirectToRoute('session_list');
        }
        return $this->render('session/addSession.html.twig', [
           'sessionFormType' => $form->createView(),
           'editSession' => $session->getID() !== null,
           $this->redirectToRoute('session_list')
        ]);
    }

    /**
     * @Route("/{id}/session_delete", name="session_delete")
     * @IsGranted("ROLE_ADMIN")
     */
    public function deleteSession(Session $session, EntityManagerInterface $manager){       
        $manager->remove($session);
        $manager->flush();
        $this->addFlash('success', 'La session a bien été supprimée');
        return $this->redirectToRoute('session_list');
    }


    /**
     * @Route("session/{id}", name="session_show", methods="GET")
     * @IsGranted("ROLE_USER")
     */
    public function showSession(Session $session): Response {
        return $this->render('session/showSession.html.twig', ['session' => $session]);
    }

    /**
     * @Route("/{id}/stagiaire_session_add", name="stagiaire_session_add", methods={"GET", "POST"})
     * @IsGranted("ROLE_USER")
     */
    public function addStagiaireToSession(Session $session = null, Request $request, ManagerRegistry $manager): Response{
 
        $form = $this->createForm(StagiaireSessionFormType::class);
 
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){ 
            dump($form->getData());  
            $em = $manager->getManager();
            foreach ($form->getData() as $stagiaireNew) {
                $session->addStagiaire($stagiaireNew);
            }
            $em->persist($session);
            $em->flush();
            $this->addFlash('success', 'Le/la stagiaire a bien été ajouté');
 
            //return $this->redirectToRoute('session_list');
        }
        return $this->render('session/addStagiaireToSession.html.twig',
        [
            'stagiaireToSessionFormType' => $form->createView(),
            'session' => $session
        ]);
 
    }

    /**
     * @Route("/module_session_add/{id}", name="module_session_add")
     * @IsGranted("ROLE_ADMIN")
     * @ParamConverter("module", options={"id" = "module_id"})     
     */
    public function addModuleToSession(Session $session, Request $request, EntityManagerInterface $em): Response{
        // génère une nouvelle instance Programme, puis le formulaire associé
        $programme = new Programme();
        $form = $this->createForm(ProgrammeFormType::class, $programme);

        $form->handleRequest($request);
        // vérifie la validité du formulaire
        if($form->isSubmitted() && $form->isValid()){ 
        // enregistre l'action en base de données et en notifie l'utilisateur
            if($session->addProgramme($programme)){
                $em->persist($programme);
                $em->flush();
                $this->addFlash('success', 'Le module a bien été ajouté');
            }
            else $this->addFlash("error", "Ce module existe déjà dans cette session");
        }
        // génère la vue correspondant au formulaire
        return $this->render('session/addModuleToSession.html.twig',
        [
            'moduleToSessionFormType' => $form->createView(),
            'session' => $session
        ]);  
    }


     /**
     * @Route("/module_remove/{id}", name="module_remove")
     * @IsGranted("ROLE_ADMIN")
     *  @ParamConverter("programme", options={"id" = "programme_id"})
     * @ParamConverter("module", options={"id" = "module_id"})
     */
    public function removeModuleFromSession(Programme $programme, Session $session, Request $request, EntityManagerInterface $em): Response{

        if($session->removeProgramme($programme)){
            $em->persist($programme);
            $em->flush();
            $this->addFlash('success', 'Le module a bien été enlevé');
        }   
        return $this->redirectToRoute('session_list');
    }

    /**
     * @Route("/{stagiaire_remove/{id}", name="stagiaire_remove")
     * @IsGranted("ROLE_ADMIN")
     * @ParamConverter("session", options={"id" = "session_id"})
     * @ParamConverter("stagiaire", options={"id" = "stagiaire_id"})
     */
    public function removeStagiaireFromSession(Stagiaire $stagiaire, Session $session, Request $request, EntityManagerInterface $em): Response{
        
        if($session->removeStagiaire($stagiaire)){
            $em->persist($stagiaire);
            $em->flush();
            $this->addFlash('success', 'Stagiaire désinscrit(e)');
        }        
        return $this->redirectToRoute('session_list');
    }


}