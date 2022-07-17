<?php
namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
Use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route(path: '/api')]
class SecurityController extends AbstractController {

    private UserRepository $userRepository;
    private EmailSender $emailSender;

    public function __construct(UserRepository $userRepository, private SerializerInterface $serializer, EmailSender $emailSender)
    {
        $this->userRepository = $userRepository;
        $this->emailSender = $emailSender;
    }

    #[Route(path: '/login_check', name: 'api.login', methods : ('POST'))]
    public function login(): JsonResponse {
        $user = $this->getUser();
        return new JsonResponse([
            'username' => $user->getUsername(),
            'roles' => $user->getRoles()
        ]);
    }

    #[Route(path: '/register', name: 'api_register', methods : ('POST'))]
    public function register(Request $request) {
        $jsonData = json_decode($request->getContent(), true);

        $user = $this->userRepository->findOneBy([
            'email' => $jsonData['email'],
        ]);
        if (!is_null($user)) {
            return new JsonResponse([
                'message' => 'User already exists'
            ], status: Response::HTTP_CONFLICT);
        }
        if (!isset($jsonData['email']) || !isset($jsonData['firstname']) || !isset($jsonData['lastname']) || !isset($jsonData['password'])){
            return new JsonResponse([
                'message' => 'Incomplete form'
            ], status: Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $jsonData['Roles'] = ['ROLE_ARTIST'];
        $user = $this->userRepository->create($jsonData);

        $this->emailSender->emailSender(['user' => $user]);

        return new JsonResponse(
            json_decode($this->serializer->serialize($user, 'json')),
            status: Response::HTTP_CREATED
        );
    }
}
