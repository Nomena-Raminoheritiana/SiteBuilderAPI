<?php
namespace App\ApiResource\Dto\Input\Model;

use Symfony\Component\Serializer\Annotation\Groups;

final class ModelSyncInput {
    #[Groups(['Model:sync-write'])]
    public array $defaultProps;
    #[Groups(['Model:sync-write'])]
    public string $slug;
    #[Groups(['Model:sync-write'])]
    public array $forceKeys = [];
}