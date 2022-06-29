<?php


namespace App\Common;

use App\Entity\User;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Throwable;

class JwtFindUserDecoder extends AbstractController
{
    private UserRepository $userRepository;
    private JWTTokenManagerInterface $jwtManager;
    private TokenStorageInterface $tokenStorageInterface;

    public function __construct(UserRepository $userRepository, JWTTokenManagerInterface $jwtManager, TokenStorageInterface $tokenStorageInterface)
    {
        $this->userRepository = $userRepository;
        $this->jwtManager = $jwtManager;
        $this->tokenStorageInterface = $tokenStorageInterface;
    }

    public function findUser(): User|string
    {
        try {
            return $this->userRepository->findBy(['email' => $this->checkIfUser()["username"]])[0];
        } catch (Throwable $e) {
            return 'Erreur : '. $e;
        }
    }

    public function checkIfUser(): array|string
    {
        $data =  $this->jwtManager->decode($this->tokenStorageInterface->getToken());

        if($data != null) {
            return $data;
        }
        return "Aucun utilisateur n'est connecté.";
    }
}