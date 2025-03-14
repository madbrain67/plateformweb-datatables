<?php

declare(strict_types=1);

namespace Plateformweb\Datatables\Adapters;

use Plateformweb\Datatables\Adapter;
use Plateformweb\Datatables\AdapterInterface;

class ArrayAdapter extends Adapter implements AdapterInterface
{
    public function query(array $datas): void
    {
        $this->recordsTotal = count($datas);
        $this->recordsFiltered = count($datas);
        $this->datas = array_slice($datas, $this->start, $this->length);
    }
}