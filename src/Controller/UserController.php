<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="app_user")
     */
    public function index(Request $request,UserPasswordHasherInterface $encoder,EntityManagerInterface $manager
    ): Response
    {
        $user=new User();
        $formRegistration=$this->createForm(RegistrationFormType:: class,$user);
        $formRegistration->handleRequest($request);
        if($formRegistration->isSubmitted()and$formRegistration->isValid()){
            $password=$encoder->hashPassword($user,$user->getPassword());
            dd($password);
        }
        return $this->render('user/index.html.twig', [
            "formRegistration"=>$formRegistration->createView(),
        ]);
    }
}
