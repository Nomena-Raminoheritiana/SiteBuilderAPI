<?php

namespace App\Command;

use Spatie\DbDumper\Databases\MySql;
use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[AsCommand(
    name: 'app:db:full-dump',
    description: 'Command to dump the database (.sql)',
)]
class DbFullDumpCommand extends Command
{
    public function __construct(
        private Connection $connection,
        private ParameterBagInterface $params
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $params = $this->connection->getParams();
        $user = $params['user'] ?? null;
        $password = $params['password'] ?? null;
        $host = $params['host'] ?? '127.0.0.1';
        $port = $params['port'] ?? 3306;
        $dbname = $params['dbname'] ?? null;
        $file = 'var/dump_' . date('Y-m-d_H-i-s') . '.sql';
        
        if (!$user || !$dbname) {
            $output->writeln('❌ Impossible de récupérer les paramètres de connexion.');
            return Command::FAILURE;
        }

        $mysqldumpPath = $this->params->get('mysql.dump.path');
        $projectDir = $this->params->get('kernel.project_dir');
        $file = $projectDir.'\\var\\dump_sql\\db_dump_' . date('Y-m-d_H-i-s') . '.sql';
        
        try { 
            MySql::create()
                ->setDumpBinaryPath($mysqldumpPath) 
                ->setDbName($dbname)
                ->setUserName($user)
                ->setPassword($password)
                ->setHost($host)
                ->setPort($port)
                ->addExtraOption('--all-databases')
                ->dumpToFile($file);
            $output->writeln("✅ Dump enregistré dans : $file");
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln("❌ Erreur lors de l’exécution de mysqldump.");
            return Command::FAILURE;
          
        }
        return Command::FAILURE;
        
      
    }
}
