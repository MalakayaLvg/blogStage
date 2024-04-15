<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CategoryController extends AbstractController
{

    #[Route('/category/new', name: 'app_category')]
    public function index(CategoryRepository $categoryRepository, EntityManagerInterface $manager, Request $request): Response
    {
        $category = new Category();
        $category_form = $this->createForm(CategoryType::class, $category);
        $category_form->handleRequest($request);
        if ($category_form->isSubmitted() && $category_form->isValid())
        {
            $manager->persist($category);
            $manager->flush();

            return $this->redirectToRoute("app_category");
        }

        return $this->render('category/index.html.twig', [
            "category_form" => $category_form->createView(),
            "categories" => $categoryRepository->findAll()
        ]);
    }
}
