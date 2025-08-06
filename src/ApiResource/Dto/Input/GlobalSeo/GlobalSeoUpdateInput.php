<?php
namespace App\ApiResource\Dto\Input\GlobalSeo;

use Symfony\Component\Serializer\Annotation\Groups;

final class GlobalSeoUpdateInput
{
    #[Groups(['GlobalSeo:write'])]
    public string $model; // L'IRI : ex "/api/models/123"

    #[Groups(['GlobalSeo:write'])]
    public string $category; // L'IRI : ex "/api/models/123"

    #[Groups(['GlobalSeo:write'])]
    public ?string $modelName = null;

    #[Groups(['GlobalSeo:write'])]
    public ?array $formValue = null;

    #[Groups(['GlobalSeo:write'])]
    public ?array $metadata = null;

    #[Groups(['GlobalSeo:write'])]
    public ?SeoData $seo = null;
}

final class SeoData
{
    #[Groups(['GlobalSeo:write'])]
    public ?string $seoTitle = null;

    #[Groups(['GlobalSeo:write'])]
    public ?string $seoDescription = null;

    #[Groups(['GlobalSeo:write'])]
    public ?array $seoKeywords = null;
}