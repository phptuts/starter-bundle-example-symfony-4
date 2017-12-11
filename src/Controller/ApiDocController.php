<?php

namespace App\Controller;

use Nelmio\ApiDocBundle\ApiDocGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiDocController extends Controller
{
    /**
     * @var ApiDocGenerator
     */
    private $apiDocGenerator;

    public function __construct(ApiDocGenerator $apiDocGenerator)
    {
        $this->apiDocGenerator = $apiDocGenerator;
    }

    /**
     * @Route(name="api_docs", path="api-docs", methods={"GET"})
     *
     * @param Request $request
     * @return Response
     */
    public function apiDoc(Request $request)
    {
        $spec = $this->apiDocGenerator->generate()->toArray();
        if ('' !== $request->getBaseUrl()) {
            $spec['basePath'] = $request->getBaseUrl();
        }

        return $this->render('@NelmioApiDoc/SwaggerUi/index.html.twig', ['swagger_data' => ['spec' => $spec]]);
    }
}