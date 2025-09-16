<?php

namespace App\ApiResource\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Model;

class ModelPatchProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private ProcessorInterface $persistProcessor
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Model
    {
        if (!$data instanceof Model) {
            return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
        }

        $payload = $context['request']->toArray() ?? [];

        // Si le patch ne contient que "chatBotConfig"
        if (array_keys($payload) === ['chatBotConfig']) {
            $parent = $data->getParent();
            if ($parent) {
                $parent->setChatBotConfig($data->getChatBotConfig());
                $data->setChatBotConfig(null);
                $this->em->persist($parent);
                $this->em->flush();

                return $parent;
            }
        }

        // sinon, comportement normal
        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}
