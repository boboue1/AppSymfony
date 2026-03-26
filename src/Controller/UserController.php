<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
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

    /**
     * Récupère les détails d'un utilisateur spécifique
     * Accessible uniquement aux administrateurs (ROLE_ADMIN)
     */
    #[Route('/api/users/{id}', name: 'api_users_detail', methods: ['GET'])]
    public function show(User $user): JsonResponse
    {
        $data = [
            'id'               => $user->getId(),
            'nom'              => $user->getNom(),
            'email'            => $user->getEmail(),
            'roles'            => $user->getRoles(),
            'date_inscription' => $user->getDateInscription()?->format('Y-m-d H:i:s'),
        ];

        return new JsonResponse($data, JsonResponse::HTTP_OK);
    }

    /**
     * Crée un nouvel utilisateur
     * Accessible uniquement aux administrateurs (ROLE_ADMIN)
     */
    #[Route('/api/users', name: 'api_users_create', methods: ['POST'])]
public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        // Logique de création d'un nouvel utilisateur (ex: validation des données, enregistrement en base)
        $data = json_decode($request->getContent(), true);

        $user = new User();
        $user->setNom($data['nom']);
        $user->setEmail($data['email']);
        $user->setRoles($data['roles'] ?? ['ROLE_USER']);
        $user->setDateInscription(new \DateTimeImmutable());

        $em->persist($user);
        $em->flush();

        return new JsonResponse(['message' => 'Utilisateur créé avec succès'], JsonResponse::HTTP_CREATED);
    }

     /**
     * Met à jour les informations d'un utilisateur spécifique
     * Accessible uniquement aux administrateurs (ROLE_ADMIN)
     */
    #[Route('/api/users/{id}', name: 'api_users_update', methods: ['PUT'])]

public function update(Request $request, User $user, EntityManagerInterface $em): JsonResponse
    {
        // Logique de mise à jour de l'utilisateur (ex: validation des données, enregistrement en base)
        $data = json_decode($request->getContent(), true);

        $user->setNom($data['nom'] ?? $user->getNom());
        $user->setEmail($data['email'] ?? $user->getEmail());
        $user->setRoles($data['roles'] ?? $user->getRoles());

        $em->flush();

        return new JsonResponse(['message' => 'Utilisateur mis à jour avec succès'], JsonResponse::HTTP_OK);
    }

     /**
     * Supprime un utilisateur spécifique
     * Accessible uniquement aux administrateurs (ROLE_ADMIN)
     */
    #[Route('/api/users/{id}', name: 'api_users_delete', methods: ['DELETE'])]
    public function delete(User $user, EntityManagerInterface $em): JsonResponse
    {
        // Logique de suppression de l'utilisateur (ex: suppression en base)
        // ...

        $em->remove($user);
        $em->flush();

        return new JsonResponse(['message' => 'Utilisateur supprimé avec succès'], JsonResponse::HTTP_OK);
    }
}

