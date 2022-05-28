<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Entity\Question;
use App\Entity\User;
use App\Form\QuestionType;
use App\Repository\UserRepository;
use App\Repository\CommentsRepository;
use App\Repository\QuestionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class QuestionController extends AbstractController
{
    /**
     * @Route("/ViewQuests", name="ViewQuests")
     * IsGranted("ROLE_USER")
     */
    public function index(QuestionRepository $QR,CommentsRepository $CR): Response{
        $user=$this->getUser();
        $cmnts=$CR->findAll();
        $quests=$QR->findAll();
        return $this->render('question/view.html.twig', [
            "quests"=>$quests,"comment"=>$cmnts,"user"=>$user
        ]);
    }
    /**
     * @Route("/ViewDetails", name="ViewDetails")
     * IsGranted("ROLE_USER")
     */
    public function ViewDetails(Request $request,QuestionRepository $QR,CommentsRepository $CR): Response{
        $user=$this->getUser();
        $quest=$QR->find($request->query->get('quest'));
        $cmnts=$CR->findBy(['Question'=>$quest]);
        return $this->render('question/viewComments.html.twig', [
            "quest"=>$quest,"comment"=>$cmnts,"user"=>$user
        ]);
    }
    /**
     * @Route("/deltequest", name="deltequest")
     * IsGranted("ROLE_USER")
     */
    public function deltequest(Request $request,CommentsRepository $CR,QuestionRepository $QR,EntityManagerInterface $manager): Response {
      $quest=$QR->find($request->query->get('quest'));
      $manager->remove($quest);
        $manager->flush();
        return $this->redirect("/ViewQuests");
    }
    /**
     * @Route("/Comment", name="Comment")
     * IsGranted("ROLE_USER")
     */
    public function addComment(Request $request,CommentsRepository $CR,QuestionRepository $QR,EntityManagerInterface $manager): Response {
        $comment= new Comments();
        $quest= new Question();
        $comment->setUser($this->getUser());
        $quest=$QR->find($request->query->get('quest'));
        $comment->setComment($request->query->get('comment'));
        $comment->setQuestion($quest);
        $manager->persist($comment);
        $manager->flush();
        $user=$this->getUser();
        $cmnts=$CR->findBy(['Question'=>$quest]);
        return $this->render('question/viewComments.html.twig', [
            "quest"=>$quest,"comment"=>$cmnts,"user"=>$user
        ]);
    }
    /**
     * @Route("/deletecmnt", name="deletecmnt")
     * IsGranted("ROLE_USER")
     */
    public function deleteComment(Request $request,CommentsRepository $CR,EntityManagerInterface $manager):Response{
        $comment= $CR->find($request->query->get('idcmnt'));
        $manager->remove($comment);
        $manager->flush();
        return $this->redirect("/ViewQuests");
    }

    /**
     * @Route("/createpost", name="createpost")
     * IsGranted("ROLE_USER")
     */
    public function create(Request $request,EntityManagerInterface $manager): Response
    {
        $question= new Question();
        $formquest=$this->createForm(QuestionType:: class,$question);
        $formquest->handleRequest($request);
        if($formquest->isSubmitted() and $formquest->isValid()){

            $question->setUser($this->getUser());
            $manager->persist($question);
            $manager->flush();
            return $this->redirect("/ViewQuests");
        }
        return $this->render('question/index.html.twig', [
            "formquest"=>$formquest->createView(),
        ]);
    }
}
