<?php

namespace App\Modules\ApiModule\Model\Player;

use Nette\Database\Table\ActiveRow;

class PlayerMapper
{
    public function mapPlayer(ActiveRow $row): Player
    {
        return new Player($row['id'], $row['name'], $row['uuid'], $row['statistics_id']);
    }
}