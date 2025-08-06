<?php
namespace App\ApiResource\Normalizer\Model;

use App\Entity\Model;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class ModelCompactNormalizer implements NormalizerInterface
{
    private const ALREADY_CALLED = 'MODEL_COMPACT_NORMALIZER_ALREADY_CALLED';
    private const MAX_DEPTH = 2; // Profondeur max que tu veux (2 ou 3)

    private ObjectNormalizer $normalizer;

    public function __construct(ObjectNormalizer $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    public function supportsNormalization($data, $format = null, array $context = []): bool
    {
        // Ce normalizer s'applique uniquement sur Model et si on demande ce groupe précis
        return $data instanceof Model
            && isset($context['groups'])
            && in_array('Model:compact:read', $context['groups']);
    }

    public function normalize($object, ?string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
{
    // On s'assure que le groupe est bien présent dans le contexte
    if (!isset($context['groups']) || !in_array('Model:compact:read', $context['groups'])) {
        $context['groups'][] = 'Model:compact:read';
    }

    // Empêcher récursion infinie
    if (isset($context[self::ALREADY_CALLED])) {
         // Normaliser status et children avec le bon groupe
        $status = $object->getStatus() ? $this->normalizer->normalize($object->getStatus(), $format, $context) : null;
        
        $children = [];
        foreach ($object->getChildren() as $child) {
            $children[] = $this->normalize($child, $format, $context);
        }
        
        return [
            'id' => $object->getId(),
            'url' => $object->getUrl(),
            'status' => $status,
            'children' => $children,
        ];
    }

    $context[self::ALREADY_CALLED] = true;

    // Gestion profondeur
    $depth = $context['depth'] ?? 0;
    if ($depth >= self::MAX_DEPTH) {
        return ['id' => $object->getId()];
    }

    $context['depth'] = $depth + 1;
    $context['iri_only'] = false;

    // Normalisation principale avec groupes
    $data = $this->normalizer->normalize($object, $format, $context);

    // Parent avec le même contexte (pour respecter groupes)
    if ($object->getParent() !== null) {
        $data['parent'] = $this->normalize($object->getParent(), $format, $context);
    } else {
        $data['parent'] = null;
    }

    // Children avec le même contexte
    $data['children'] = [];
    foreach ($object->getChildren() as $child) {
        $data['children'][] = $this->normalize($child, $format, $context);
    }

    return $data;
}


    public function getSupportedTypes(?string $format): array
    {
        return [
            Model::class => true,
        ];
    }
}