<?php

declare(strict_types=1);

namespace App\Helpers;


use ParseCsv\Csv;

class CsvHelper
{
    /**
     *  Parse CSV file
     *
     * @param string $content
     * @return Csv|null
     * @throws \Exception
     */
    public static function parseCsv(string $content)
    {
        try {
            $csv = new Csv();
            $csv->parse($content);

            return $csv->file && $csv->error == 0 ? $csv : null;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }

    /**
     * Search by field in CSV file
     *
     * @param Csv $csv
     * @param string $byField
     * @param $byValue
     * @return array
     * @throws \Exception
     */
    public static function searchByField(Csv $csv, string $byField, $byValue): array
    {
        try {
            return array_filter($csv->data, function ($item) use ($byField, $byValue) {
                return ($item[$byField] == $byValue);
            });
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }
}
