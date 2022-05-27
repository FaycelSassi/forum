<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/register", name="create")
     */
    public function index(Request $request,UserPasswordHasherInterface $encoder,EntityManagerInterface $manager
    ): Response
    {
        $user=new User();
        $formRegistration=$this->createForm(RegistrationFormType:: class,$user);
        $formRegistration->handleRequest($request);
        if($formRegistration->isSubmitted()and$formRegistration->isValid()){
            $password=$encoder->hashPassword($user,$user->getPassword());
            $user->setPassword($password);
            $user->setRoles(['ROLE_USER']);
            $manager->persist($user);
            $manager->flush();
        }
        return $this->render('user/index.html.twig', [
            "formRegistration"=>$formRegistration->createView(),
        ]);
    }
    /**
     * @Route("/", name="login")
     */
    public function login(AuthenticationUtils $AU){
        $error=$AU->getLastAuthenticationError();
        $username=$AU->getLastUsername();
        //if($username){
         //   return $this->redirect("/ViewQuests");
        //}
        return $this->render('main/main.html.twig', [
            "error"=>$error,"username"=>$username
        ]);
    }
    /**
     * @Route("/logout", name="logout")
     */
    public function logout(){}
}
