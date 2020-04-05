<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ConferenceRepository;
use Twig\Environment;
use App\Entity\Conference;
use App\Repository\CommentRepository;

class ConferenceController extends AbstractController
{

    private $twig;

    public function __constructor(Environment $twig)
    {
        $this->twig = $twig;
    }
    /**
     * @Route("/", name="homepage")
     * @param Environment $twig
     * @param ConferenceRepository $conferenceRepository
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function index(ConferenceRepository $conferenceRepository){



        return new Response($this->twig->render('conference/index.html.twig', [
            'conferences' => $conferenceRepository->findAll(),
        ]));

    }

    /**
     * @Route("/conference/{id}", name="conference")
     * @param Request $request
     * @param Conference $conference
     * @param CommentRepository $commentRepository
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function show(Request $request, Conference $conference, CommentRepository $commentRepository)
    {
        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $commentRepository->getCommentPaginator($conference, $offset);

        return new Response($this->twig->render('conference/show.html.twig', [
            'conference' => $conference,
            'comments' => $paginator,
            'previous' => $offset - CommentRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + CommentRepository::PAGINATOR_PER_PAGE),
        ]));
    }

}
