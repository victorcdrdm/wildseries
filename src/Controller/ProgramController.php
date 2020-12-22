<?php
namespace App\Controller;

use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use App\Form\ProgramType;
use App\Service\Slugify;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
        dd($programs);
        return $this->render('program/index.html.twig', [
            'programs' => $programs
        ]);
    }

    /**
     * @Route ("/new", name="new")
     * @param Request $request
     * @param Slugify $slugify
     * @return Response
     */
    public function new(Request $request, Slugify $slugify): Response
    {

        $program = new Program();
        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){

            $slug = $slugify->generate($program->getTitle());
            $program->setSlug($slug);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($program);
            $entityManager->flush();
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
     * @Route("/{slug}/seasons/{seasonId}/episodes/{episodeSlug}", name="episode_show")
     * @ParamConverter ("program", class="App\Entity\Program", options={"mapping": {"slug": "slug"}})
     * @ParamConverter ("season", class="App\Entity\Season", options={"mapping": {"season": "id"}})
     * @ParamConverter ("episode", class="App\Entity\Episode", options={"mapping": {"episodeSlug": "slug"}})
     * @return Response
     */

    public function showEpisode(Program $program, Season $season, Episode $episode): Response
    {

        return $this->render('program/episode_show.html.twig',[
            'program' => $program,
            'season'  => $season,
            'episode'=> $episode,
        ]);
    }
}
