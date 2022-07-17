<?php
namespace App\Controller;

use App\Entity\Work;
use App\Entity\WorkFiles;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/api')]
class PutWorkUpdatedAtAction extends AbstractController
{
    public function __construct(private EntityManagerInterface $em){}

    public function __invoke(Request $request): Work
    {
        $work = $request->attributes->get('data');

        if (!($work instanceof Work)){
            throw new \RuntimeException('Work attendu');
        }

        $work->setUpdatedAt(new DateTime('now'));

        return $work;
    }
}