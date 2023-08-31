<?php

namespace App\Controller\Admin;

use App\Repository\CategoriesRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/categories', name:'admin_categories_')]
Class CategoriesController extends AbstractController
{
    #[Route('/', name:'index')]
    public function index(CategoriesRepository $categoriesRepository): Response
    {
        $categories = $categoriesRepository->findBy([], ['categoryOrder' => 'ASC']);

        return $this->render('admin/categories/index.html.twig', compact('categories'));
    }

}