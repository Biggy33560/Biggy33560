<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


//afin de crypter les mdp il faut a nouveau injecter une dependance UserPasswordEncoderInterface

class RegisterController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/inscription", name="register")
     */
    public function index(Request $request, UserPasswordHasherInterface  $encoder)
    {
        //variable pour message de notification
        $notification = null;
        //j ai besoin d instancier ma class user
        $user = new User();

        //j ai besoin d instancier mon formulaire utilisation de la methode createform avec en parametre la class de mon formulaire et les info user
        $form = $this->createForm(RegisterType::class, $user);

        //des que le formulaire est soumis je souhaite que tu rentre dans la base de donnees
        //ecoute la requete entrante avec handlerequest et je dois faire une injection de dependance dans le param de la fonction
        $form->handleRequest($request);
        //condition savoir si le form est soumis et si il est valid
        if ($form->isSubmitted()  && $form->isValid()) {
            //si oui tu inject dans user les infos du formulaire
            $user = $form->getData();
            //faire appel a doctrine pour enregistrer les infos dans la base directement dans le constructeur
            //$doctrine=$this->getDoctrine()->getManager();


            //faire une recherche d email
            $search_email = $this->entityManager->getRepository(User::class)->findOneByEmail($user->getEmail());
            //verifier si l email est deja present dans la base de données
            if (!$search_email) {
                //encoder le mot de passe 
                $password = $encoder->hashPassword($user, $user->getPassword());
                //reinjecter dans l'objet user le mdp crypter

                $user->setPassword($password);

                //je fige les infos
                $this->entityManager->persist($user);
                //j enregistre les infos
                $this->entityManager->flush();

                //envoi d un mail avec mailjet instanciation de la class Mail
                $mail = new Mail();
                //corp du mail dans $content
                $content="BLablabla";
                $mail->send($user->getEmail(),$user->getFirstname(),'Bienvenue sur la boutique de steeve',$content);

                //affichage du message
                $notification = "Votre inscription s'est bien déroulé";
            } else {

                //affichage du message
                $notification = "Impossible l email existe déjà";
            }





            //$user->setPassword ($userPasswordHasher->hashPassword($user,$form->get('plainPassword')->getData()));





        }
        return $this->render('register/index.html.twig', [
            //je souhaite l affichage en passant mes variables a ma template
            'form' => $form->createView(),
            //je souhaite l affichage du message
            'notification' => $notification
        ]);
    }
}
