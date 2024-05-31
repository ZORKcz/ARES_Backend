<?php

namespace App\Controller;

use App\Service\CompanyService;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/company', name: 'get_company_')]
class CompanyController extends AbstractController
{
    #[OA\Get(
        summary: 'Get companies by partial name',
        tags: ['Company'],
        parameters: [
            new OA\QueryParameter(
                name: 'name',
                description: 'Partial name of the company',
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Returns data',
                content: new OA\JsonContent(
                    schema: 'array'
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Internal server error'
            ),
        ]
    )]
    #[Route('/search', name: 'search', methods: ['GET'])]
    public function getCompaniesByPartialName(#[MapQueryParameter] string $name): JsonResponse
    {
        try {
            $companies = $this->companyService->getCompaniesByPartialName($name);

            if (empty($companies)) {
                return $this->json([
                    'error' => 'No companies found',
                ], Response::HTTP_NOT_FOUND);
            }

            return $this->json($companies, Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function __construct(
        private readonly CompanyService $companyService
    ) {
    }

    #[OA\Get(
        summary: 'Get company by ICO',
        tags: ['Company'],
        parameters: [
            new OA\PathParameter(
                name: 'ico',
                description: 'ICO'
            )],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Returns data',
                content: new OA\JsonContent(
                    schema: 'array',
                    example: [
                        'ico' => 12345678,
                        'name' => 'Company name',
                        'address' => 'Company address',
                    ]
                ),
            ),
        ]
    )]
    #[Route('/{ico}', name: 'ico', methods: ['GET'])]
    public function getCompanyInfo(string $ico): JsonResponse
    {
        try {
            $company = $this->companyService->getCompanyInfoByIco($ico);

            if (!$company) {
                return $this->json([
                    'error' => 'Company not found',
                ], Response::HTTP_NOT_FOUND);
            }

            return $this->json($company, Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
