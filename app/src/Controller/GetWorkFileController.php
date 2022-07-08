<?php


namespace App\Controller;

use App\Entity\WorkFiles;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route(path: '/api')]
class GetWorkFileController extends AbstractController
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function __invoke(Request $request)
    {
        $workFile = $request->attributes->get('data');

        if (!($workFile instanceof WorkFiles)){
            throw new \RuntimeException('Work file expected.');
        }

        if (is_null($this->security->getUser())) {
            throw new \RuntimeException('The current user is not logged in.');
        }

        if($this->security->getUser()->getUserIdentifier() !== $workFile->getWork()->getUser()->getUserIdentifier()) {
            throw new \RuntimeException('It is not the current user file.');
        }

        return $workFile;
    }
}