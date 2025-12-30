<?php

declare(strict_types=1);

namespace J4F\PayDay;

require_once __DIR__ . '/../../vendor/autoload.php';

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableCellStyle;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Output\ConsoleOutput;

$payrol = Command::createPayrol(3);
$currency = Command::currency('EUR');
$results = Command::breakdownResults($payrol, $currency);
$output = new ConsoleOutput;

// payrol
echo '**PAYROL SAMPLE**' . PHP_EOL;
new Table($output)
    ->setHeaders(Command::payrolHeaders($currency))
    ->setRows(Command::payrolRows($payrol))
    ->setStyle('box')
    ->render();

// breakdown summary
echo '**BREAKDOWN SUMMARY**' . PHP_EOL;
new Table($output)
    ->setHeaders(Command::breakdownHeaders($currency))
    ->setRows(Command::breakdownRows($results))
    ->setStyle('box')
    ->render();

class Command
{
    public static function currency(string $code): Currency
    {
        $currencies = require_once 'currencies.php';

        return Currency::fromArray($currencies[$code]);
    }

    /** @return array<int, array{employee: string, amount: float}> */
    public static function createPayrol(int $employeesNo): array
    {
        $payrol = [];
        $pos = 0;

        while ($employeesNo) {
            $pos++;
            $payrol[] = [
                'employee' => "Employee $pos",
                'amount' => random_int(100000, 200000) / 100,
            ];
            $employeesNo--;
        }

        return $payrol;
    }

    /**
     * @param  array<int, array{employee: string, amount: float}>  $payrol
     * @return array<int, array{count: int, denomination: float, amount: float, type: string}> $results
     * */
    public static function breakdownResults(array $payrol, Currency $currency): array
    {
        return new Cash($currency)
            ->breakdownCollection($payrol);
    }

    /** @return array<int, TableCell> */
    public static function breakdownHeaders(Currency $currency): array
    {
        return [
            new TableCell('Count', [
                'style' => new TableCellStyle(['align' => 'right']),
            ]),
            new TableCell('Denomination', [
                'style' => new TableCellStyle(['align' => 'right']),
            ]),
            new TableCell('Type', [
                'style' => new TableCellStyle(['align' => 'left']),
            ]),
            new TableCell("Amount ($currency->code)", [
                'style' => new TableCellStyle(['align' => 'right']),
            ]),
        ];
    }

    /**
     * @param  array<int, array{count: int, denomination: float, amount: float, type: string}>  $results
     * @return array<int, array<int, string|TableCell>|TableSeparator>
     */
    public static function breakdownRows(array $results): array
    {
        $total = 0;
        $rows = [];
        foreach ($results as $result) {
            $count = $result['count'];
            $denomination = $result['denomination'];
            $amount = $result['amount'];
            $type = $result['type'];
            $total += $amount;
            $rows[] = [
                new TableCell(number_format($count, 0), [
                    'style' => new TableCellStyle(['align' => 'right']),
                ]),
                new TableCell(number_format($denomination, 2), [
                    'style' => new TableCellStyle(['align' => 'right']),
                ]),
                new TableCell($type),
                new TableCell(number_format($amount, 2), [
                    'style' => new TableCellStyle(['align' => 'right']),
                ]),
            ];
        }

        $rows[] = new TableSeparator;
        $rows[] = [
            '',
            '',
            '',
            new TableCell( number_format($total, 2), [
                'style' => new TableCellStyle([
                    'align' => 'right',
                    'cellFormat' => '<fg=yellow;options=bold>%s</>',
                ]),
            ]),
        ];

        return $rows;
    }

    /** @return array<int, TableCell> */
    public static function payrolHeaders(Currency $currency): array
    {
        return [
            new TableCell('Employee', [
                'style' => new TableCellStyle(['align' => 'left']),
            ]),
            new TableCell("Amount ($currency->code)", [
                'style' => new TableCellStyle(['align' => 'right']),
            ]),
        ];
    }

    /**
     * @param  array<int, array{employee: string, amount: float}>  $payrol
     * @return array<int, array<int, string|TableCell>|TableSeparator>
     */
    public static function payrolRows(array $payrol): array
    {
        $total = 0;
        $rows = [];
        foreach ($payrol as $result) {
            $employee = $result['employee'];
            $amount = $result['amount'];
            $total += $amount;

            $rows[] = [
                new TableCell($employee),
                new TableCell(number_format($amount, 2), [
                    'style' => new TableCellStyle(['align' => 'right']),
                ]),
            ];
        }

        $rows[] = new TableSeparator;
        $rows[] = [
            '',
            new TableCell( number_format($total, 2), [
                'style' => new TableCellStyle([
                    'align' => 'right',
                    'cellFormat' => '<fg=yellow;options=bold>%s</>',
                ]),
            ]),
        ];

        return $rows;
    }
}
