<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use App\Repository\HistoriqueRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/historique')]
final class HistoriqueController extends AbstractController
{
    #[Route('/historique', name: 'app_historique')]
    public function index(): Response
    {
        return $this->render('historique/index.html.twig', [
            'controller_name' => 'HistoriqueController',
        ]);
    }
    #[Route('/searchByCoffre', name:'search_by_coffre', methods: ['GET'])]
    public function show(Request $request, HistoriqueRepository $historiqueRepository): JsonResponse
    {
        $id = $request->query->get('id');
        $historiques = $historiqueRepository->findBy(['coffre' => $id]);

        if (empty($historiques)) {
            return new JsonResponse(['message' => 'No historique found for this coffre'], Response::HTTP_NOT_FOUND);
        }

        $data = [];
        foreach ($historiques as $h) {
            $data[] = [
            'id' => $h->getId(),
            'ancien_code' => $h->getCode(),
            'date' => $h->getCreatedAt()?->format('Y-m-d H:i:s'),
            'changed_by' => $h->getChangedBy()?->getEmail()
            ];
        }

        return new JsonResponse([
                'success' => true,
                'data' => $data,
            ], Response::HTTP_OK);
    }
}
