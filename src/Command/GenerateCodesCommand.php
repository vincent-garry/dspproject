<?php

namespace App\Command;

use App\Entity\Code;
use Doctrine\DBAL\Connection;
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
    private $connection;

    public function __construct(Connection $connection)
    {
        parent::__construct();
        $this->connection = $connection;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $totalCodes = 500000;
        $batchSize = 1000;
        $prizes = [
            'Infuseur à thé' => 0.60,
            'Boite de 100g de thé détox ou infusion' => 0.20,
            'Boite de 100g de thé signature' => 0.10,
            'Coffret découverte 39€' => 0.06,
            'Coffret découverte 69€' => 0.04,
        ];

        $output->writeln('Génération des codes en cours...');

        $this->connection->executeStatement('CREATE TEMPORARY TABLE temp_codes (code VARCHAR(255), prize VARCHAR(255))');

        for ($i = 0; $i < $totalCodes; $i += $batchSize) {
            $batch = [];
            for ($j = 0; $j < $batchSize && ($i + $j) < $totalCodes; $j++) {
                $batch[] = [
                    $this->generateUniqueCode(),
                    $this->getRandomPrize($prizes)
                ];
            }

            $this->connection->executeStatement(
                'INSERT INTO temp_codes (code, prize) VALUES ' .
                implode(', ', array_fill(0, count($batch), '(?, ?)')),
                array_merge(...$batch)
            );

            if ($i % 10000 === 0) {
                $output->writeln(($i + $batchSize) . " codes générés...");
            }
        }

        $output->writeln("Transfert des données vers la table principale...");
        $this->connection->executeStatement('
            INSERT INTO code (code, prize, is_used)
            SELECT code, prize, 0 FROM temp_codes
        ');

        $this->connection->executeStatement('DROP TEMPORARY TABLE temp_codes');

        $output->writeln('Génération des codes terminée.');

        return Command::SUCCESS;
    }

    private function generateUniqueCode(): string
    {
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

        return array_key_first($prizes);
    }
}