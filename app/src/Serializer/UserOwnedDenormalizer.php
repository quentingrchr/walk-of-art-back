<?php

namespace App\Serializer;

use App\Entity\UserOwnedInterface;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;

class UserOwnedDenormalizer implements ContextAwareDenormalizerInterface, DenormalizerAwareInterface
{

    use DenormalizerAwareTrait;

    private const ALREADY_CALLED_DENORMALIZER = "UserOwnedDenormalizerCalled";

    public function __construct(private Security $security, private UserRepository $userRepository){}

    public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
    {
        $reflectionClass = new \ReflectionClass($type);
        $alreadyCalled = $context[$this->getAlreadyCalledKey($type)] ?? false;
        return $reflectionClass->implementsInterface(UserOwnedInterface::class) && $alreadyCalled === false;
    }

    public function denormalize(mixed $data, string $type, string $format = null, array $context = [])
    {
        $context[$this->getAlreadyCalledKey($type)] = true;
        /** @var UserOwnedInterface $entity */
        $entity = $this->denormalizer->denormalize($data, $type, $format, $context);
        $user = $this->userRepository->find($this->security->getUser()->getId()); // TODO: Enlevé l'appel a la db
        $entity->setUser($user);
//        $entity->setUser($this->security->getUser());
        return $entity;
    }

    private function getAlreadyCalledKey(string $type){
        return self::ALREADY_CALLED_DENORMALIZER . $type;
    }
}