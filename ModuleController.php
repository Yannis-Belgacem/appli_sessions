<?php

namespace App\Controller;

use App\Entity\Module;
use App\Entity\Categorie;
use App\Form\ModuleFormType;
use App\Form\CategorieFormType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ModuleController extends AbstractController
{

    /**
     * @Route("/module_list", name="module_list")
     * @IsGranted("ROLE_USER")
     */
    public function listModules(Request $request, PaginatorInterface $paginator)
    {
        $modules = $this->getDoctrine()
                    ->getRepository(Module::class)
                    ->getAllModules();
                    $modules = $paginator->paginate(
                        $modules, // Requête contenant les données à paginer 
                        $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
                        6 // Nombre de résultats par page
                    );
        return $this->render('module/listModule.html.twig', [
            'modules' => $modules,
        ]);
    }

    /**
     * @Route("/module_add", name="module_add", methods={"GET", "POST"})
     * @Route("/{id}/module_edit", name="module_edit", methods={"GET", "POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function addModule(Module $module = null, Request $request, EntityManagerInterface $manager) {
        if(!$module){
            $module = new Module();
        }
        $form = $this->createForm(ModuleFormType::class, $module);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            //$module = $form->getData();
            $manager->persist($module);
            $manager->flush();
            $this->addFlash('success', 'Module créé');

            return $this->redirectToRoute('module_list');
        }
        return $this->render('module/addModule.html.twig', [
           'moduleFormType' => $form->createView(),
           'editModule' => $module->getID() !== null,
           $this->redirectToRoute('module_list')
        ]);
    }

    /**
     * @Route("/{id}/module_delete", name="module_delete")
     * @IsGranted("ROLE_ADMIN")
     */
    public function deleteModule(Module $module, EntityManagerInterface $manager){
        $manager->remove($module);
        $manager->flush();

        return $this->redirectToRoute('module_list');
    }

    /**
     * @Route("module/{id}", name="module_show", methods="GET")
     * @IsGranted("ROLE_USER")
     */
    public function showModule(Module $module): Response {
        return $this->render('module/showModule.html.twig', ['module' => $module]);
    }

    /**
     * @Route("/categorie_add", name="categorie_add", methods={"GET", "POST"})
     * @Route("/{id}/categorie_edit", name="categorie_edit", methods={"GET", "POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function addCategorie(Categorie $categorie = null, Request $request, EntityManagerInterface $manager) {
        if(!$categorie){
            $categorie = new Categorie();
        }
        $form = $this->createForm(CategorieFormType::class, $categorie);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            //$categorie = $form->getData();
            $manager->persist($categorie);
            $manager->flush();
            $this->addFlash('success', 'Catégorie créée');

            return $this->redirectToRoute('module_list');
        }
        return $this->render('categorie/addCategorie.html.twig', [
           'categorieFormType' => $form->createView(),
           'editCategorie' => $categorie->getID() !== null,
           $this->redirectToRoute('module_list')
        ]);
    }

    /**
     * @Route("/{id}/categorie_delete", name="categorie_delete")
     * @IsGranted("ROLE_USER")
     */
    public function deleteCategorie(Categorie $categorie, EntityManagerInterface $manager){
        $manager->remove($categorie);
        $manager->flush();

        return $this->redirectToRoute('categorie_list');
    }

    /**
     * @Route("categorie/{id}", name="categorie_show", methods="GET")
     * @IsGranted("ROLE_USER")
     */
    public function showCategorie(Categorie $categorie): Response {
        return $this->render('categorie/show.html.twig', ['categorie' => $categorie]);
    }

}