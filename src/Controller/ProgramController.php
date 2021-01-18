<?php
namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\ProgramType;
use App\Service\Slugify;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


/**
 * @Route("/programs", name="program_")
 */

class ProgramController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */

    public function index(): Response
    {

        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findAll();
        return $this->render('program/index.html.twig', [
            'programs' => $programs
        ]);
    }

    /**
     * @Route ("/new", name="new")
     */
    public function new(Request $request, Slugify $slugify, MailerInterface $mailer): Response
    {

        $program = new Program();

        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $entityManager = $this->getDoctrine()->getManager();

            $slug = $slugify->generate($program->getTitle());
            $program->setSlug($slug);
            $program->setOwner($this->getUser());

            $entityManager->persist($program);
            $entityManager->flush();

            $email = (new Email())
                ->from($this->getParameter('mailer_from'))
                ->to('your_email@example.com')
                ->subject('Une nouvelle série vient d\'être publiée !')
                ->html($this->renderView('program/newProgramEmail.html.twig', ['program' => $program]));
            $mailer->send($email);

            return $this->redirectToRoute('program_index');
        }

        return $this->render('program/new.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/{slug}", methods={"GET"}, name="show")
     * @ParamConverter ("program", class="App\Entity\Program", options={"mapping": {"slug": "slug"}})
     * @return Response
     */
    public function show(Program $program): Response
    {

        return $this->render('program/show.html.twig', [
            'program' => $program,
            'seasons'  => $program->getSeason(),
        ]);
    }

    /**
     * @Route("/{slug}/seasons/{season}", name="season_show")
     * @ParamConverter ("program", class="App\Entity\Program", options={"mapping": {"slug": "slug"}})
     * @ParamConverter ("season", class="App\Entity\Season", options={"mapping": {"season": "id"}})
     * @return Response
     */
    public function showSeason(Program $program, Season $season): Response
    {


        $episodes = $this->getDoctrine()
            ->getRepository(Episode::class)
            ->findBy(['season' => $season ]);

        return $this->render('program/season_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episodes' => $episodes,
        ]);
    }

    /**
     * @Route("/{programSlug}/seasons/{seasonId<\d+>}/episodes/{episodeSlug}", methods={"GET", "POST"}, name="episode_show")
     * @ParamConverter("program", class="App\Entity\Program", options={"mapping": {"programSlug": "slug"}})
     * @ParamConverter("season", class="App\Entity\Season", options={"mapping": {"seasonId": "id"}})
     * @ParamConverter("episode", class="App\Entity\Episode", options={"mapping": {"episodeSlug": "slug"}})
     */
    public function showEpisode(Program $program, Season $season, Episode $episode, Request $request, EntityManagerInterface $entityManager): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        $author = $this->getUser();

        if($form->isSubmitted() && $form->isValid()) {
            $comment->setAuthor($author);
            $comment->setEpisode($episode);
            $entityManager->persist($comment);
            $entityManager->flush();

        }

        $comments = $this->getDoctrine()->getRepository(Comment::class)
                    ->findBy(['episode' => $episode]);

        return $this->render('program/episode_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episode' => $episode,
            'comments' => $comments,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{slug}/edit", name="edit", methods={"GET","POST"})
     * @ParamConverter("program", class="App\Entity\Program", options={"mapping": {"slug": "slug"}})
     */
    public function edit(Request $request, Program $program) :Response
    {
        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);
        if (!($this->getUser() == $program->getOwner())) {

            // If not the owner, throws a 403 Access Denied exception

            throw new AccessDeniedException('Only the owner can edit the program!');

        }
            if ($form->isSubmitted() && $form->isValid()) {
                $this->getDoctrine()->getManagers()->flush();

                return $this->redirectToRoute('program_index');
            }

        return $this->render('program/edit.html.twig', [
            'program' => $program,
            'form' => $form->createView(),
        ]);
    }
}
