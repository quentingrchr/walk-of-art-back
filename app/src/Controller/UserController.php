<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\SerializerInterface;

#[Route(path: '/api')]
class UserController extends AbstractController {

    private UserRepository $userRepository;
    private EntityManagerInterface $entityManager;
    private JWTTokenManagerInterface $jwtManager;
    private TokenStorageInterface $tokenStorageInterface;
    private Security $security;
    private SerializerInterface $serializer;
    private UserPasswordHasherInterface $passwordEncoder;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $entityManager, JWTTokenManagerInterface $jwtManager, TokenStorageInterface $tokenStorageInterface,
                                Security $security, SerializerInterface $serializer, UserPasswordHasherInterface $passwordEncoder)
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->jwtManager = $jwtManager;
        $this->tokenStorageInterface = $tokenStorageInterface;
        $this->security = $security;
        $this->serializer = $serializer;
        $this->passwordEncoder = $passwordEncoder;
    }

    #[Route(path: '/artists', name: 'app_api_artists')]
    public function artists(): JsonResponse
    {
        if (is_null($this->security->getUser())) {
            return new JsonResponse([
                'message' => 'User not logged in'
            ], status: Response::HTTP_CONFLICT);
        }

        if(in_array('ROLE_MODERATOR', $this->security->getUser()->getRoles()) || in_array('ROLE_ADMIN',  $this->security->getUser()->getRoles())) {
            $users = $this->userRepository->findAll();

            $arrayOfUsers = [];

            foreach($users as $user) {
                if(in_array('ROLE_ARTIST', $user->getRoles()))
                $arrayOfUsers[] = $user;
            }

            return new JsonResponse(
                json_decode($this->serializer->serialize($arrayOfUsers, 'json')),
                status: Response::HTTP_CREATED);
        }

        return new JsonResponse([
            'message' => 'This user it is not a moderator or an admin'
        ], status: Response::HTTP_CONFLICT);
    }

    #[Route('/update-profile/{id}', name: 'app_api_update_profile', methods: ['POST'])]
    public function updateProfile(Request $request, User $user): JsonResponse
    {
        if (is_null($this->security->getUser())) {
            return new JsonResponse([
                'message' => 'User not logged in'
            ], status: Response::HTTP_CONFLICT);
        }

        $jsonData = json_decode($request->getContent(), true);

        if (is_null($jsonData)) {
            return new JsonResponse([
                'message' => 'Incomplete form'
            ], status: Response::HTTP_CONFLICT);
        }

        if($this->security->getUser() != null && $user->getUserIdentifier() == $this->security->getUser()->getUserIdentifier()) {
            $user = $this->userRepository->findBy(['email' => $user->getUserIdentifier()])[0];
            $user->setEmail($jsonData['username'])
                ->setPassword($this->passwordEncoder->hashPassword($user, $jsonData['password']));

            try {
                $this->entityManager->persist($user);
                $this->entityManager->flush();

                return new JsonResponse([
                    'message' => 'User updated'
                ], status: Response::HTTP_CONFLICT);

            } catch (ORMException $e) {
                return new JsonResponse([
                    'message' => 'User updated'
                ], status: Response::HTTP_CONFLICT);
            }
        }

        return new JsonResponse([
            'message' => 'User not found'
        ], status: Response::HTTP_CONFLICT);
    }
}