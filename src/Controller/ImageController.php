<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Entity\Image;
use App\Form\ImageType;
use App\Managers\FigureManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ImageController extends AbstractController
{
    /**
     * Add new figure image to gallery
     *
     * @Route("/image/new/{id}", name="figure_image_new", methods={"GET", "POST"})
     * @param Request $request
     * @param Figure $figure
     * @param FigureManager $figureManager
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function newFigureImage(
        Request $request,
        Figure $figure,
        FigureManager $figureManager,
        EntityManagerInterface $entityManager): Response
    {
        $image = new Image();
        $form = $this->createForm(ImageType::class, $image);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            $imageDirectorty = $this->getParameter('images_directory');
            if ($imageFile) {
                $newFilename = $figureManager->uploadImage($imageFile, $imageDirectorty);
                $image
                    ->setName($figure->getName())
                    ->setFigure($figure)
                    ->setUrl($newFilename);
                $entityManager->persist($image);
                $entityManager->flush();
            }
            $figureManager->save($figure);
            return $this->redirectToRoute('figure_show', ['slug' => $figure->getSlug()], Response::HTTP_SEE_OTHER);
        }
        return $this->render('figure/image_new.html.twig', [
            'image' => $image,
            'form' => $form->createView()
        ]);
    }

    /**
     * Edit Figure image
     *
     * @Route("/image/edit/{id}", name="figure_image_edit", methods={"GET", "POST"})
     * @param Request $request
     * @param Image $image
     * @param FigureManager $figureManager
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function editFigureImage(
        Request $request,
        Image $image,
        FigureManager $figureManager,
        EntityManagerInterface $entityManager): Response
    {
        $_image = new Image();
        $figure = $image->getFigure();
        $form = $this->createForm(ImageType::class, $_image);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            $imageDirectorty = $this->getParameter('images_directory');
            if ($imageFile) {
                $newFilename = $figureManager->uploadImage($imageFile, $imageDirectorty);
                $image->setUrl($newFilename);
                $entityManager->persist($image);
                $entityManager->flush();
            }
            $figureManager->save($figure);
            return $this->redirectToRoute('figure_show', ['slug' => $figure->getSlug()], Response::HTTP_SEE_OTHER);
        }
        return $this->render('figure/image.html.twig', [
            'image' => $image,
            'form' => $form->createView()
        ]);
    }
}
