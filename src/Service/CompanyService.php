<?php

namespace App\Service;

use _PHPStan_7961f7ae1\Nette\Neon\Exception;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CompanyRepository;
use App\Entity\Company;
use h4kuna\Ares\Ares;
use h4kuna\Ares\Exceptions\IdentificationNumberNotFoundException;

class CompanyService
{
    private $entityManager;
    private $companyRepository;
    private $ares;

    public function __construct(EntityManagerInterface $entityManager, CompanyRepository $companyRepository, Ares $ares)
    {
        $this->entityManager = $entityManager;
        $this->companyRepository = $companyRepository;
        $this->ares = $ares;
    }

    public function getCompanyInfo(string $ico): ?Company
    {
        $company = $this->companyRepository->findOneBy(['ico' => $ico]);

        if (!$company)
        {
            $company = $this->fetchCompanyFromAresByIco($ico);

            if ($company)
            {
                $this->entityManager->persist($company);
                $this->entityManager->flush();
            }
        }
        return $company;
    }

    private function fetchCompanyFromAresByIco(string $ico): ?Company
    {
        try
        {
            $aresData = $this->ares->findByIdentificationNumber($ico);
        }
        catch (IdentificationNumberNotFoundException $e)
        {
            throw new Exception('Company not found in ARES');
        }

        $company = new Company();
        $company->setIco($ico);
        $company->setName($aresData->getCompanyName());
        $company->setAddressStreet($aresData->getStreet());
        $company->setAddressHousenumber($aresData->getStreetNumber());
        $company->setAddressCity($aresData->getTown());
        $company->setAddressPostalCode($aresData->getZip());
        $company->setAddressCounty($aresData->getDistrict());

        return $company;
    }

}