<?php

namespace App\Helper;

use App\Repository\FigureRepository;

class UniqueSlug
{
    private FigureRepository $figureRepository;

    /**
     * @param FigureRepository $figureRepository
     */
    public function __construct(FigureRepository $figureRepository)
    {
        $this->figureRepository = $figureRepository;
    }

    /**
     * Get unique slug
     *
     * @param $slug
     * @return string
     */
    public function getUniqueSLug($slug): string
    {
        $i = 1;
        $_slug = $slug;
        while (true) {
            $slugExist = (bool) $this->figureRepository->findBy(['slug' => $_slug]);
            if ($slugExist) $_slug = $slug . $i++;
            if (!$slugExist) return $_slug;
        }
    }
}