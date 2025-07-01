<?php
namespace App\Services;

use App\Command\DbFullDumpCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class DumpSqlService {
    public function __construct( 
        private DbFullDumpCommand $fullDumpSqlCommand
    ) {}
    public function dumpSql():int {
        $input = new ArrayInput([]);
        $output = new BufferedOutput();
        $resultCode = $this->fullDumpSqlCommand->run($input, $output);
        
        return (int) $resultCode;
    }
}