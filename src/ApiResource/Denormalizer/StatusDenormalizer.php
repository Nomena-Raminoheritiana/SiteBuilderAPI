<?php
namespace App\ApiResource\Denormalizer;
// src/Serializer/Denormalizer/StatusDenormalizer.php

use App\Entity\Status;
use App\Repository\StatusRepository;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;

class StatusDenormalizer implements DenormalizerInterface
{
    public function __construct(private StatusRepository $statusRepository) {}

    public function supportsDenormalization($data, $type, $format = null, array $context = []): bool
    {
        return $type === Status::class && is_string($data);
    }

    public function denormalize($data, $type, $format = null, array $context = []): Status
    {
        $status = $this->statusRepository->findOneBy(['code' => $data]);

        if (!$status) {
            throw new UnexpectedValueException("The status code '$data' is invalid");
        }

        return $status;
    }
    public function getSupportedTypes(?string $format): array
    {
        return  [
            Status::class => true,
        ];
    }
}
