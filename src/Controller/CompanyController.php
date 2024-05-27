<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
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
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                schema: 'array',
                example: [
                    'ico' => 12345678,
                ]
            )
        ),
        tags: ['Company'],
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
    #[Route('/ico', name: 'ico', methods: ['POST'])]
    public function getCompanyInfo(Request $request): JsonResponse
    {
        $content = $request->getContent();
        if (!$content) {
            return $this->json([
                'error' => 'Missing ICO',
            ], 400);
        }

        try {
            $company = $this->companyService->getCompanyInfo(json_decode($content['ico'], true));

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
