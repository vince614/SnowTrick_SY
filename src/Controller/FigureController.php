<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Figure;
use App\Form\FigureType;
use App\Repository\FigureRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Managers\FigureManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FigureController extends AbstractController
{

    /**
     * @Route("/", name="figure_index", methods={"GET"})
     * @param FigureRepository $figureRepository
     * @return Response
     */
    public function index(FigureRepository $figureRepository): Response
    {
        return $this->render('figure/index.html.twig', [
            'figures' => $figureRepository->findAll(),
        ]);
    }

    /**
     * @Route("/figure/new", name="figure_new", methods={"GET", "POST"})
     * @param Request $request
     * @param FigureManager $figureManager
     * @return RedirectResponse|Response
     */
    public function new(Request $request, FigureManager $figureManager)
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
     * @Route("/figure/{id}", name="figure_show", methods={"GET"})
     * @param Figure $figure
     * @return Response
     */
    public function show(Figure $figure): Response
    {
        return $this->render('figure/show.html.twig', [
            'category' => $figure,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="figure_edit", methods={"GET", "POST"})
     * @param Request $request
     * @param FigureManager $figureManager
     * @return Response
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
     * @Route("/{id}", name="figure_delete", methods={"POST"})
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
