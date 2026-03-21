<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;


final class UserController extends AbstractController
{
    /**
     * Liste tous les utilisateurs
     * Accessible uniquement aux administrateurs (ROLE_ADMIN)
     */
    #[Route('/api/users', name: 'api_users_list', methods: ['GET'])]
    public function index(UserRepository $repository): JsonResponse
    {
        $users = $repository->findAll();

        $data = array_map(function (User $user) {
            return [
                'id'               => $user->getId(),
                'nom'              => $user->getNom(),
                'email'            => $user->getEmail(),
                'roles'            => $user->getRoles(),
                'date_inscription' => $user->getDateInscription()?->format('Y-m-d H:i:s'),
            ];
        }, $users);

        return new JsonResponse($data, JsonResponse::HTTP_OK);
    }
}
