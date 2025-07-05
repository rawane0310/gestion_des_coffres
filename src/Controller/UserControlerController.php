<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#use Symfony\Component\Security\Core\Authentication\Token\Storage\
#[Route('/user')]
final class UserControlerController extends AbstractController
{
    #[Route('/user/controler', name: 'app_user_controler')]
    public function index(): Response
    {
        return $this->render('user_controler/index.html.twig', [
            'controller_name' => 'UserControlerController',
        ]);
    }
    #[Route('/create', name:'create_user',methods:['POST'])]
    public function register(Request $request, EntityManagerInterface $entityManager,UserPasswordHasherInterface $passwordHasher): Response
    {
        $data= json_decode($request->getContent(), true);


        if (!isset($data['email']) || !isset($data['password'])) {
            return new Response('email and password are required', Response::HTTP_BAD_REQUEST);
        }

        $user = new User();
        $user->setEmail($data['email']);

        $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);

        $entityManager->persist($user);
        $entityManager->flush();

        return new Response("User created {$user->getEmail()}", Response::HTTP_OK);

    }
}
