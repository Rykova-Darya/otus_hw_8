<?php

namespace App\Services;


use Carbon\Carbon;

class FilesService
{
    public function __construct()
    {
    }

    public function generateFile(int $N, int $T, string $fileName = '')
    {
        $today = Carbon::now()->format('d-m-Y');
        $uniqId = uniqid();
        $fileName .= $uniqId . $today . '.txt';
        $filePath = 'public/in/' . $fileName;
        $file = fopen($filePath , 'w');
        for ($i = 0; $i < $N; $i++) {
            fwrite($file, rand(1, $T) . PHP_EOL);
        }
        fclose($file);
        return $filePath;
    }

    public function splitFile($inputFile, $linesPerFile) {
        $handle = fopen($inputFile, "r");

        $fileIndex = 1;
        $lineCount = 0;
        $linesArray = [];
        $filesNameArr = [];

        while (($line = fgets($handle)) !== false) {
            $linesArray[] = $line;
            $lineCount++;

            if ($lineCount % $linesPerFile === 0) {
                $filesNameArr[] = $this->writeSortedFile($linesArray, $fileIndex, $linesPerFile);
                $fileIndex++;
                $linesArray = [];
            }
        }

        if (!empty($linesArray)) {
            $this->writeSortedFile($linesArray, $fileIndex, $linesPerFile);
        }

        fclose($handle);
        return $filesNameArr;
    }

    private function writeSortedFile($linesArray, $fileIndex, $linesPerFile) {
        $sort = new Sort($linesPerFile, $linesArray);
        $sort->quickSort();
        $linesArray = $sort->getArrSorted();
        $outputFile = sys_get_temp_dir() . $fileIndex . ".txt";
        $currentOutput = fopen($outputFile, "w");

        foreach ($linesArray as $sortedLine) {
            fwrite($currentOutput, $sortedLine);
        }

        fclose($currentOutput);
        return $outputFile;
    }

    public function mergeSortedFiles(array $inputFiles): string
    {
        $uniqId = uniqid();
        $outputFile = 'public/out/' . $uniqId . '_result.txt';
        $fileHandles = [];
        foreach ($inputFiles as $file) {
            $fileHandles[] = fopen($file, 'r');
        }

        $outputHandle = fopen($outputFile, 'w');

        $currentLines = [];
        foreach ($fileHandles as $index => $handle) {
            $line = fgets($handle);
            if ($line !== false) {
                $currentLines[$index] = (int)trim($line);
            }
        }

        while (!empty($currentLines)) {
            $maxIndex = array_keys($currentLines, max($currentLines))[0];

            $maxValue = $currentLines[$maxIndex];

            fwrite($outputHandle, $maxValue . PHP_EOL);

            $line = fgets($fileHandles[$maxIndex]);
            if ($line !== false) {
                $currentLines[$maxIndex] = (int)trim($line);
            } else {
                unset($currentLines[$maxIndex]);
            }
        }

        foreach ($fileHandles as $handle) {
            fclose($handle);
        }
        fclose($outputHandle);

        foreach ($inputFiles as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
        echo "Слияние завершено. Результат в файле: {$outputFile}\n";
        return $outputFile;
    }

    public function blocksSorted($inputFile)
    {
        $tempFile1 = sys_get_temp_dir() . '/temp1.txt';
        $tempFile2 = sys_get_temp_dir() . '/temp2.txt';

        $inputHandle = fopen($inputFile, 'r');
        $tempHandles = [fopen($tempFile1, 'w'), fopen($tempFile2, 'w')];
        $block = [];
        $tempFileIndex = 0;

        while (!feof($inputHandle)) {
            $line = fgets($inputHandle);
            if ($line !== false) {
                $block[] = (int)trim($line);
                if (count($block) == 100) {
                    $sort = new Sort(100, $block);
                    $sort->quickSort();
                    $block = $sort->getArrSorted();
                    foreach ($block as $number) {
                        fwrite($tempHandles[$tempFileIndex], $number . PHP_EOL);
                    }
                    $block = [];
                    $tempFileIndex = 1 - $tempFileIndex;
                }
            }
        }

        if (!empty($block)) {
            $sort = new Sort(count($block), $block);
            $sort->quickSort();
            $block = $sort->getArrSorted();
            foreach ($block as $number) {
                fwrite($tempHandles[$tempFileIndex], $number . PHP_EOL);
            }
        }

        fclose($inputHandle);
        fclose($tempHandles[0]);
        fclose($tempHandles[1]);

        return [$tempFile1, $tempFile2];
    }
}