<?php namespace App\RobotChallenge\Interfaces ;

interface GridItemInterface
{


    public function setGrid($grid);

    public function getGrid();

    public function setInitialGridPosition($position);

    public function getTypeOfItem();

    public function getGridPosition();

    public function isBlockable();
}
