<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class RequestDataRemapper
{
    public function __construct(
        private RequestStack $requestStack
    ) {

    }

    public function remapRequestData(string $entityName):Request {
        $request = $this->requestStack->getCurrentRequest();
        $data = $request->request->all();
        $files = $request->files->all();

        // Initialize the mapped data arrays
        $mappedData = [];
        $mappedFiles = [];

        // Remap request data
        foreach ($data as $key => $value) {
            $mappedData[$entityName][$key] = $value;
            $request->request->remove($key);
        }

        // Remap file data
        foreach ($files as $key => $value) {
            $mappedFiles[$entityName]['file'][$key] = $value;
            $request->files->remove($key);
        }

        // modify the request
        if(isset($mappedData[$entityName])) $request->request->set($entityName, $mappedData[$entityName]);
        if(isset($mappedFiles[$entityName])) $request->files->set($entityName, $mappedFiles[$entityName]);


        return $request;
    }
}