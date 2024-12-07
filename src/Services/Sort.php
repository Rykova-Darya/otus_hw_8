<?php

namespace App\Services;

class Sort
{
    private int $N;
    private array $arr;
    private int $cmp = 0;
    private int $asg = 0;
    private array $T = [];
    public function __construct($N = 10, $arr = [])
    {
        $this->N = $N;
        $this->arr = $arr;
    }


    public function quickSort()
    {
        $this->qSort(0, $this->N - 1);

    }

    public function getArrSorted()
    {
        return $this->arr;

    }

    public function heapSort(): void
    {
        for ($h = (int) ($this->lengthArr / 2) - 1; $h >= 0; $h--) {
            $this->heapify($h, $this->lengthArr);
        }
        for ($j = $this->lengthArr - 1; $j > 0; $j--) {
            $this->Swap(0, $j);
            $this->heapify(0, $j);
        }

    }
    public function mergeSort()
    {
        $this->T = array_fill(0, $this->N, null);
        $this->mSort(0, $this->N - 1);

    }

    private function mSort(int $L, int $R)
    {
        if ($L >= $R) return;
        $M = (int) (($L + $R) / 2);
        $this->mSort($L, $M);
        $this->mSort($M +1, $R);
        $this->merge($L, $M, $R);

    }

    private function merge(int $L, int $M, int $R)
    {
        $a = $L;
        $b = $M + 1;
        $m = $L;

        while ($a <= $M && $b <= $R) {
            if ($this->More($this->arr[$a], $this->arr[$b])) {
                $this->T[$m++] = $this->arr[$b++];
            } else {
                $this->T[$m++] = $this->arr[$a++];
            }
        }
        while ($a <= $M) {
            $this->T[$m++] = $this->arr[$a++];
        }
        while ($b <= $R) {
            $this->T[$m++] = $this->arr[$b++];
        }
        for ($m = $L; $m <= $R; $m++) {
            $this->arr[$m] = $this->T[$m];
        }
    }

    private function qSort(int $L, int $R)
    {
        if ($L >= $R) return;
        $M = $this->split($L, $R);
        $this->qSort($L, $M - 1);
        $this->qSort($M + 1, $R);
    }

    private function split(int $L, int $R)
    {
        $M = $L - 1;
        $P = $this->arr[$R];
        for ($j = $L; $j <= $R; $j++) {
            if (!$this->More($P, $this->arr[$j]))
            {
                $this->Swap(++$M, $j);
            }
        }
        return $M;
    }

    private function More(int $a, int $b): bool
    {
        $this->cmp++;
        return $a > $b;
    }

    private function Swap(int $x, int $y): void
    {
        $t = $this->arr[$x];
        $this->arr[$x] = $this->arr[$y];
        $this->arr[$y] = $t;
        $this->asg += 3;
    }

    public function setRandom(int $lengthArr)
    {
        $this->lengthArr = $lengthArr;

        mt_srand(1234567);

        for ($j = 0; $j < $lengthArr; $j++) {
            $this->arr[$j] = mt_rand(0, $lengthArr * 100 -1);
        }

    }

    public function setSorted(int $lengthArr)
    {
        $this->lengthArr = $lengthArr;
        for ($j = 0; $j < $lengthArr; $j++) {
            $this->arr[$j] = $j+1;
        }
    }

    public function setReversed(int $lengthArr)
    {
        $this->lengthArr = $lengthArr;
        for ($j = 0; $j < $lengthArr; $j++) {
            $this->arr[$j] = $j-1;
        }
    }

    private function heapify(int $root, int $size)
    {
        $P = $root;
        $L = 2 * $P + 1;
        $R = $L + 1;
        if ($L < $size && $this->More($this->arr[$L], $this->arr[$P])) $P = $L;
        if ($R < $size && $this->More($this->arr[$R], $this->arr[$P])) $P = $R;
        if ($P == $root) return;
        $this->Swap($root, $P);
        $this->heapify($P, $size);

    }
    public function toString()
    {
        return "Длинна массива: " . $this->lengthArr . "\tcmp: " . $this->cmp . "\tasg " . $this->asg;

    }
}