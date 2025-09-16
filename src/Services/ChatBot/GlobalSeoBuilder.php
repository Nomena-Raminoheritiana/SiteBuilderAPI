<?php

namespace App\Services\ChatBot;

use App\Entity\Model;

class GlobalSeoBuilder
{
    public function buildJson(Model $model): string
    {
        $globalSeo = $model->getGlobalSeo();

        $metadataJson = json_encode($globalSeo->getMetadata(), JSON_UNESCAPED_UNICODE);
        $formValueJson = json_encode($globalSeo->getFormValue(), JSON_UNESCAPED_UNICODE);

        return $metadataJson . $formValueJson;
    }
}
