<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\CommentRepository;
use App\Repository\FigureRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{

    /** @var int  */
    const ITEM_PER_PAGE = 16;

    private PaginatorInterface $paginator;

    public function __construct(PaginatorInterface $paginator)
    {
        $this->paginator = $paginator;
    }

    /**
     * Admin index
     *
     * @IsGranted('ROLE_ADMIN')
     * @Route("/admin", name="app_admin")
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController'
        ]);
    }

    /**
     * Manage users
     *
     * @Route("/admin/users", name="app_admin_users")
     * @param Request $request
     * @param UserRepository $userRepository
     * @return Response
     */
    public function manageUsers(Request $request, UserRepository $userRepository): Response
    {
        $users = $userRepository->findBy([], ['created_at' => 'DESC']);
        $users = $this->paginator->paginate(
            $users,
            $request->query->getInt('page', 1),
            self::ITEM_PER_PAGE
        );
        return $this->render('admin/users.html.twig', [
            'users' => $users,
            'controller_name' => 'AdminController'
        ]);
    }

    /**
     * Manage figures
     *
     * @Route("/admin/comments", name="app_admin_comments")
     * @param Request $request
     * @param CommentRepository $commentRepository
     * @return Response
     */
    public function manageComments(Request $request, CommentRepository $commentRepository): Response
    {
        $comments = $commentRepository->findBy([], ['created_at' => 'DESC']);
        $comments = $this->paginator->paginate(
            $comments,
            $request->query->getInt('page', 1),
            self::ITEM_PER_PAGE
        );
        return $this->render('admin/comments.html.twig', [
            'comments' => $comments,
            'controller_name' => 'AdminController'
        ]);
    }

    /**
     * Manage figures
     *
     * @Route("/admin/figures", name="app_admin_figures")
     * @param Request $request
     * @param FigureRepository $figureRepository
     * @return Response
     */
    public function manageFigures(Request $request, FigureRepository $figureRepository): Response
    {
        $figures = $figureRepository->findBy([], ['created_at' => 'DESC']);
        $figures = $this->paginator->paginate(
            $figures,
            $request->query->getInt('page', 1),
            self::ITEM_PER_PAGE
        );
        return $this->render('admin/figures.html.twig', [
            'figures' => $figures,
            'controller_name' => 'AdminController'
        ]);
    }

    /**
     * Delete user
     *
     * @IsGranted("ROLE_ADMIN")
     * @Route("/admin/users/{id}", name="user_delete", methods={"POST"})
     * @param Request $request
     * @param User $user
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function deleteUser(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }
        return $this->redirectToRoute('app_admin', [], Response::HTTP_SEE_OTHER);
    }
}
