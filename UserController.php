<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AdminUserFormType;
use App\Form\UserSearchType;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/register", name="app_register", methods={"GET", "POST"})
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('user_list');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/user_list", name="user_list")
     * @IsGranted("ROLE_USER")
     */
    public function listUsers(Request $request, PaginatorInterface $paginator)
    {
        $users = $this->getDoctrine()
                    ->getRepository(User::class)
                    ->getAllUsers();
                    $users = $paginator->paginate(
                        $users, // Requête contenant les données à paginer 
                        $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
                        10 // Nombre de résultats par page
                    );
        return $this->render('user/listUser.html.twig', [
            'users' => $users,
        ]);
    }

        /**
     * @Route("/user_add", name="user_add", methods={"GET", "POST"})
     * @Route("/{id}/user_edit", name="user_edit", methods={"GET", "POST"})
     * @IsGranted("ROLE_USER")
     */
    public function editUser(User $user = null, Request $request, EntityManagerInterface $manager) {

        $form = $this->createForm(AdminUserFormType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            //$user = $form->getData();
            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('user_list');
        }
        return $this->render('user/editUser.html.twig', [
           'userFormType' => $form->createView(),
           'editUser' => $user->getID() !== null,
           $this->redirectToRoute('user_list')
        ]);
     }

        /**
     * @Route("/{id}/user_delete", name="user_delete")
     * @IsGranted("ROLE_USER")
     */
    public function deleteUser(User $user, EntityManagerInterface $manager){
        $manager->remove($user);
        $manager->flush();
        $this->addFlash('success', 'Utilisateur supprimé !');

        return $this->redirectToRoute('user_list');
    }

    /**
     * @Route("user/{id}", name="user_show", methods="GET")
     * @IsGranted("ROLE_USER")
     */
    public function showUser(User $user): Response {
        return $this->render('user/showUser.html.twig', ['user' => $user]);
    }
}