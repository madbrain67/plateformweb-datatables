<?php

declare(strict_types=1);

namespace Plateformweb\Datatables;

class Debug
{
    protected bool $debug = false;

    /**
     * Diagnostics with DataTables Debugging Tool
     * 
     * Enable or disable the debug option in DataTables to obtain more information about the problem
     */
    public function setDebug(bool $debug = true): self
    {
        $this->debug = $debug; 

        return $this;
    }
}