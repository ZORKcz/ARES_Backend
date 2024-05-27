<?php

namespace App\Service;

use App\Entity\Company;
use App\Repository\CompanyRepository;
use Doctrine\ORM\EntityManagerInterface;
use h4kuna\Ares;
use h4kuna\Ares\Exceptions\IdentificationNumberNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CompanyService
{
    private \h4kuna\Ares\Ares $ares;
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CompanyRepository      $companyRepository
    ) {
        $this->ares = (new Ares\AresFactory())->create();
    }

    public function getCompanyInfoByIco(string $ico): ?Company
    {
//        dump('tu');
//        dd($this->companyRepository->findOneBy([
//        'ico' => $ico,
//    ]));

        $company = $this->companyRepository->test();
        dump('tu');
//        dd($company);

        if (!$company)
        {
            dump('tu');
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
        try {
            $aresData = $this->ares->loadDataBox($ico);
        } catch (IdentificationNumberNotFoundException $e) {
            throw new NotFoundHttpException('Company not found in ARES');
        }

        if ($aresData == null) {
            return null;
        }

        $aresData = $aresData[0];
        dd($aresData);
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
