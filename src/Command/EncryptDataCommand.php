<?php

namespace App\Command;

use App\Service\Interfaces\CrypticInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:encrypt-data',
    description: 'Encrypts non-encrypted data in production.',
)]
class EncryptDataCommand extends Command
{
    public function __construct(protected CrypticInterface $cryptService, protected EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('table name', InputArgument::REQUIRED, 'Entity to encrypt.');
        $this->addOption('--amount', '-a', InputOption::VALUE_REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $table = $input->getArgument('table name');
        $amounts = $input->getOption('amount');
        if (json_last_error() !== JSON_ERROR_NONE) {
            $output->writeln('<error>Invalid JSON data</error>');
            return Command::FAILURE;
        }
        if (!$amounts) {
            $output->writeln('<error>Data not set</error>');
        }
        $amounts = json_decode($amounts, true);
        $this->em->beginTransaction();
        try {
            foreach ($amounts as $amount) {
                $encrypted = $this->cryptService->encrypt($amount['amount']);
                 $this->em->createQuery("UPDATE $table t SET t.amount = :encrypted WHERE t.id = :id")
                    ->setParameter('encrypted', $encrypted)
                    ->setParameter('id', $amount['id'])
                    ->getResult();
                $output->writeln("write id = {$amount['id']} with value $encrypted") . '\n';
	    }
	    $this->em->commit();
        } catch (Exception $e) {
            $this->em->rollback();
            $output->writeln('<error>' . $e->getMessage() . '</error>');
        }

        return Command::SUCCESS;
    }
}
