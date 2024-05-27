<?php

namespace App\tests\Controller;

use App\Entity\Company;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class CompanyControllerTest extends WebTestCase
{
    public const EXPECTED_RETURN_KEYS = ['ico', 'name', 'addressCity', 'addressStreet', 'addressHousenumber', 'addressPostalCode',
        'addressCounty'];
    public const EXPECTED_RETURN_VALUES = ['61679992', 'Proof & Reason, s.r.o.', 'Hořice', 'Kollárova', '703', '50801',
        'Královéhradecký kraj'];

    public function testGetCompanyByICValid()
    {
        $client = static::createClient();
        $client->request('GET', '/company/validIC');

        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());

        $responseContent = $client->getResponse()->getContent();
        $responseData = json_decode($responseContent, true);

        foreach ($this::EXPECTED_RETURN_KEYS as $index => $key) {
            $this->assertArrayHasKey($key, $responseData);
            $this->assertEquals($this::EXPECTED_RETURN_VALUES[$index], $responseData[$key]);
        }

        $this->assertTrue($this->isIcoInDatabase('61679992', $client));
    }

    public function testGetCompanyByICInvalid()
    {
        $client = static::createClient();
        $client->request('GET', '/company/invalidIC');

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        $this->assertJson($client->getResponse()->getContent());

        $responseContent = $client->getResponse()->getContent();
        $responseData = json_decode($responseContent, true);

        $this->assertArrayHasKey('error', $responseData);
        $this->assertEquals('Company not found in ARES', $responseData['error']);

        $this->assertFalse($this->isIcoInDatabase('61679999', $client));
    }

    public function isIcoInDatabase(string $ico, $client): bool
    {
        $container = $client->getContainer();
        $entityManager = $container->get('doctrine')->getManager();
        $companyRepository = $entityManager->getRepository(Company::class);
        $company = $companyRepository->findOneBy([
            'ico' => $ico,
        ]);

        return $company != null;
    }

    /* Přidej sem ještě zbytek funkcí a popiš k nim, co dělají */
}
