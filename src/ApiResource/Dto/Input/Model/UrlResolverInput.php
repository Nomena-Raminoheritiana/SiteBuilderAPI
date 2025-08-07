<?php
namespace App\ApiResource\Dto\Input\Model;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

final class UrlResolverInput
{
    #[Assert\NotBlank]
    #[Groups(['resolve_url:write'])]
    public ?string $url = null;

    #[Assert\NotBlank]
    #[Groups(['resolve_url:write'])]
    public mixed $modelId = null;
}
