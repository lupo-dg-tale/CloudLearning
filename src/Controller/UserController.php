<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EditUserType;
use App\Form\EditPasswordType;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * @Route("user")
 */
class UserController extends AbstractController
{

     /**
     * @Route("/delete/{id}", name="user_delete")
     */
    public function delete(User $user){

        $em = $this->getDoctrine()->getManager();

        $em->remove($user);
        $em->flush();

        return $this->redirectToRoute('user_index');

    }


    /**
     * @Route("/", name="user_index")
     */
    public function index(): Response
    {
        $users = $this->getDoctrine()
            ->getRepository(User::class)
            ->getAll();
        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/ajouter", name="user_add")
     * @Route("/edit{id}", name="user_edit")
     */
    public function addEdit(User $user = null, Request $request, EntityManagerInterface $manager)
    {
        if (!$user) {
            $user = new User();
        }
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($user);
            $manager->flush();
            return $this->redirectToRoute('user_index');
        }
        return $this->render('user/add.html.twig', [
            'RegistrationFormType' => $form->createView(),
            'editMode' => $user->getId() !== null,
            'user' => $user->getNom()
        ]);
    }

    /**
     * @Route("/myaccount", name="user_myaccount")
     */
    public function  myAccount(Request $request, EntityManagerInterface $manager)
    {

        $user = $this->getUser();
        $form = $this->createForm(EditUserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($user); 
            $manager->flush(); 
            return $this->redirectToRoute('user_myaccount');
        }
    
        return $this->render('user/myaccount.html.twig', [
            'EditUserType' => $form->createView(),
            'user' => $this->getUser(),
        ]);
    }


    /**
     * @Route("/editPassword", name="user_edit_password")
     */
    public function editPassword(Request $request, EntityManagerInterface $manager){


        $user = $this->getUser();
        $form = $this->createForm(EditPasswordType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($user); 
            $manager->flush();
            return $this->redirectToRoute('user_myaccount');
        }
    
        return $this->render('user/editPassword.html.twig', [
            'EditPasswordType' => $form->createView(),
            'user' => $this->getUser(),
        ]);
    }

    // /**
    //  * @Route("/editEmail", name="user_editEmail")
    //  */
    // public function editEmail(Request $request, EntityManagerInterface $manager): Response
    // {
    //      $user = $this->getUser();
    //     $form = $this->createForm(EditUserType::class, $user);
    //     $form->handleRequest($request);
    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $manager->persist($user); // équivalent de prepare()
    //         $manager->flush(); // pour valider les changements dans la base de données, il "sait" si il doit UPDATE ou INSERT et ce pour tout les objets persist()
    //         return $this->redirectToRoute('user_myaccount');
    //     }
    //     $user = new User();
    //     return $this->render('user/show.html.twig', [
    //         'user' => $user,
    //     ]);
    // }



    /**
     * @Route("/{id}", name="user_show", methods="GET")
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', ['user' => $user]);
    }

}
