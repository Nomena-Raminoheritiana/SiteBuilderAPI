<?php

namespace App\Services\ChatBot;

use App\Entity\Model;

class PromptBuilder
{
    public function __construct(
        private PageInformationBuilder $pageInformationBuilder,
        private GlobalSeoBuilder $GlobalSeoBuilder,
        private TemplateLoader $templateLoader,
        private string $templatePath
    ) {}

    public function buildSubject(Model $model): string
    {
        $pageInformationJson = $this->pageInformationBuilder->buildJson($model);
        $seoJson = $this->GlobalSeoBuilder->buildJson($model);
        $additionalInfo = $model->getChatBotConfig()['subject'] ?? '';

        $template = $this->templateLoader->load($this->templatePath);

        return str_replace(
            ['[JSON_PROPS]', '[SEO_PROPS]', '[ADDITIONAL_INFORMATION]'],
            [$pageInformationJson, $seoJson, $additionalInfo],
            $template
        );
    }
}
