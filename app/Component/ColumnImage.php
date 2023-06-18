<?php

namespace App\Component;

use Nette\Database\Table\ActiveRow;
use Nette\Utils\Html;
use Ublaboo\DataGrid\Column\ColumnText;
use Ublaboo\DataGrid\Column\Renderer;
use Ublaboo\DataGrid\DataGrid;

class ColumnImage extends ColumnText
{
    private ?string $imagePath;

    public function __construct(
        DataGrid $grid,
        string $key,
        string $name,
        ?string $column = null,
        ?string $path = null
    )
    {
        parent::__construct($grid, $key, $column, $name);
        $this->imagePath = $path;
        $this->renderer = new Renderer(function (ActiveRow $row) use ($key, $column, $path, $name) {
            $imagePath = $path ?? $row[$column] ?? null;

            if (empty($imagePath)) {
                return "";
            }

            return Html::el('img')
                ->src($imagePath)
                ->alt("Nebylo možné načíst obrázek.")
                ->style('max-width', '250px');
        }, null);
        $this->setTemplateEscaping(false);
    }

    /**
     * @return string|null
     */
    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }
}