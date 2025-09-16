<?php

namespace App\Services\ChatBot;

use App\Entity\Model;

class PageInformationBuilder
{
    public function buildJson(Model $parent): string
    {
        $pagePropsArray = [];

        foreach (array_merge([$parent], $parent->getChildren()->toArray()) as $child) {
            $pagePropsArray[$child->getUrl()] = [
                'pageStructure' => $child->getPropsPublished(),
                'seo' => json_encode($child->getSeo(), JSON_UNESCAPED_UNICODE),
            ];
        }

        return json_encode($pagePropsArray, JSON_UNESCAPED_UNICODE);
    }
}
