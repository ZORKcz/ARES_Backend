<?php

namespace App\Service;

use App\Entity\Company;
use App\Repository\CompanyRepository;
use Doctrine\ORM\EntityManagerInterface;
use h4kuna\Ares;
use h4kuna\Ares\Exceptions\IdentificationNumberNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CompanyService
{
    private Ares\Ares $ares;
    private HttpClientInterface $httpClient;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CompanyRepository $companyRepository,
        HttpClientInterface $httpClient
    ) {
        $this->ares = (new Ares\AresFactory())->create();
        $this->httpClient = $httpClient;
    }

    public function getCompanyInfoByIco(string $ico): ?array
    {
        // TODO: Refactor: simplify
        $company = $this->companyRepository->findNewRecords(1, $ico);

        if (empty($company)) {
            $company = $this->fetchCompanyFromAresByIco($ico);
            if ($company === null) {
                return null;
            }
            $this->updateCompanyIfChanged($company);
        }

        if (is_array($company)) {
            $company = $company[0];
        }

        if (!$company) {
            $this->entityManager->persist($company);
            $this->entityManager->flush();

            return $this->convertCompanyToArray($company);
        }

        return $this->convertCompanyToArray($company);
    }

    public function getCompaniesByPartialName(string $partialName): array
    {
        $arrayOfChoices = [
            'obchodniJmeno' => $partialName,
            'pocet' => 10,
            'razeni' => [],
            'start' => 0,
        ];

        $response = $this->httpClient->request('POST', 'https://ares.gov.cz/ekonomicke-subjekty-v-be/rest/ekonomicke-subjekty/vyhledat', [
            'body' => json_encode($arrayOfChoices),
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ]);

        $data = $response->toArray();

        if (empty($data['ekonomickeSubjekty'])) {
            throw new NotFoundHttpException('No companies found');
        }

        $companies = array_slice($data['ekonomickeSubjekty'], 0, 10);
        $companyEntities = [];

        foreach ($companies as $companyData) {
            $company = $this->companyRepository->findOneBy([
                'ico' => $companyData['ico'],
            ]);

            if ($company === null) {
                $company = new Company();
            }

            $company->setName($companyData['obchodniJmeno']);
            $company->setAddressStreet($companyData['sidlo']['nazevUlice'] ?? '');
            $company->setAddressHousenumber($companyData['sidlo']['cisloDomovni']);
            $company->setAddressCity($companyData['sidlo']['nazevObce']);
            $company->setAddressPostalCode($companyData['sidlo']['psc']);
            $company->setAddressCounty($companyData['sidlo']['nazevKraje']);

            $this->entityManager->persist($company);

            $companyEntities[] = $this->convertCompanyToArray($company); // TODO: MS make into DTO builder
        }
        $this->entityManager->flush();

        return $companyEntities;
    }

    private function fetchCompanyFromAresByIco(string $ico): ?Company
    {
        try {
            $aresData = $this->ares->loadBasic($ico);
        } catch (IdentificationNumberNotFoundException) {
            throw new NotFoundHttpException('Company not found in ARES');
        }

        $company = new Company();
        $company->setIco($ico);
        $company->setName($aresData->original->obchodniJmeno);
        $company->setAddressStreet($aresData->original->sidlo->nazevUlice ?? '');
        $company->setAddressHousenumber($aresData->original->sidlo->cisloDomovni);
        $company->setAddressCity($aresData->original->sidlo->nazevObce);
        $company->setAddressPostalCode($aresData->original->sidlo->psc);
        $company->setAddressCounty($aresData->original->sidlo->nazevKraje);

        return $company;
    }

    private function updateCompanyIfChanged(Company $companyFromAres): void
    {
        //        $newData = $this->fetchCompanyFromAresByIco($ico);
        $existingCompany = $this->companyRepository->findOneBy([
            'ico' => $companyFromAres->getIco(),
        ]);

        if ($existingCompany) {
            $isUpdated = false;

            if ($existingCompany->getName() !== $companyFromAres->getName()) {
                $existingCompany->setName($companyFromAres->getName());
                $isUpdated = true;
            }
            if ($existingCompany->getAddressStreet() !== $companyFromAres->getAddressStreet()) {
                $existingCompany->setAddressStreet($companyFromAres->getAddressStreet());
                $isUpdated = true;
            }
            if ($existingCompany->getAddressHousenumber() !== $companyFromAres->getAddressHousenumber()) {
                $existingCompany->setAddressHousenumber($companyFromAres->getAddressHousenumber());
                $isUpdated = true;
            }
            if ($existingCompany->getAddressCity() !== $companyFromAres->getAddressCity()) {
                $existingCompany->setAddressCity($companyFromAres->getAddressCity());
                $isUpdated = true;
            }
            if ($existingCompany->getAddressPostalCode() !== $companyFromAres->getAddressPostalCode()) {
                $existingCompany->setAddressPostalCode($companyFromAres->getAddressPostalCode());
                $isUpdated = true;
            }
            if ($existingCompany->getAddressCounty() !== $companyFromAres->getAddressCounty()) {
                $existingCompany->setAddressCounty($companyFromAres->getAddressCounty());
                $isUpdated = true;
            }

            if ($isUpdated) {
                $existingCompany->setUpdatedAt();
                //                $this->entityManager->persist($existingCompany);
                $this->entityManager->flush();
            }
        }
    }

    private function convertCompanyToArray(Company $company): array
    {
        return [
            'ico' => $company->getIco(),
            'name' => $company->getName(),
            'address' => [
                'city' => $company->getAddressCity(),
                'street' => $company->getAddressStreet(),
                'housenumber' => $company->getAddressHousenumber(),
                'postalCode' => $company->getAddressPostalCode(),
                'county' => $company->getAddressCounty(),
            ],
        ];
    }
}
