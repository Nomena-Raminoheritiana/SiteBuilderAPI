<?php

namespace App\Services\ChatBot;

class TemplateLoader
{
    public function load(string $path): string
    {
        if (!file_exists($path)) {
            throw new \RuntimeException("Template file not found: $path");
        }

        return file_get_contents($path);
    }
}
