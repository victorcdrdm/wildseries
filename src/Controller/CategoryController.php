<?php


namespace App\Controller;

use App\Entity\Program;
use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/categories", name="category_")
 */

class CategoryController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        $categories = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findAll();
        return $this->render('category/index.html.twig',[
            'categories' => $categories
        ]);
    }

    /**
     * @route("/{category}", methods={"GET"}, name="show")
     * @param string $category
     * @return Response
     */
    public function show(string $category): Response
    {
        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findOneBy(['name' => $category]);

        if(!$category) {
            throw $this->createNotFoundException('No category : '.$category.' found in category table'
            );
        }
        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findBy(
                ['category' => $category],
                ['id' => 'DESC'],
                3
            );
        if (!$programs){
            throw $this->createNotFoundException(
                'No program with category name : ' .$category. ' found in program\'s table.'
            );
        }
        return $this->render('category/show.html.twig', [
            'category' => $category,
            'programs' => $programs,
        ]);
    }
}