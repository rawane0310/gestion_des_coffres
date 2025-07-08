<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Repository\UserRepository;

class UserManager
{
    private EntityManagerInterface $em;
    private UserPasswordHasherInterface $passwordHasher;
    private UserRepository $userRepository;

    public function __construct(
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
        UserRepository $userRepository
    ) {
        $this->em = $em;
        $this->passwordHasher = $passwordHasher;
        $this->userRepository = $userRepository;
    }

    public function createUser(array $data): array
    {
        if (empty($data['email']) || empty($data['password'])) {
            return [
                'message' => ['error' => 'Email and password are required'],
                'status' => Response::HTTP_BAD_REQUEST
            ];
        }

        // Check if user already exists
        $existing = $this->userRepository->findOneBy(['email' => $data['email']]);
        if ($existing) {
            return [
                'message' => ['error' => 'User already exists'],
                'status' => Response::HTTP_CONFLICT
            ];
        }

        $user = new User();
        $user->setEmail($data['email']);

        $hashedPassword = $this->passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);

        $this->em->persist($user);
        $this->em->flush();

        return [
            'message' => [
                'success' => true,
                'email' => $user->getEmail()
            ],
            'status' => Response::HTTP_OK
        ];
    }
}
