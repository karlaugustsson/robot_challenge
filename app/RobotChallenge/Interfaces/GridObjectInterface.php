<?php namespace RobotChallenge\Interfaces ;

interface GridObjectInterface
{


    public function setGrid($grid);

    public function getGrid();

    public function setInitialGridPosition($position);

    public function getTypeOfGridObject();

    public function getGridPosition();

    public function IsBlockable();
}
