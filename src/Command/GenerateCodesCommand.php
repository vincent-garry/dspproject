<?php

// src/Command/GenerateCodesCommand.php
namespace App\Command;

use App\Entity\Code;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:generate-codes',
    description: 'Génère 500 000 codes uniques avec la répartition des gains spécifiée.',
)]
class GenerateCodesCommand extends Command
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $this->entityManager->getConnection()->getConfiguration()->setSQLLogger(null);

        $totalCodes = 500000;
        $prizes = [
            'Infuseur à thé' => 0.60,
            'Boite de 100g de thé détox ou infusion' => 0.20,
            'Boite de 100g de thé signature' => 0.10,
            'Coffret découverte 39€' => 0.06,
            'Coffret découverte 69€' => 0.04,
        ];

        $output->writeln('Génération des codes en cours...');

        for ($i = 0; $i < $totalCodes; $i++) {
            $code = new Code();
            $code->setCode($this->generateUniqueCode());
            $code->setPrize($this->getRandomPrize($prizes));
            $code->setUsed(false);
            $this->entityManager->persist($code);

            if ($i % 1000 === 0) {
                $this->entityManager->flush();
                $this->entityManager->clear();
                $output->writeln("$i codes générés...");
            }
        }

        $this->entityManager->flush();
        $output->writeln('Génération des codes terminée.');

        return Command::SUCCESS;
    }

    private function generateUniqueCode(): string
    {
        // Implémentez votre logique de génération de code unique ici
        return bin2hex(random_bytes(8));
    }

    private function getRandomPrize(array $prizes): string
    {
        $rand = mt_rand(1, 100) / 100;
        $cumulativeProbability = 0;

        foreach ($prizes as $prize => $probability) {
            $cumulativeProbability += $probability;
            if ($rand <= $cumulativeProbability) {
                return $prize;
            }
        }

        return array_key_first($prizes); // Fallback au premier prix si nécessaire
    }
}