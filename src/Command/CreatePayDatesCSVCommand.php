<?php
namespace App\Command;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreatePayDatesCSVCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:create-pay-dates-csv';

    protected function configure(): void
    {
        // ...
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $month = date('n');
        $currentYear = date('Y');

        //Initialize CSV header
        $lines[] = ['Period', 'Basic Payment', 'Bonus Payment'];

        for ($i = 1; $i <= 12; $i++) {
            if ($month > 12) {
                //Reset to january and increase year
                $month = 1;
                $currentYear++;
            }

            //Get number of days in month
            $lastDay = cal_days_in_month(CAL_GREGORIAN, $month, $currentYear);

            //Work out basic salary date.
            $salaryPaymentDate = $currentYear.'-'.$month.'-'.$lastDay;
            $dayOfWeekSalary = date('D', strtotime($salaryPaymentDate));
            
            if ($dayOfWeekSalary == 'Sat' || $dayOfWeekSalary == 'Sun') { //If weekend day, get last weekday.
                $salaryPaymentDate = date('Y-m-d', strtotime("$currentYear-$month-$lastDay -1 Weekday"));
            }

            //Work out bonus payment date.
            $bonusPaymentDate = $currentYear.'-'.$month.'-10';
            $dayOfWeekBonusPayment = date('D', strtotime($bonusPaymentDate));

            if ($dayOfWeekBonusPayment == 'Sat' || $dayOfWeekBonusPayment == 'Sun') {
                $bonusPaymentDate = date('Y-m-d', strtotime("$currentYear-$month-10 +1 Weekday")); //If weekend day, get next weekday.
            }

            //Add new line to CSV data
            $lines[] = [date('M/y', strtotime($currentYear . '-'. $month . '-1')), $salaryPaymentDate, $bonusPaymentDate];

            $month++;
        }

        $io->write('Saving CSV...' . "\n\n");

        try {
            $fp = fopen("dates.csv", "w");
        } catch (\Exception $e) {
            $io->write('Failed to open CSV file, it may be in use!' . "\n");
            die();
        }
    
        //Put each CSV line into the file
        foreach ($lines as $line) {

            $outputLine = '';
            foreach ($line as $val) {
                $outputLine .= $val . ' ';
            }
            $outputLine .= "\n";

            $io->write($outputLine);

            fputcsv(
                $fp, // The file pointer
                $line, // The fields
                ',' // The delimiter
            );
        }
    
        fclose($fp); //Close CSV file

        // Write to the standard output
        $io->write("\n" . 'dates.csv was saved');

        return Command::SUCCESS;
    }
}
