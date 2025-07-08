<?php

namespace App\Controller;


use App\Service\UserManager;

use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Routing\Attribute\Route;


#use Symfony\Component\Security\Core\Authentication\Token\Storage\
#[Route('api/user')]
final class UserControlerController extends AbstractController
{

    #[Route('/create', name:'create_user',methods:['POST'])]
    public function signUp(Request $request, UserManager $userManager): JsonResponse
    {
        $data= json_decode($request->getContent(), true);
        $response = $userManager->createUser($data);
        return new JsonResponse($response['message'], $response['status']);

    }
}
