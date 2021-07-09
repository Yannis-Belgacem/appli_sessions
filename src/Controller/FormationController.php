<?php

namespace App\Controller;


use App\Entity\Salle;
use App\Entity\Centre;
use SalleCentreFormType;
use App\Entity\Formateur;
use App\Form\CentreFormType;
use App\Form\FormateurFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FormationController extends AbstractController
{
    /**
     * @Route("/centre_list", name="centre_list")
     * @IsGranted("ROLE_USER")
     */
    public function listCentres()
    {
        $centres = $this->getDoctrine()
                    ->getRepository(Centre::class)
                    ->getAllCentres();
        return $this->render('centre/listCentre.html.twig', [
            'centres' => $centres,
        ]);
    }

    /**
     * @Route("/formateur_add", name="formateur_add", methods={"GET", "POST"})
     * @Route("/{id}/formateur_edit", name="formateur_edit", methods={"GET", "POST"})
     * @IsGranted("ROLE_USER")
     */
    public function addFormateur(Formateur $formateur = null, Request $request, EntityManagerInterface $manager) {
        if(!$formateur){
            $formateur = new Formateur();
        }
        $form = $this->createForm(FormateurFormType::class, $formateur);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            //$formateur = $form->getData();
            $manager->persist($formateur);
            $manager->flush();
            $this->addFlash('success', 'Formateur créé');

            return $this->redirectToRoute('formateur_list');
        }
        return $this->render('formateur/addFormateur.html.twig', [
           'formateurFormType' => $form->createView(),
           'editFormateur' => $formateur->getID() !== null,
           $this->redirectToRoute('session_list')
        ]);
     }

    /**
     * @Route("/{id}/deleteFormateur", name="formateur_delete")
     * @IsGranted("ROLE_USER")
     */
    public function deleteFormateur(Formateur $formateur, EntityManagerInterface $manager){
        $manager->remove($formateur);
        $manager->flush();

        return $this->redirectToRoute('formateur_list');
    }

    /**
     * @Route("/formateur_list", name="formateur_list")
     * @IsGranted("ROLE_USER")
     */
    public function listFormateurs()
    {
        $formateurs = $this->getDoctrine()
                    ->getRepository(Formateur::class)
                    ->getAllFormateurs();
        return $this->render('formateur/listFormateur.html.twig', [
            'formateurs' => $formateurs,
        ]);
    }

    /**
     * @Route("/centre_add", name="centre_add", methods={"GET", "POST"})
     * @Route("/{id}/centre_edit", name="centre_edit", methods={"GET", "POST"})
     * @IsGranted("ROLE_SUPER_ADMIN")
     */
    public function addCentre(Centre $centre = null, Request $request, EntityManagerInterface $manager) {
        if(!$centre){
            $centre = new Centre();
        }
        $form = $this->createForm(CentreFormType::class, $centre);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            //$centre = $form->getData();
            $manager->persist($centre);
            $manager->flush();
            $this->addFlash('success', 'Centre de formation ajouté');

            return $this->redirectToRoute('centre_list');
        }
        return $this->render('centre/addCentre.html.twig', [
           'centreFormType' => $form->createView(),
           'editCentre' => $centre->getID() !== null,
           $this->redirectToRoute('session_list')
        ]);
     }

    /**
     * @Route("/{id}/deleteCentre", name="centre_delete")
     * @IsGranted("ROLE_SUPER_ADMIN")
     */
    public function deleteCentre(Centre $centre, EntityManagerInterface $manager){
        $manager->remove($centre);
        $manager->flush();

        return $this->redirectToRoute('centre_list');
    }

    /**
     * @Route("centre/{id}", name="centre_show", methods="GET")
     * @IsGranted("ROLE_USER")
     */
    public function showCentre(Centre $centre): Response {
        return $this->render('centre/showCentre.html.twig', ['centre' => $centre]);
    }

        /**
     * @Route("/salle_add", name="salle_add", methods={"GET", "POST"})
     * @Route("/{id}/salle_edit", name="salle_edit", methods={"GET", "POST"})
     * @IsGranted("ROLE_SUPER_ADMIN")
     */
    public function addSalle(Salle $salle = null, Request $request, EntityManagerInterface $manager) {
        if(!$salle){
            $salle = new Salle();
        }
        $form = $this->createForm(SalleFormType::class, $salle);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            //$salle = $form->getData();
            $manager->persist($salle);
            $manager->flush();
            $this->addFlash('success', 'Salle créée');

            return $this->redirectToRoute('salle_list');
        }
        return $this->render('salle/addSalle.html.twig', [
           'salleFormType' => $form->createView(),
           'editSalle' => $salle->getID() !== null,
           $this->redirectToRoute('session_list')
        ]);
     }

    /**
     * @Route("/{id}/stagiaire_session_add", name="stagiaire_session_add", methods={"GET", "POST"})
     * @IsGranted("ROLE_USER")
     */
    public function addSalleToCentre(Centre $centre, Salle $salle = null, Request $request, ManagerRegistry $manager): Response{
 
        $form = $this->createForm(SalleCentreFormType::class);
 
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){ 
            dump($form->getData());  
            $em = $manager->getManager();
            foreach ($form->getData() as $sallenew) {
                $centre->addSalle($salleNew);
            }
            $em->persist($centre);
            $em->flush();
            $this->addFlash('success', 'La salle a bien été assignée');
 
            //return $this->redirectToRoute('centre_list');
        }
        return $this->render('session/addSalleToCentre.html.twig',
        [
            'salleToCentreFormType' => $form->createView(),
            'centre' => $centre
        ]);
 
    }

    /**
     * @Route("/{id}/deleteSalle", name="salle_delete")
     * @IsGranted("ROLE_SUPER_ADMIN")
     */
    public function deleteSalle(Salle $salle, EntityManagerInterface $manager){
        $manager->remove($salle);
        $manager->flush();

        return $this->redirectToRoute('salle_list');
    }

}