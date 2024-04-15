<?php

namespace App\Controller;

use App\Entity\Animal;
use App\Entity\Comment;
use App\Entity\Image;
use App\Form\AnimalType;
use App\Form\CommentType;
use App\Form\ImageType;
use App\Repository\AnimalRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AnimalController extends AbstractController
{
    #[Route('/animal', name: 'app_animal')]
    public function index(AnimalRepository $animalRepository): Response
    {


        return $this->render('animal/index.html.twig', [
            'controller_name' => 'AnimalController',
            'animals' => $animalRepository->findAll(),
        ]);
    }

    #[Route('/animal/create', name: 'create_animal')]
    public function create(EntityManagerInterface $manager, Request $request)
    {
        $animal = new Animal();
        $form_animal = $this->createForm(AnimalType::class,$animal);
        $form_animal->handleRequest($request);

        if($form_animal->isSubmitted() && $form_animal->isValid())
        {
            $animal->setAuthor($this->getUser());
            $animal->setCreatedAt(new \DateTime());
            $manager->persist($animal);
            $manager->flush();


            return $this->redirectToRoute('app_animal');
        }

        return $this->render('animal/create.html.twig',[
            "form_animal"=>$form_animal->createView()

        ]);
    }

    #[Route('/animal/show/{id}', name: 'show_animal')]
    public function show(AnimalRepository $animalRepository, Animal $animal): Response
    {

        $comment = new Comment();
        $comment_form = $this->createForm(CommentType::class, $comment);


        return $this->render('animal/show.html.twig', [
            'animal' => $animal,
            'comment_form' => $comment_form->createView(),

        ]);
    }

    #[Route('/animal/delete/{id}', name: 'delete_animal')]
    public function delete(Animal $animal, EntityManagerInterface $manager): Response
    {
        $manager->remove($animal);
        $manager->flush();
        return $this->redirectToRoute('app_animal');

    }

    #[Route('/animal/edit/{id}', name: 'edit_animal')]
    public function edit(Animal $animal, Request $request, EntityManagerInterface $manager): Response
    {
        $form_animal = $this->createForm(AnimalType::class, $animal);
        $form_animal->handleRequest($request);
        if($form_animal->isSubmitted() && $form_animal->isValid())
        {
            $manager->persist($animal);
            $manager->flush();
            return $this->redirectToRoute('app_animal');

        }

        return $this->render('animal/edit.html.twig', [
            'animal' => $animal,
            'form_animal' => $form_animal->createView(),
        ]);

    }

    #[Route('/animal/{id}/add', name: 'animal_image')]
    public function addImage(Animal $animal): Response
    {
        $image = new Image();
        $form_image = $this->createForm(ImageType::class, $image);

        return $this->render('image/index.html.twig', [
            'animal' => $animal,
            'form_image' => $form_image->createView(),
        ]);
    }
}
