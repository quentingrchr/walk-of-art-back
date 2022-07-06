<?php

namespace App\Serializer;

use App\Entity\Work;
use App\Entity\WorkFiles;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Vich\UploaderBundle\Storage\StorageInterface;

class WorkFileNormalizer implements ContextAwareNormalizerInterface, NormalizerAwareInterface
{

    use NormalizerAwareTrait;

    private const ALREADY_CALLED = 'WORK_FILE_NORMALIZER_ALREADY_CALLED';

    public function __construct(private StorageInterface $storage){}

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        if (isset($context[self::ALREADY_CALLED])) {
            return false;
        }

        return $data instanceof WorkFiles;
    }

    public function normalize($object, ?string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
    {
        $context[self::ALREADY_CALLED] = true;

        $object->setFileUrl($this->storage->resolveUri($object, 'file'));

        return $this->normalizer->normalize($object, $format, $context);
    }

}