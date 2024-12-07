<?php

namespace App\Command;

use App\Services\ExternalSort;
use App\Services\FilesService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'external:sort',
    description: 'Алгоритм внешней сортировки',
)]
class ExternalSortCommand extends Command
{
    public function __construct(private readonly FilesService $filesService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('N', InputArgument::REQUIRED, 'строки')
            ->addArgument('alg', InputArgument::REQUIRED, 'Алгоритм')
            ->addArgument('T', InputArgument::OPTIONAL, 'числа')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $N = $input->getArgument('N');
        $T = $input->getArgument('T');
        $alg = $input->getArgument('alg');



        if ($alg == 'ES1') {
            $file = $this->filesService->generateFile($N, $T);
            $start = (int) (microtime(true) * 1000);
            $filesName =  $this->filesService->splitFile($file, $T);
            $result = $this->filesService->mergeSortedFiles($filesName);
            unlink($file);
        } elseif ($alg == 'ES2') {
            $file = $this->filesService->generateFile($N, $T);
            $start = (int) (microtime(true) * 1000);
            $filesName =  $this->filesService->splitFile($file, 2);
            $result = $this->filesService->mergeSortedFiles($filesName);
        } elseif ($alg == 'ES3') {
            $file = $this->filesService->generateFile($N, $T);
            $start = (int) (microtime(true) * 1000);
            $filesName =  $this->filesService->blocksSorted($file);
            $result = $this->filesService->mergeSortedFiles($filesName);
            unlink($file);
        }
        $ms = (int) (microtime(true) * 1000) - $start;

       $io->success('Файл отсортирован. Результат в: ' . $ms);
        return Command::SUCCESS;
    }
}
