<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Entity\Group;
use App\Form\FigureType;
use App\Repository\FigureRepository;
use App\Repository\GroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Managers\FigureManager;
use Exception;
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
    /**
     * @Route("/", name="figure_index", methods={"GET"})
     * @param FigureRepository $figureRepository
     * @param GroupRepository $groupRepository
     * @return Response
     */
    public function index(FigureRepository $figureRepository, GroupRepository $groupRepository): Response
    {
        return $this->render('figure/index.html.twig', [
            'figures' => $figureRepository->findAll(),
            'groups' => $groupRepository->findAll(),
        ]);
    }

    /**
     * @Route("/figure/new", name="figure_new", methods={"GET", "POST"})
     * @param Request $request
     * @param FigureManager $figureManager
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function new(Request $request, FigureManager $figureManager): RedirectResponse|Response
    {
        $figure = new Figure();
        $form = $this->createForm(FigureType::class, $figure);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $figureManager->save($figure);
            return $this->redirectToRoute('figure_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('figure/new.html.twig', [
            'figure' => $figure,
            'form' => $form,
        ]);
    }

    /**
     * Show figure card
     *
     * @Route("/figure/show/{slug}", name="figure_show", methods={"GET"})
     * @param Figure $figure
     * @return Response
     */
    public function show(Figure $figure): Response
    {
        return $this->render('figure/show.html.twig', [
            'figure' => $figure,
        ]);
    }

    /**
     * Figures list
     *
     * @Route("/figure/list", name="figure_list", methods={"GET"})
     * @param FigureRepository $figureRepository
     * @return Response
     */
    public function list(FigureRepository $figureRepository): Response
    {
        return $this->render('figure/list.html.twig', [
            'figures' => $figureRepository->findAll()
        ]);
    }

    /**
     * Edit figure
     *
     * @Route("/figure/edit/{id}", name="figure_edit", methods={"GET", "POST"})
     * @param Request $request
     * @param FigureManager $figureManager
     * @return Response
     * @throws Exception
     */
    public function edit(Request $request, FigureManager $figureManager): Response
    {
        $figure = new Figure();
        $form = $this->createForm(FigureType::class, $figure);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $figureManager->save($figure);
            return $this->redirectToRoute('figure_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('figure/edit.html.twig', [
            'figure' => $figure,
            'form' => $form,
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
        if ($this->isCsrfTokenValid('delete' . $figure->getId(), $request->request->get('_token'))) {
            $entityManager->remove($figure);
            $entityManager->flush();
        }
        return $this->redirectToRoute('figure_index', [], Response::HTTP_SEE_OTHER);
    }


}
