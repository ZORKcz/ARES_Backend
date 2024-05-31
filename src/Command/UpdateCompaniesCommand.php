<?php

namespace App\Command;

use App\Repository\CompanyRepository;
use App\Service\CompanyService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateCompaniesCommand extends Command
{
    protected static $defaultName = 'app:update-companies';

    public function __construct(
        private CompanyService $companyService,
        private CompanyRepository $companyRepository,
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Update older company records from ARES')
            ->addOption('limit', null, InputOption::VALUE_OPTIONAL,
                'Limit the number of records to update', 100);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $limit = $input->getOption('limit');
        $companies = $this->companyRepository->findOldRecords($limit);

        foreach ($companies as $company) {
            try {
                $this->companyService->getCompanyInfoByIco($company->getIco());

                $output->writeln('Updated: ' . $company->getName());
            } catch (\Exception $e) {
                $output->writeln('Failed to update: ' . $company->getIco() . ' - ' . $e->getMessage());
            }
        }

        $this->entityManager->flush();
        $output->writeln('Update completed');

        return Command::SUCCESS;
    }
}
