<?php

namespace App\Controller;

use App\Entity\Coffre;
use App\Repository\CoffreRepository;
use App\Repository\UserRepository;
use App\Repository\HistoriqueRepository;
use App\Service\CoffreManager;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/coffre')]
final class CoffreController extends AbstractController
{
    #[Route('/coffre', name: 'app_coffre')]
    public function index(): Response
    {
        return $this->render('coffre/index.html.twig', [
            'controller_name' => 'CoffreController',
        ]);
    }
    #[Route('/create', name:'create_coffre', methods: ['POST'])]
    public function register(Request $request, EntityManagerInterface $em, UserRepository $userRepository, CoffreRepository $coffreRepository): Response
    {
        $data= json_decode($request->getContent(), true);

        if (!isset($data['user']) || !isset($data['nom'])) {
            return new Response('User and Name are required', Response::HTTP_BAD_REQUEST);
        }

        $coffre= new Coffre();
        $coffre->setNom($data['nom']);

        #verifier que le code est unique
        $maxAttempts=10;
        $attempts = 0;

        do {
            $code = bin2hex(random_bytes(18));
            $attempts++;

            // Vérifier si le code existe déjà
            $existingCoffre = $coffreRepository->findOneBy(['code' => $code]);

            if ($existingCoffre === null) {
                $coffre->setCode($code);
                break;
            }

        } while ($attempts < $maxAttempts);

        if ($coffre->getCode() === null) {
            return new Response("Impossible to generate unique code {$maxAttempts} attempts.", Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $user= $userRepository->findOneBy(['email' => $data['user']]);
        if (!$user) {
        return new Response("User not found", Response::HTTP_NOT_FOUND);
        }

        $coffre->addUser($user);

        $em->persist($coffre);
        $em->flush();

        return new Response("Coffre created with name {$coffre->getNom()} , and code {$coffre->getCode()}", Response::HTTP_OK);
    }
    #[Route(path:"/updateCode", name:"update_code", methods: ["PUT"])]
    public function updateCode(Request $request, CoffreRepository $coffreRepository,UserRepository $userRepository ,EntityManagerInterface $em, CoffreManager $coffreManager): Response
    {
        $data = json_decode($request->getContent(), true);

        if (!$data || !isset($data['id']) || !isset($data['user'])) {
            return new Response("idCoffre and user are required", Response::HTTP_BAD_REQUEST);
        }

        $coffre = $coffreRepository->findOneBy(['id'=> $data['id']]);
        if (!$coffre) {
            return new Response("Coffre not found", Response::HTTP_NOT_FOUND);
        }

        $user = $userRepository->findOneBy(['email'=> $data['user']]);
        if (!$user) {
            return new Response("User not found", Response::HTTP_NOT_FOUND);
        }

        // Generer le code aleatoirement
        $attempts = 0;
        $maxAttempts = 10;
        $codeValide = null;

        do {
            $code = bin2hex(random_bytes(18));
            $attempts++;

            // Vérifier si le code existe déjà
            $existingCoffre = $coffreRepository->findOneBy(['code' => $code]);

            if ($existingCoffre === null) {

                $codeValide = $code;
                $historique = $coffreManager->createHistorique($coffre, $user, $em);

                $coffre->setCode($codeValide);
                $coffre->addHistorique($historique);
                break;
            }

        } while ($attempts < $maxAttempts);


        if ($codeValide) {

            $em->flush();
            return new Response("Code changed from {$historique->getCode()} to {$coffre->getCode()}", Response::HTTP_OK);
        } else {
            return new Response("Impossible to generate random code", Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    #[Route('/searchByCode','search_by_code',methods: ['GET'])]
    public function searchByCode(Request $request, CoffreRepository $coffreRepository, HistoriqueRepository $historiqueRepository): Response
    {
        $code = $request->query->get('code');
        if ($code === null) {
            return new Response('Code is required', Response::HTTP_BAD_REQUEST);
        }

        $coffre = $coffreRepository->findOneBy(['code' => $code]);
        if ($coffre) {
            return new Response("Coffre found : {$coffre->getId()}, nom : {$coffre->getNom()}, code : {$coffre->getCode()} " , Response::HTTP_OK);
        }

        $historique = $historiqueRepository->findOneBy(['code' => $code]);
        if (! $historique) {
                return new Response('Code does not exist', Response::HTTP_NOT_FOUND);
        }

        $coffre = $historique->getCoffre();
        if ($coffre) {
            return new Response("Coffre found : {$coffre->getId()}, nom : {$coffre->getNom()}, code : {$coffre->getCode()} with old code {$historique->getCode()} and id {$historique->getId()}", Response::HTTP_OK);
        }

        return new Response('Code does not exist', Response::HTTP_NOT_FOUND);
    }
    #[Route('/','all_coffre',methods: ['GET'])]
    public function show(CoffreRepository $coffreRepository): JsonResponse
    {
        $coffres = $coffreRepository->findAll();

        $data = [];

        foreach ($coffres as $coffre) {
            $data[] = [
                'id' => $coffre->getId(),
                'nom' => $coffre->getNom(),
                'code' => $coffre->getCode()
            ];
        }
        return new JsonResponse ([
                'success' => true,
                'data' => $data,
            ], Response::HTTP_OK);
    }
    #[Route('/changeNom','change_nom',methods: ['PUT'])]
    public function updateNom(CoffreRepository $coffreRepository, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (! $data || ! isset($data['id']) || ! isset($data['nom'])) {
            return new JsonResponse(['error' => 'id and nom are required'], Response::HTTP_BAD_REQUEST);
        }

        $coffre = $coffreRepository->find($data['id']);
        if (!$coffre) {
            return new JsonResponse(['error' => 'Coffre does not exist'], Response::HTTP_NOT_FOUND);
        }
        $coffre->setNom($data['nom']);

        $em->flush();
        return new JsonResponse([
            'message' => 'Nom updated succefully',
            'id' => $coffre->getId(),
            'new_nom' => $coffre->getNom()
            ], Response::HTTP_OK);
    }
}
