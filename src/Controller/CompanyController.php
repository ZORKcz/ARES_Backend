<?php

namespace App\Controller;

use App\Service\CompanyService;
use http\Env\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

#[Route('/company', name: 'get_company_')]
class CompanyController extends AbstractController
{

    public function __construct(
        private readonly CompanyService $companyService
    )
    {
    }

    #[OA\Get(
        summary: 'get company by ICO',
        tags: ['Company'],
        parameters: [
            new OA\QueryParameter(
                name: 'ico',
                description: 'Identifikační číslo občana',
                required: true,
                schema: new OA\Schema(type: 'string')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Returns the rewards of an user',
                content: new OA\JsonContent(
                    type: 'array',
                )
            )
        ]
    )]
    #[Route('/ico', name: 'ico', methods: ['GET'])]
    public function getCompanyInfo(string $ico): JsonResponse
    {
        try
        {
            $company = $this->companyService->getCompanyInfo($ico);

            if (!$company)
            {
                return $this->json(['error' => 'Company not found'], 404);
            }
            return $this->json($company);
        }
        catch (\Exception $e)
        {
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }
}
