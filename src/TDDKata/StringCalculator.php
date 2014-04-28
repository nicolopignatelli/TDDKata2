<?php

namespace TDDKata;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use TDDKata\Exception\LoggerException;

class StringCalculator implements LoggerAwareInterface
{
    private $extractor;
    private $logger;
    private $webservice;

    public function __construct()
    {
        $this->extractor = new NumberExtractor();
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function setWebservice(WebserviceInterface $webservice)
    {
        $this->webservice = $webservice;
    }

    public function add($string)
    {
        $numbers = $this->extractor->extract($string);

        $this->throwExceptionIfAnyNegativeNumber($numbers);

        $sum = $this->sum($numbers);

        $this->logSum($sum);

        return $sum;
    }

    private function logSum($sum)
    {
        try {
            if ($this->logger != null) {
                $this->logger->info('Sum is ' . $sum);
            }
        } catch(LoggerException $exception) {
            if($this->webservice != null) {
                $this->webservice->notify("Logger exception: ".$exception->getMessage());
            }
        }
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
