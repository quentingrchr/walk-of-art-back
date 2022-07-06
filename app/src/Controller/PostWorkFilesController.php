<?php
namespace App\Controller;

use App\Entity\Work;
use App\Entity\WorkFiles;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/api')]
class PostWorkFilesController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em){}

    public function __invoke(Request $request): Work
    {
        $work = $request->attributes->get('data');
        if (!($work instanceof Work)){
            throw new \RuntimeException('Work attendu');
        }

        $uploadedFiles = $request->files->get('file');
        $mainFiles = $request->files->get('mainFile');


        if (!$uploadedFiles && !$mainFiles) {
            throw new BadRequestHttpException('"file" is required');
        }


        if ($mainFiles) {
            $this->addFile($work, $mainFiles, true);
        }
        if (is_array($uploadedFiles)){
            foreach ($uploadedFiles as $uploadedFile){
                $this->addFile($work, $uploadedFile);
            }
        }
        elseif ($uploadedFiles){
            $this->addFile($work, $uploadedFiles);
        }

        return $work;
    }

    public function addFile(Work &$work, UploadedFile $uploadedFile, bool $main = false)
    {
        $workFile = new WorkFiles();
        $workFile->setFile($uploadedFile);

        $workFile->setWork($work);
        $this->em->persist($workFile);

        if (!$work->getMainFile() || $main){
            $work->setMainFile($workFile);
        }
    }
}