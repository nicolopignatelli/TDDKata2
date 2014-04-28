<?php

namespace TDDKata;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

class StringCalculator implements LoggerAwareInterface
{
    private $extractor;
    private $logger;

    public function __construct()
    {
        $this->extractor = new NumberExtractor();
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function add($string)
    {
        $numbers = $this->extractor->extract($string);

        $this->throwExceptionIfAnyNegativeNumber($numbers);

        $sum = $this->sum($numbers);

        if($this->logger != null)
        {
            $this->logger->info('Sum is '.$sum);
        }

        return $sum;
    }

    private function throwExceptionIfAnyNegativeNumber($numbers)
    {
        $negativeNumbers = [];

        foreach ($numbers as $number) {
            if ($number < 0) {
                $negativeNumbers[] = $number;
            }
        }

        if (count($negativeNumbers) > 0) {
            throw new Exception\NegativeNumbersNotAllowedException($negativeNumbers);
        }
    }

    private function sum($numbers)
    {
        $sum = 0;

        foreach ($numbers as $number) {
            if ($this->numberIsLessThanOrEqualToOneThousand($number)) {
                $sum += $number;
            }
        }

        return $sum;
    }

    private function numberIsLessThanOrEqualToOneThousand($number)
    {
        return $number <= 1000;
    }
}
