<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Address;
use App\Form\AddressType;
use App\Repository\AddressRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class AccountAddressController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManger = $entityManager;
    }
    /**
     * @Route("/compte/addresses", name="app_account_address")
     */
    public function index()
    {
        return $this->render('account/adress.html.twig');
    }


    /**
     * @Route("/compte/ajouter_une_addresse", name="add_address")
     */
    public function add(Cart $cart, Request $request)
    {
        $address = new Address();
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $address->setUser($this->getUser());
            $this->entityManger->persist($address);
            $this->entityManger->flush();
            if ($cart->get()) {
                return $this->redirectToRoute('app_order');
            } else {
                return $this->redirectToRoute('app_account_address');
            }
        }
        return $this->render('account/form_address.html.twig', [
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/compte/editer_une_addresse/{id}", name="edit_address")
     */
    public function edit(AddressRepository $repo, Request $request, $id)
    {
        $address = $repo->findOneById($id);
        if (!$address || $address->getUser() != $this->getUser()) {
            return $this->redirectToRoute('app_account_address');
        }


        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $this->entityManger->flush();
            return $this->redirectToRoute('app_account_address');
        }

        return $this->render('account/form_address.html.twig', [
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/compte/supprimer_une_addresse/{id}", name="delete_address")
     */
    public function delete(AddressRepository $repo, $id)
    {
        $address = $repo->findOneById($id);

        if ($address || $address->getUser() == $this->getUser()) {

            $this->entityManger->remove($address);
            $this->entityManger->flush();
        }


        return $this->redirectToRoute('app_account_address');
    }
}
