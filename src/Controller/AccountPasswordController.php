<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AccountPasswordController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/compte/modifier-mon-mot-de-passe", name="account_password")
     */
    public function index(Request $request, UserPasswordHasherInterface  $encoder): Response
    {
//affichage de notif
$notif=null;



                                                //j ai besoin d instancier ma class user
        $user = $this->getUser();
                                            //j ai besoin d instancier mon formulaire utilisation de la methode createform avec en parametre la class de mon formulaire et les info user
        $form = $this->createForm(ChangePasswordType::class, $user);
                                            //des que le formulaire est soumis je souhaite que tu rentre dans la base de donnees
                                            //ecoute la requete entrante avec handlerequest et je dois faire une injection de dependance dans le param de la fonction
        $form->handleRequest($request);
                                             //condition savoir si le form est soumis et si il est valid
        if ($form->isSubmitted()  && $form->isValid()) {
                                            //si oui tu inject dans user les infos du formulaire
            $user = $form->getData();
                                            //recuperation du mdp dans le formulaire
            $old_pwd= $form->get('old_password')->getData();
            if ($encoder->isPasswordValid($user, $old_pwd)) {

                                                //recuperation du nouveau mdp
                $new_pwd= $form->get('new_password')->getData();
                
                                                //faire appel a doctrine pour enregistrer les infos dans la base directement dans le constructeur

                                                    //encoder le mot de passe                            
            $password = $encoder->hashPassword($user, $new_pwd);
                                                            //reinjecter dans l'objet user le mdp crypter

            $user->setPassword($password);

                                                                    //je fige les infos
            $this->entityManager->persist($user);
                                                                        //j enregistre les infos
            $this->entityManager->flush();

            $notif= "votre mot de passe a bien ete mis a jour";
            }else{
                $notif= "votre mot de passe actuel n est pas le bon ";
            }

        }


        return $this->render('account/password.html.twig', [
            'form' => $form->createView(),
            'notif'=>$notif




        ]);
    }
}
