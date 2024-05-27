<?php

namespace App\Service;

use _PHPStan_7961f7ae1\Nette\Neon\Exception;
use App\Entity\Company;
use App\Repository\CompanyRepository;
use Doctrine\ORM\EntityManagerInterface;
//use h4kuna\Ares\Ares;
use h4kuna\Ares\Exceptions\IdentificationNumberNotFoundException;

class CompanyService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private CompanyRepository $companyRepository,
//        private Ares $ares
    ) {
    }

    public function getCompanyInfo(string $ico): ?Company
    {
        $company = $this->companyRepository->findOneBy([
            'ico' => $ico,
        ]);

        if (!$company) {
            $company = $this->fetchCompanyFromAresByIco($ico);

            if ($company) {
                $this->entityManager->persist($company);
                $this->entityManager->flush();
            }
        }

        return $company;
    }

    private function fetchCompanyFromAresByIco(string $ico): ?Company
    {
        try {
            $aresData = '§test';
        } catch (IdentificationNumberNotFoundException $e) {
            throw new Exception('Company not found in ARES');
        }

        $company = new Company();
        $company->setIco($ico);
//        $company->setName($aresData->getCompanyName());
//        $company->setAddressStreet($aresData->getStreet());
//        $company->setAddressHousenumber($aresData->getStreetNumber());
//        $company->setAddressCity($aresData->getTown());
//        $company->setAddressPostalCode($aresData->getZip());
//        $company->setAddressCounty($aresData->getDistrict());

        return $company;
    }
}
