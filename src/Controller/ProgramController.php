<?php
namespace App\Controller;

use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

        return $this->render('program/index.html.twig', [
            'programs' => $programs
        ]);
    }

    /**
     * @Route("/show/{id}", requirements={"page"="\d+"}, methods={"GET"}, name="show")
     * @param int $id
     * @return Response
     */
    public function show(int $id): Response
    {
        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(['id' => $id]);

        if (!$program) {
            throw $this->createNotFoundException(
                'No program with id : '.$id.' found in program\'s table.'
            );
        }

        $seasons = $this->getDoctrine()
            ->getRepository(Season::class)
            ->findBy(['program' => $program]);


        return $this->render('program/show.html.twig', [
            'program' => $program,
            'seasons'  => $seasons,
        ]);
    }

    /**
     * @Route("/{programId}/seasons/{seasonId<\d+>}", methods={"GET"}, name="season_show")
     */
    public function showSeason(int $programId, int $seasonId): Response
    {
        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(['id' => $programId]);

        if (!$program) {
            throw $this->createNotFoundException(
                'No program with id : '.$programId.' found in program\'s table.'
            );
        }


        $season = $this->getDoctrine()
            ->getRepository(Season::class)
            ->findOneBy(['program' => $programId, 'id'=> $seasonId]);

        if (!$season) {
            throw $this->createNotFoundException(
                'No season with id : '.$seasonId.' found for program with id ' .$programId.' in season\'s table.'
            );
        }
        $episodes = $this->getDoctrine()
            ->getRepository(Episode::class)
            ->findBy(['season' => $season ]);

        return $this->render('program/season_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episodes' => $episodes,
        ]);
    }

}