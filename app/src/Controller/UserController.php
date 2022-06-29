<?php

namespace App\Controller;

use App\Common\ResponseRenderer;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[AsController]
class UserController extends AbstractController {

    private UserRepository $userRepository;
    private EntityManagerInterface $entityManager;
    private JWTTokenManagerInterface $jwtManager;
    private TokenStorageInterface $tokenStorageInterface;
    private ResponseRenderer $response;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $entityManager, JWTTokenManagerInterface $jwtManager, TokenStorageInterface $tokenStorageInterface,
                                ResponseRenderer $response)
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->jwtManager = $jwtManager;
        $this->tokenStorageInterface = $tokenStorageInterface;
        $this->response = $response;
    }

    #[Route(path: '/api/artistes', name: 'app_api_artists')]
    public function artists(): array|string
    {
        $data =  $this->jwtManager->decode($this->tokenStorageInterface->getToken());

        if($data != null) {
            $user = $this->userRepository->findBy(['email' => $data['username']])[0];

            if(in_array('ROLE_MODERATOR', $user->getRoles()) || in_array('ROLE_ADMIN', $user->getRoles())) {
                $users = $this->userRepository->findByRoles('ROLE_ARTIST');

                $arrayOfUsers = [];

                foreach($users as $user) {
                    $arrayOfUsers[] = $user->jsonSerialize();
                }

                return $this->response->response($arrayOfUsers);
            }
            return "L'utilisateur n'est pas un modérateur.";
        }
        return "Aucun utilisateur n'est connecté.";
    }

    #[Route('/update-profile/{id}', name: 'app_api_update_profile', methods: ['GET', 'POST'])]
    public function updateProfile(Request $request, User $user)
    {
        $data = json_decode($request->getContent(), true);

        if($data != null && $user->getUserIdentifier() == $data['username']) {
            $user = $this->userRepository->findBy(['email' => $data['username']])[0];
            $user->setEmail($data['username'])
                ->setPassword($this->hasher->hashPassword($user, $data['password']));

            try {
                $this->entityManager->persist($user);
                $this->entityManager->flush();

                echo 'success';
                die;
            } catch (ORMException $e) {
                echo $e;
            }
        }
    }
}