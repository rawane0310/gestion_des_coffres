<?php

namespace App\Controller;


use App\Service\CoffreManager;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/api/coffre')]
final class CoffreController extends AbstractController
{

    #[Route('/create', name:'create_coffre', methods: ['POST'])]
    public function register(Request $request, CoffreManager $coffreManager): JsonResponse
    {
        $data= json_decode($request->getContent(), true);

        $response = $coffreManager->createCoffre($data);

        return new JsonResponse($response['message'], $response['status']);
    }
    #[Route(path:"/updateCode", name:"update_code", methods: ["PUT"])]
    public function updateCode(Request $request,CoffreManager $coffreManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $response = $coffreManager->updateCoffreCode($data);

        return new JsonResponse($response['message'], $response['status']);
    }
    #[Route('/searchByCode','search_by_code',methods: ['GET'])]
    public function searchByCode(Request $request, CoffreManager $coffreManager): JsonResponse
    {
        $code = $request->query->get('code');
        $response = $coffreManager->searchByCode($code);
        return new JsonResponse($response['message'], $response['status']);
    }
    #[Route(path:'/searchByNom', name:'searche_by_nom', methods: ['GET'])]
    public function searchByNom(Request $request, CoffreManager $coffreManager): JsonResponse
    {
        $name = $request->query->get('name');
        $response = $coffreManager->searchByNom($name);
        return new JsonResponse($response['message'], $response['status']);
    }

    #[Route('/','all_coffre',methods: ['GET'])]
    public function show(CoffreManager $coffreManager): JsonResponse
    {
        $response = $coffreManager->getAllCoffres();
        return new JsonResponse($response['message'], $response['status']);
    }
    #[Route('/updateNom','change_nom',methods: ['PUT'])]
    public function updateNom(Request $request, CoffreManager $coffreManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $response = $coffreManager->updateCoffreName($data);
        return new JsonResponse($response['message'], $response['status']);
    }
}
