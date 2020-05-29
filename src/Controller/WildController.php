<?php
// src/Controller/WildController.php
namespace App\Controller;

use App\Entity\Category;
use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

Class WildController extends AbstractController
{
    /**
     * Show all rows from Program’s entity
     *
     * @Route("/wild", name="wild_index")
     * @return Response A response instance
     */
    public function index(): Response
    {
        $programs = $this->getDoctrine()
        ->getRepository(Program::class)
        ->findAll();

        if (!$programs) {
            throw $this->createNotFoundException(
                'No program found in pprogram\'s table.'
            );
        }
        return $this->render('wild/index.html.twig', [
            'website' => 'Wild Séries',
            'programs'=> $programs
        ]);
    }

    /**
     * Getting a program with a formatted slug for title
     *
     * @param string $slug The slugger
     * @Route("/show/{slug<^[a-z0-9-]+$>}", defaults={"slug" = null}, name="show")
     * @return Response
     */
    public function show(?string $slug ): Response
    {
        if (!$slug) {
            throw $this
            ->createNotFoundException('No slug has been sent to find a program in program\'s table');
        }
        $slug = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($slug)), "-")
        );
        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(['title' => mb_strtolower($slug)]);
        if (!$program) {
            throw $this->createNotFoundException(
                'No program with '. $slug . ' title found in program\'s table.'
            );
        }
        return $this->render('wild/show.html.twig', [
            'slug' => $slug,
            'program' => $program
        ]);
    }

    /**
     * Getting all programs sort by category
     *
     * @param string $categoryName
     * @Route("/wild/category/{categoryName<^[a-z0-9-]+$>}", name="show_category")*
     * @return Response
     */
    public function showByCategory(string $categoryName)
    {
        if (!$categoryName) {
            throw $this->createNotFoundException('This category seems to not exists');
        }
        $categoryName = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($categoryName)), "-")
        );
        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findOneBy(['name' => mb_strtolower($categoryName)]);
        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findBy(['category' => $category],
                ['id' => 'DESC'],
                3);
        if (!$category) {
            throw $this->createNotFoundException(
                'The category '. "'".$categoryName."'".' is unknown.'
            );
        }
        if (!$programs)
            throw $this->createNotFoundException('No program found in '. "'".$categoryName."'".'');

        return $this->render('wild/category.html.twig', [
            'programs' => $programs,
            'category' => $category,
            'categoryName' => $categoryName
        ]);
    }

    /**
     * @Route("/showbyprogram/{slug}", defaults={"slug" = null}, name="show_by_program")
     * @return Response
     */
    public function showByProgram(?string $slug ): Response
    {
        if (!$slug) {
            throw $this
                ->createNotFoundException('No slug has been sent to find a program in program\'s table');
        }
        $slug = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($slug)), "-")
        );
        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(['title' => mb_strtolower($slug)]);
        if (!$program) {
            throw $this->createNotFoundException(
                'No program with '. $slug . ' title found in program\'s table.'
            );
        }

        $seasons = $this->getDoctrine()
            ->getRepository(Season::class)
            ->findBy(['program' => $program]);

        return $this->render('wild/showByProgram.html.twig', [
            'program' => $program,
            'seasons' => $seasons
        ]);
    }


    /**
     * @Route("/showbyseason/{id}", name="show_by_season")
     * @param string $id
     * @return Response
     */
    public function showBySeason(string $id): Response
    {
        if (!$id) {
            throw $this
                ->createNotFoundException('No season has been sent to find a program in season\'s table.');
        }

        $season = $this->getDoctrine()
            ->getRepository(Season::class)
            ->findOneBy(['id' => $id]);


        $episodes = $this->getDoctrine()
            ->getRepository(Episode::class)
            ->findBy(['season' => $id], ['id' => 'asc']);

        return $this->render('wild/showBySeason.html.twig', [
            'program' => $season->getProgram(),
            'season' => $season,
            'episodes' => $episodes,

        ]);
    }

    /**
     * @Route("/episode/{id}", name="show_episode")
     */
    public function showEpisode(Episode $episode): Response
    {
        $season = $episode->getSeason();
        $program = $season->getProgram();

        return $this->render('wild/showEpisode.html.twig', [
            'program' => $program,
            'season' => $season,
            'episode' => $episode,
        ]);
    }

}