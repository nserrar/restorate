<?php

namespace App\Controller;

use App\Entity\Media;
use App\Entity\Restaurant;
use App\Form\RestaurantType;
use App\Repository\RestaurantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/restaurant")
 */
class RestaurantController extends AbstractController
{
    /**
     * @Route("/", name="app_restaurant_index", methods={"GET"})
     */
    public function index(RestaurantRepository $restaurantRepository): Response
    {
        return $this->render('restaurant/index.html.twig', [
            'restaurants' => $restaurantRepository->getNLastRestaurant(10),
        ]);


    }

    /**
     * @Route("/", name="app_restaurant_search", methods={"POST"})
     */
    public function searchByCodePostal(Request $request, RestaurantRepository $restaurantRepository): Response
    {
        $postalCode = $request->request->get('search');
        return $this->render('restaurant/index.html.twig', [
            'restaurants' => $restaurantRepository->getRestaurantsByPostalCode($postalCode),
        ]);
    }

    /**
     * @Route("/new", name="app_restaurant_new", methods={"GET", "POST"})
     */
    public function new(Request $request, RestaurantRepository $restaurantRepository, SluggerInterface $slugger): Response
    {
        $restaurant = new Restaurant();
        $form = $this->createForm(RestaurantType::class, $restaurant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mediaFile = $form->get('mediaFile')->getData();
            $user = $this->get('security.token_storage')->getToken()->getUser();
            $restaurant->setOwner($user);
            if ($mediaFile) {
                $media = new Media();
                $media->setRestaurant($restaurant);
                $originalFilename = pathinfo($mediaFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$mediaFile->guessExtension();

                try {
                    $mediaFile->move(
                        'media/',
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                $media->setFile($newFilename);
            }

            $restaurantRepository->add($restaurant);
            return $this->redirectToRoute('app_restaurant_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('restaurant/new.html.twig', [
            'restaurant' => $restaurant,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_restaurant_show", methods={"GET"})
     */
    public function show(Restaurant $restaurant): Response
    {
        return $this->render('restaurant/show.html.twig', [
            'restaurant' => $restaurant,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_restaurant_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Restaurant $restaurant, RestaurantRepository $restaurantRepository): Response
    {
        $form = $this->createForm(RestaurantType::class, $restaurant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $restaurantRepository->add($restaurant);
            return $this->redirectToRoute('app_restaurant_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('restaurant/edit.html.twig', [
            'restaurant' => $restaurant,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_restaurant_delete", methods={"POST"})
     */
    public function delete(Request $request, Restaurant $restaurant, RestaurantRepository $restaurantRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$restaurant->getId(), $request->request->get('_token'))) {
            $restaurantRepository->remove($restaurant);
        }

        return $this->redirectToRoute('app_restaurant_index', [], Response::HTTP_SEE_OTHER);
    }
}
