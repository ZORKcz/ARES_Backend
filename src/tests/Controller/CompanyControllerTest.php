<?php

namespace App\tests\Controller;

use App\Entity\Company;
use App\tests\ApiTestCase;

class CompanyControllerTest extends ApiTestCase
{
    public const EXPECTED_RETURN = [
        'ico' => '61679992',
        'name' => 'Proof & Reason, s.r.o.',
        'address' => [
            'city' => 'Hořice',
            'street' => 'Kollárova',
            'housenumber' => '703',
            'postalCode' => '50801',
            'county' => 'Královéhradecký kraj',
        ],
    ];

    public function testGetCompanyByICValid(): void
    {
        $response = $this->requestAndGetResponseWithAssert(
            method: 'GET',
            uri: '/api/company/61679992',
        );

        $responseData = $this->getContentFromResponse($response);

        //TODO: fix properly
        foreach ($this::EXPECTED_RETURN as $key => $value) {
            $this->assertArrayHasKey($key, $responseData, "Key '{$key}' not found in response data");
            if (is_array($value)) {
                foreach ($value as $subKey => $subValue) {
                    $this->assertArrayHasKey($subKey, $responseData[$key]);
                    $this->assertEquals($this::EXPECTED_RETURN[$key][$subKey], $responseData[$key][$subKey]);
                }

                continue;
            }
            $this->assertEquals($this::EXPECTED_RETURN[$key], $responseData[$key]);
        }

        $this->assertTrue($this->isIcoInDatabase('61679992'));
    }

    public function testGetCompanyByICInvalid()
    {
        $response = $this->requestAndGetResponseWithAssert(
            method: 'GET',
            uri: '/api/company/61679999',
            expectedSuccess: false
        );

        $responseData = $this->getContentFromResponse($response);

        $this->assertArrayHasKey('error', $responseData);
        $this->assertEquals('Company not found in ARES', $responseData['error']);

        $this->assertFalse($this->isIcoInDatabase('61679999'));
    }

    public function isIcoInDatabase(string $ico): bool
    {
        $container = $this->client->getContainer();
        $entityManager = $container->get('doctrine')->getManager();
        $companyRepository = $entityManager->getRepository(Company::class);
        $company = $companyRepository->findOneBy([
            'ico' => $ico,
        ]);

        return $company != null;
    }
    // TODO: Přidej sem ještě zbytek funkcí a popiš k nim, co dělají
}
