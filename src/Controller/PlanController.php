<?php

// src/Controller/PlanController.php
namespace App\Controller;

use App\Entity\Plan;
use App\Form\PlanType;
use App\Repository\PlanRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/plan')]
class PlanController extends AbstractController
{
    #[Route('/', name: 'app_plan_index', methods: ['GET'])]
    public function index(PlanRepository $planRepository): Response
    {
        return $this->render('plan/index.html.twig', [
            'plans' => $planRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_plan_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $plan = new Plan();
        $form = $this->createForm(PlanType::class, $plan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('plans_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // handle exception if something happens during file upload
                }

                // updates the 'imagePath' property to store the image file name
                // instead of its contents
                $plan->setImagePath($newFilename);
            }

            $entityManager->persist($plan);
            $entityManager->flush();

            return $this->redirectToRoute('app_plan_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('plan/new.html.twig', [
            'plan' => $plan,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_plan_show', methods: ['GET'])]
    public function show(Plan $plan): Response
    {
        return $this->render('plan/show.html.twig', [
            'plan' => $plan,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_plan_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Plan $plan, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(PlanType::class, $plan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('plans_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // handle exception if something happens during file upload
                }

                // updates the 'imagePath' property to store the image file name
                // instead of its contents
                $plan->setImagePath($newFilename);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_plan_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('plan/edit.html.twig', [
            'plan' => $plan,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_plan_delete', methods: ['POST'])]
    public function delete(Request $request, Plan $plan, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$plan->getId(), $request->request->get('_token'))) {
            $entityManager->remove($plan);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_plan_index', [], Response::HTTP_SEE_OTHER);
    }
}