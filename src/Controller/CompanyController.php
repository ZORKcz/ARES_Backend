<?php

namespace App\Controller;

use App\Service\CompanyService;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/company', name: 'get_company_')]
class CompanyController extends AbstractController
{
    public function __construct(
        private readonly CompanyService $companyService
    ) {
    }

    #[OA\Post(
        summary: 'get company by ICO',
        tags: ['Company'],
        parameters: [new OA\PathParameter(
            name: 'ico',
            description: 'LFG ICO'
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
    public function getCompanyInfo(int $ico): JsonResponse
    {
        try {
            $company = $this->companyService->getCompanyInfoByIco($ico);

            if (!$company) {
                return $this->json([
                    'error' => 'Company not found',
                ], 404);
            }

            return $this->json($company);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
