<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\HistoriqueManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('api/historique')]
final class HistoriqueController extends AbstractController
{

    #[Route('/searchByCoffre', name:'search_by_coffre', methods: ['GET'])]
    public function show(Request $request, HistoriqueManager $historiqueManager ): JsonResponse
    {
        $id = $request->query->get('id');
        $response = $historiqueManager->getHistoriquesByCoffreId($id);
        return new JsonResponse($response['message'], $response['status']);
    }
}
