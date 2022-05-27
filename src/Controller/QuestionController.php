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
        $cmnts=$CR->findAll();
        $quests=$QR->findAll();
        return $this->render('question/view.html.twig', [
            "quests"=>$quests,"comment"=>$cmnts
        ]);
    }
    /**
     * @Route("/Comment", name="Comment")
     * IsGranted("ROLE_USER")
     */
    public function addComment(Request $request,UserRepository $UR,QuestionRepository $QR,EntityManagerInterface $manager): Response {
        $comment= new Comments();
        $user = new User();
        $quest= new Question();
        $user=$UR->find($request->query->get('user'));
        $comment->setUser($user);
        $quest=$QR->find($request->query->get('quest'));
        $comment->setComment($request->query->get('comment'));
        $comment->setQuestion($quest);
        $manager->persist($comment);
        $manager->flush();
        return $this->redirect("/ViewQuests");
    }
    /**
     * @Route("/create", name="app_question")
     * IsGranted("ROLE_USER")
     */
    public function create(Request $request,EntityManagerInterface $manager): Response
    {
        $question= new Question();
        $formquest=$this->createForm(QuestionType:: class,$question);
        $formquest->handleRequest($request);
        if($formquest->isSubmitted()and$formquest->isValid()){
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
