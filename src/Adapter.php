<?php

declare(strict_types=1);

namespace Plateformweb\Datatables;

class Adapter
{
    protected array $columns = [];
    protected array $columnsIndexed = [];
    protected array $datas = [];
    protected int $draw = 1;
    protected int $recordsTotal = 0;
    protected int $recordsFiltered = 0;
    protected string $search = '';
    protected int $start = 0;
    protected int $length = 10;
    protected string $where = '';
    protected string $groupBy = '';
    protected string $having = '';
    protected string $query = '';
    protected array $params = [];
    protected array $types = [];
    protected string $position = 'normal';
    protected bool $active_debug = false;
    protected array $orderColumnName = [];
    protected array $orderColumnDirection = [];
    protected array $columnsCallable = [];

    public function __construct()
    {
        $postData = $_POST ?? [];
        
        if (!empty($postData) && \array_key_exists('columns', $postData)) {
            // Column header without join alias
            $this->columns = array_column($postData['columns'], 'data');

            // Searchable columns for LIKE
            $this->columnsIndexed = array_map(
                fn($column) => $column['name'],
                array_filter($postData['columns'], fn($column) => $column['searchable'] === 'true')
            );

            // Search term
            $this->search = $postData['search']['value'] ?? '';

            // LIMIT 0, 10 ....
            $this->start = (int) ($postData['start'] ?? 1);
            $length = (int) ($postData['length'] ?? 10);
            $this->length = $length === -1 ? PHP_INT_MAX : $length;

            // ORDER BY : (default: the first column visible in asc) / Columns tagged with ‘order’ => ‘asc/desc’. 
            if (array_key_exists('order', $postData)) {
                $this->orderColumnName = array_column($postData['order'], 'name');
                $this->orderColumnDirection = array_column($postData['order'], 'dir');
            } else {
                $this->orderColumnName = [$postData['extra']['firstColumnNameVisible']];
                $this->orderColumnDirection = ['asc'];
            }

            $this->draw = intval($postData['draw'] ?? 1);
        }
    }

    protected function setDebug(bool $active_debug = true): self
    {
        $this->active_debug = $active_debug;

        return $this;
    }

    protected function setDebugPosition(string $position = 'normal'): self
    {
        $this->position = $position;

        return $this;
    }

    public function editColumn(string $column, callable $callable): self
    {
        $this->columnsCallable[$column] = $callable;

        return $this;
    }

    private function processDatas($data): array
    {
        $datas = [];

        foreach ($this->columns as $column) {
            if ($column === 'column_0') {
                $value = null;
            } else {
                if (\array_key_exists($column, $this->columnsCallable) && \is_callable($this->columnsCallable[$column])) {
                    $value = $this->columnsCallable[$column]($data);
                } else {
                    $value = $data[$column];
                }
            }
            $datas[$column] = $value;
        }

        return $datas;
    }

    protected function response(): string
    {
        $response = [
            'draw' => $this->draw,
            'recordsTotal' => $this->recordsTotal,
            'recordsFiltered' => $this->recordsFiltered,
            'data' => array_map([$this, 'processDatas'], $this->datas),
        ];
    
        $json = json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    
        if ($json === false) {
            return json_encode([
                'error' => 'Error during JSON encoding : ' . json_last_error_msg()
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
    
        return $json;
    }
}