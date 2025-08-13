<?php

namespace App\ApiResource\Dto\Input\Model;

use Symfony\Component\Serializer\Annotation\Groups;

class CheckDomainInput
{
    #[Groups(['CheckDomain:write'])]
    public ?int $modelId = null;

    #[Groups(['CheckDomain:write'])]
    public ?string $domain = null;
}