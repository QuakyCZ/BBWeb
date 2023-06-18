<?php

namespace App\Component;

use Ublaboo\DataGrid\DataGrid;

class CustomDataGrid extends DataGrid
{
    public function addColumnImage(string $key, string $name, ?string $path = null, ?string $column = null): ColumnImage {
        $column = $column ?? $key;
        $column = new ColumnImage($this, $key, $name, $column, $path);
        $this->addColumn($key, $column);
        return $column;
    }
}