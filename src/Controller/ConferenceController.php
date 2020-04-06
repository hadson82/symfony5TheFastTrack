<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentFromType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
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
    private $entityManager;

    public function __construct(Environment $twig, EntityManagerInterface $entityManager)
    {
        $this->twig = $twig;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="homepage")
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
     * @Route("/conference/{slug}", name="conference")
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
        $comment = new Comment();
        $form = $this->createForm(CommentFromType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setConference($conference);

            $this->entityManager->persist($comment);
            $this->entityManager->flush();

            return $this->redirectToRoute('conference', ['slug' => $conference->getSlug()]);
        }

        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $commentRepository->getCommentPaginator($conference, $offset);

        return new Response($this->twig->render('conference/show.html.twig', [
            'conference' => $conference,
            'comments' => $paginator,
            'previous' => $offset - CommentRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + CommentRepository::PAGINATOR_PER_PAGE),
            'comment_form' => $form->createView(),
        ]));
    }

//    /**
//     * @Route("/conference/{slug}", name="conference")
//     * @param Conference $conference
//     * @param CommentRepository $commentRepository
//     * @param ConferenceRepository $conferenceRepository
//     * @return Response
//     */
//    public function showBySlug(Conference $conference, CommentRepository $commentRepository, ConferenceRepository $conferenceRepository)
//    {
//
//        return new Response($this->twig->render('conference/show.html.twig', [
//            'conference' => $conferenceRepository->findAll(),
//            'conference' => $conference,
//            'comments' => $commentRepository->findBy(['conference' => $conference], ['createdAt' => 'DESC']),
//        ]));
//    }


}
