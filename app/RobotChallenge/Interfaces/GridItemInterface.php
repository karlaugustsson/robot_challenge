<?php namespace App\RobotChallenge\Interfaces ;

use App\RobotChallenge\Grid;

interface GridItemInterface
{


    public function setGrid(Grid $grid);

    public function getGrid();

    public function setInitialGridPosition($position);

    public function getTypeOfItem();

    public function getGridPosition();

    public function isBlockable();
}
