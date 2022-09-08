<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Entity\Image;
use App\Entity\Video;
use App\Form\FigureType;
use App\Repository\FigureRepository;
use App\Repository\GroupRepository;
use App\Security\Voter\FigureVoter;
use Doctrine\ORM\EntityManagerInterface;
use App\Managers\FigureManager;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class FigureController
 * @package App\Controller
 *
 * @Route("/")
 */
class FigureController extends AbstractController
{

    /** @var int  */
    const FIGURES_PER_PAGE = 12;

    /**
     * Index page
     *
     * @Route("/", name="figure_index", methods={"GET"})
     * @param FigureRepository $figureRepository
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function index(
        FigureRepository $figureRepository,
        Request $request,
        PaginatorInterface $paginator
    ): Response
    {
        $figures = $figureRepository->findBy([], ['created_at' => 'DESC']);
        $figures = $paginator->paginate(
            $figures,
            $request->query->getInt('page', 1),
            15
        );
        return $this->render('figure/index.html.twig', ['figures' => $figures]);
    }

    /**
     * Create new figure
     *
     * @Route("/figure/new", name="figure_new", methods={"GET", "POST"})
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @param FigureManager $figureManager
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function new(Request $request, FigureManager $figureManager): RedirectResponse|Response
    {
        // Redirect if not logged
        if (!$this->getUser()) return $this->redirectToRoute('figure_index');

        $figure = new Figure();
        $form = $this->createForm(FigureType::class, $figure);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $figure->setAuthor($this->getUser());
            $figureManager->save($figure);
            return $this->redirectToRoute('figure_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('figure/new.html.twig', [
            'figure' => $figure,
            'form' => $form,
        ]);
    }

    /**
     * Show figure informations
     *
     * @Route("/figure/show/{slug}", name="figure_show", methods={"GET"})
     * @param Figure $figure
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function show(
        Figure $figure,
        Request $request,
        PaginatorInterface $paginator
    ): Response
    {
        $comments = $figure->getComments();
        $comments = $paginator->paginate(
            $comments,
            $request->query->getInt('page', 1),
            5
        );
        return $this->render('figure/show.html.twig', [
            'figure'    => $figure,
            'comments'  => $comments
        ]);
    }

    /**
     * Figures list
     *
     * @Route("/figure/list", name="figure_list", methods={"GET"})
     * @param Request $request
     * @param FigureRepository $figureRepository
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function list(Request $request, FigureRepository $figureRepository, PaginatorInterface $paginator): Response
    {
        $figures = $figureRepository->findBy([], ['created_at' => 'DESC']);
        $figures = $paginator->paginate(
            $figures,
            $request->query->getInt('page', 1),
            self::FIGURES_PER_PAGE
        );
        return $this->render('figure/list.html.twig', [
            'figures' => $figures
        ]);
    }

    /**
     * Edit figure
     *
     * @Route("/figure/edit/{id}", name="figure_edit", methods={"GET", "POST"})
     * @param Request $request
     * @param FigureManager $figureManager
     * @param Figure $figure
     * @param GroupRepository $groupRepository
     * @return Response
     */
    public function edit(
        Request $request,
        FigureManager $figureManager,
        Figure $figure,
        GroupRepository $groupRepository
    ): Response
    {
        $this->denyAccessUnlessGranted(FigureVoter::EDIT, $figure);
        $form = $this->createForm(FigureType::class, $figure);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $figureManager->save($figure);
            return $this->redirectToRoute('figure_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('figure/edit.html.twig', [
            'figure' => $figure,
            'groups' => $groupRepository->findAll(),
            'firgureForm' => $form,
        ]);
    }

    /**
     * Delete figure
     *
     * @Route("/figure/delete/{id}", name="figure_delete", methods={"POST","GET"})
     * @param Request $request
     * @param Figure $figure
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function delete(Request $request, Figure $figure, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted(FigureVoter::DELETE, $figure);
        if ($this->isCsrfTokenValid('delete' . $figure->getId(), $request->request->get('_token'))) {
            $entityManager->remove($figure);
            $entityManager->flush();
        }
        return $this->redirectToRoute('figure_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * Delete image from figure
     *
     * @Route("/figure/image/delete/{id}", name="figure_image_delete", methods={"POST","GET"})
     * @param Request $request
     * @param Image $image
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function deleteImage(Request $request, Image $image, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted(FigureVoter::DELETE, $image->getFigure());
        if ($this->isCsrfTokenValid('delete-image' . $image->getId(), $request->request->get('_token'))) {
            $figure = $image->getFigure();
            $figure->removeImage($image);
            $entityManager->flush();
        }
        return $this->redirectToRoute('figure_index', [], Response::HTTP_SEE_OTHER);
    }


    /**
     * Edit image
     *
     * @Route("/figure/image/edit/{id}", name="figure_image_edit", methods={"POST","GET"})
     * @param Request $request
     * @param Image $image
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function updateImage(Request $request, Image $image, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted(FigureVoter::EDIT, $image->getFigure());
        if ($this->isCsrfTokenValid('update-image' . $image->getId(), $request->request->get('_token'))) {
            $newImageUrl = $request->request->get('new_url');
            $image->setUrl($newImageUrl);
            $entityManager->persist($image);
            $entityManager->flush();
        }
        return $this->redirectToRoute('figure_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * Delete video
     *
     * @Route("/figure/video/delete/{id}", name="figure_video_delete", methods={"POST","GET"})
     * @param Request $request
     * @param Video $video
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function deleteVideo(Request $request, Video $video, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted(FigureVoter::DELETE, $video->getFigure());
        if ($this->isCsrfTokenValid('delete-video' . $video->getId(), $request->request->get('_token'))) {
            $figure = $video->getFigure();
            $figure->removeVideo($video);
            $entityManager->flush();
        }
        return $this->redirectToRoute('figure_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * Edit video
     *
     * @Route("/figure/video/edit/{id}", name="figure_video_edit", methods={"POST","GET"})
     * @param Request $request
     * @param Video $video
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function editVideo(Request $request, Video $video, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted(FigureVoter::EDIT, $video->getFigure());
        if ($this->isCsrfTokenValid('update-video' . $video->getId(), $request->request->get('_token'))) {
            $newVideoUrl = $request->request->get('new_url');
            $video->setUrl($newVideoUrl);
            $entityManager->persist($video);
            $entityManager->flush();
        }
        return $this->redirectToRoute('figure_index', [], Response::HTTP_SEE_OTHER);
    }


}
