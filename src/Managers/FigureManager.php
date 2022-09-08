<?php

namespace App\Managers;

use App\Entity\Figure;
use App\Helper\UniqueSlug;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * Class FigureManager
 */
class FigureManager extends AbstractManager
{

    private UniqueSlug $uniqueSlug;
    private SluggerInterface $slugger;

    /**
     * FigureManager constructor.
     * @param EntityManagerInterface $entityManager
     * @param UniqueSlug $uniqueSlug
     * @param SluggerInterface $slugger
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        UniqueSlug $uniqueSlug,
        SluggerInterface $slugger)
    {
        $this->uniqueSlug = $uniqueSlug;
        $this->slugger = $slugger;
        parent::__construct($entityManager);
    }

    /**
     * Initialise entity before save
     *
     * @param Figure $entity
     * @throws Exception
     */
    protected function initialise(Figure $entity)
    {
        if (!$entity->getId()) {
            $slug = $this->uniqueSlug->getUniqueSLug($entity->getName());
            $currentTime = new DateTimeImmutable();
            $entity
                ->setSlug($slug)
                ->setCreatedAt($currentTime);
        }
    }

    /**
     * Upload image
     *
     * @param $figureImage
     * @param $figureImageDirectorty
     * @return string
     */
    public function uploadImage($figureImage, $figureImageDirectorty): string
    {
        $originalFilename = pathinfo($figureImage->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $figureImage->guessExtension();
        try {
            $figureImage->move(
                $figureImageDirectorty,
                $newFilename
            );
        } catch (FileException $e) {
            dump($e->getMessage());
        }
        return $newFilename;
    }
}