<?php

namespace App\Service;

use App\Entity\Coffre;
use App\Entity\Historique;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CoffreRepository;
use App\Repository\UserRepository;
use App\Repository\HistoriqueRepository;

class CoffreManager {

    private EntityManagerInterface $em;
    private UserRepository $userRepository;
    private CoffreRepository $coffreRepository;
    private HistoriqueRepository $historiqueRepository;

    public function __construct(
        EntityManagerInterface $em,
        UserRepository $userRepository,
        CoffreRepository $coffreRepository,
        HistoriqueRepository $historiqueRepository
    ) {
        $this->em = $em;
        $this->userRepository = $userRepository;
        $this->coffreRepository = $coffreRepository;
        $this->historiqueRepository = $historiqueRepository;
    }

    /**
     * Generate a unique code that does not already exist in the database.
     */
    private function generateUniqueCode(int $maxAttempts): ?string
    {
        for ($i = 0; $i < $maxAttempts; $i++) {
            $code = bin2hex(random_bytes(18));
            if (!$this->coffreRepository->findOneBy(['code' => $code])) {
                return $code;
            }
        }
        return null;
    }

    public function createCoffre(array $data): array
    {
        // Validate required fields
        if (empty($data['user']) || empty($data['nom'])) {
            return [
                'message' => ['error' => 'User and Name are required'],
                'status' => 400
            ];
        }

        // Find user by email
        $user = $this->userRepository->findOneBy(['email' => $data['user']]);
        if (!$user) {
            return [
                'message' => ['error' => 'User not found'],
                'status' => 404
            ];
        }

        // Create Coffre and set properties
        $coffre = new Coffre();
        $coffre->setNom($data['nom']);

        // Generate unique code
        $code = $this->generateUniqueCode(10);
        if ($code === null) {
            return [
                'message' => ['error' => 'Unable to generate a unique code after 10 attempts.'],
                'status' => 500
            ];
        }

        $coffre->setCode($code);
        $coffre->addUser($user);

        // Save to database
        $this->em->persist($coffre);
        $this->em->flush();

        return [
            'message' => [
                'success' => true,
                'name' => $coffre->getNom(),
                'code' => $coffre->getCode()
            ],
            'status' => 200
        ];
    }



    public function createHistorique( Coffre $coffre, User $changedBy, EntityManagerInterface $em): Historique {
        $historique = new Historique();
        $historique->setCoffre($coffre);
        $historique->setCode($coffre->getCode());
        $historique->setCreatedAt(new \DateTimeImmutable());
        $historique->setChangedBy($changedBy);

        $em->persist($historique);


        return $historique;
    }

    public function updateCoffreCode(array $data): array
    {
        if (empty($data['id']) || empty($data['user'])) {
            return [
                'message' => ['error' => 'Coffre ID and user email are required'],
                'status' => 400
            ];
        }

        $coffre = $this->coffreRepository->find($data['id']);
        if (!$coffre) {
            return [
                'message' => ['error' => 'Coffre not found'],
                'status' => 404
            ];
        }

        $user = $this->userRepository->findOneBy(['email' => $data['user']]);
        if (!$user) {
            return [
                'message' => ['error' => 'User not found'],
                'status' => 404
            ];
        }

        $oldCode = $coffre->getCode();
        $newCode = $this->generateUniqueCode(10);

        if ($newCode === null) {
            return [
                'message' => ['error' => 'Unable to generate a unique code'],
                'status' => 500
            ];
        }

        $historique = $this->createHistorique($coffre, $user,$this->em);
        $coffre->setCode($newCode);
        $coffre->addHistorique($historique);

        $this->em->flush();

        return [
            'message' => [
                'success' => true,
                'old_code' => $oldCode,
                'new_code' => $newCode
            ],
            'status' => 200
        ];
    }

    public function searchByNom(?string $name): array
    {
        if ($name === null) {
            return [
                'message' => ['error' => 'Name is required'],
                'status' => 400
            ];
        }
        $coffre = $this->coffreRepository->findOneBy(['nom'=> $name]);
        if ($coffre) {
            return [
                'message' => [
                    'data' => [[
                            'id' => $coffre->getId(),
                            'name' => $coffre->getNom(),
                            'code' => $coffre->getCode()
                        ]],
                        'succes' => true
                ],
                'status' => 200
            ];
        };

        return [
            'message'=> ['error'=> 'No coffre found'],
            'status'=> 404
        ];
    }
    public function searchByCode(?string $code): array
    {
        if ($code === null) {
            return [
                'message' => ['error' => 'Code is required'],
                'status' => 400
            ];
        }

        // Search in Coffre table
        $coffre = $this->coffreRepository->findOneBy(['code' => $code]);
        if ($coffre) {
            return [
                'message' => [
                        'data' => [[
                            'id' => $coffre->getId(),
                            'name' => $coffre->getNom(),
                            'code' => $coffre->getCode()
                        ]],
                        'succes' => true
                    ],
                    'status' => 200
            ];
        }

        // Search in Historique table
        $historique = $this->historiqueRepository->findOneBy(['code' => $code]);
        if ($historique) {
            $coffre = $historique->getCoffre();
            if ($coffre) {
                return [
                    'message' => [
                        'data' => [[
                            'id' => $coffre->getId(),
                            'name' => $coffre->getNom(),
                            'code' => $coffre->getCode(),
                            'old_code' => $historique->getCode()
                        ]],
                        'succes' => true
                    ],
                    'status' => 200
                ];
            }
        }

        return [
            'message' => [
                'data' => [],
                'error' => 'Coffre not found',
                'succes' => false
            ],
            'status' => 404
        ];
    }

    public function getAllCoffres(): array
    {
        $coffres = $this->coffreRepository->findAll();

        $data = array_map(function ($coffre) {
            return [
                'id' => $coffre->getId(),
                'name' => $coffre->getNom(),
                'code' => $coffre->getCode()
            ];
        }, $coffres);

        return [
            'message' => [
                'success' => true,
                'data' => $data
            ],
            'status' => 200
        ];
    }

    public function updateCoffreName(array $data): array
    {
        if (empty($data['id']) || empty($data['nom'])) {
            return [
                'message' => ['error' => 'Coffre ID and new name are required'],
                'status' => 400
            ];
        }

        $coffre = $this->coffreRepository->find($data['id']);
        if (!$coffre) {
            return [
                'message' => ['error' => 'Coffre not found'],
                'status' => 404
            ];
        }

        $coffre->setNom($data['nom']);
        $this->em->flush();

        return [
            'message' => [
                'success' => true,
                'id' => $coffre->getId(),
                'new_name' => $coffre->getNom()
            ],
            'status' => 200
        ];
    }

}
