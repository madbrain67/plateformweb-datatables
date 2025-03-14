<?php

declare(strict_types=1);

namespace Plateformweb\Datatables;

interface AdapterInterface 
{
    public function query(array $datas): void;
}