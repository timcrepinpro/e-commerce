<?php

namespace App\Twig;

use App\Repository\CategoryRepository;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

/**
 * Envoie la liste des catégories à toutes les vues (utilisée par la navbar).
 */
class CategoriesExtension extends AbstractExtension implements GlobalsInterface
{
    public function __construct(private CategoryRepository $categoryRepository)
    {
    }

    public function getGlobals(): array
    {
        return [
            'categories' => $this->categoryRepository->findAll(),
        ];
    }
}
