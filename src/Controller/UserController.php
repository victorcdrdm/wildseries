<?php

namespace App\Controller;


use App\Entity\Comment;
use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/my-profile", name="user")
     */
    public function index(): Response
    {

        $user = $this->getUser();
        $comments = $this->getDoctrine()->getRepository(Comment::class)->findBy(['author' => $this->getUser()]);


        return $this->render('user/index.html.twig', [
            'user' => $user,
            'comments' => $comments
        ]);
    }
}
