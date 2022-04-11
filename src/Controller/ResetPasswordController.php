<?php

namespace App\Controller;

use DateTime;
use App\Classe\Mail;
use App\Entity\User;
use App\Entity\ResetPassword;
use App\Form\ResetPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;




class ResetPasswordController extends AbstractController
{


    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {

        $this->entityManager = $entityManager;
    }


    /**
     * @Route("/mot-de-passe-oublie", name="app_reset_password")
     */
    public function index(Request $request)
    {
        if ($this->getUser()) {

            return $this->redirectToRoute('home');
        }
        if ($request->get('email')) {

            $user = $this->entityManager->getRepository(User::class)->findOneByEmail($request->get('email'));

            if ($user) {
                //enregister en base la demande de resetpassword
                $reset_password = new ResetPassword();
                $reset_password->setUser($user);
                $reset_password->setToken(uniqid());
                $reset_password->setCreatedAt(new DateTime());
                $this->entityManager->persist($reset_password);
                $this->entityManager->flush();
                //envoie d un mail pour un nouveau mdp

                $url = $this->generateUrl('update_password', [

                    'token' => $reset_password->getToken()
                ]);

                $content = 'Bonjour ' . $user->getFirstname() . " " . $user->getLastname() . '<br> Vous avez demandé a réinitialiser votre mot de passe.<br><br>';
                $content .= 'Merci de bien vouloir cliquer sur le lien suivant pour mettre  <a href="' . $url . '">à jour votre mot de passe</a>.';

                $mail = new Mail();
                $mail->send($user->getEmail(), $user->getFirstname() . " " . $user->getLastname(), 'Réinitialiser votre mot de passe', $content);
                $this->addFlash('notice', 'Vous allez recevoir un email pour modifier votre mot de passe ');
            } else {
                $this->addFlash('notice', 'Cette adresse email est inconnu');
            }
        }

        return $this->render('reset_password/index.html.twig');
    }
    /**
     * @Route("/modifier-mon-mot-de-passe/{token}", name="update_password")
     */
    public function update(Request $request, $token, UserPasswordHasherInterface  $encoder)

    {
        $reset_password = $this->entityManager->getRepository(ResetPassword::class)->findOneByToken($token);

        if (!$reset_password) {
            return $this->redirectToRoute('app_reset_password');
        }

        //verifier en terme de temps si c est bon
        $now = new DateTime();
        if ($now > $reset_password->getcreatedAt()->modify('+3 hour')) {
            $this->addFlash('notice', 'Votre demande de mot d passe a expiré merci de la renouveller');
            return $this->redirectToRoute('app_reset_password');
        }

//rendre une vue avec mtp et confirmation
        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

       
        if ($form->isSubmitted() && $form->isValid()) {
            $new_pwd = $form->get('new_password')->getData();
            $password = $encoder->hashPassword($reset_password->getUser(), $new_pwd);
            //reinjecter dans l'objet user le mdp crypter
            $reset_password->getUser()->setPassword($password);
            //j enregistre les infos
            $this->entityManager->flush();

            $this->addFlash('notice', 'Votre mot de passe a bien été mis a jour');
            return $this->redirectToRoute('app_login');
        }


        return $this->render('reset_password/update.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
