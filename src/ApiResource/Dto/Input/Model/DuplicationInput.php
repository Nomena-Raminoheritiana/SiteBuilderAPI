<?php

namespace App\ApiResource\Dto\Input\Model;

use Symfony\Component\Serializer\Annotation\Groups;

class DuplicationInput
{
    #[Groups(['duplicate:write'])]
    public ?int $modelId = null;
}