<?php

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\OpenApi;
use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;

final class ProjectApiFactory implements OpenApiFactoryInterface
{
    public function __construct(private OpenApiFactoryInterface $decorated)
    {
    }

    // Customize the OpenApi object
    public function __invoke(array $context = []): OpenApi
    {
        // Retrieve the OpenApi object from the decorated factory
        $openApi = $this->decorated->__invoke($context);

        // Retrieve the paths from the OpenApi object
        $randomItem = $openApi->getPaths()->getPath('/api/projects/random');

        // Retrieve the GET operation from the path
        $getItem = $openApi->getPaths()->getPath('/api/projects/{id}');

        // randomOperation is a copy of the GET operation
        $randomOperation = $randomItem->getGet();

        $getOperation = $getItem->getGet();

        $randomOperation->addResponse($getOperation->getResponses()[200], 200);

        $randomOperation = $randomOperation
            ->withSummary('Retrieve random Project resource.')
            ->withDescription('Retrieve random Project resource.');

        $randomItem = $randomItem->withGet($randomOperation);

        $openApi->getPaths()->addPath('/api/projects/random', $randomItem);

        return $openApi;
    }
}
