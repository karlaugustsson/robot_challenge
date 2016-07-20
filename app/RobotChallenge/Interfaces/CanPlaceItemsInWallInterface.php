<?php namespace App\RobotChallenge\Interfaces ;

interface CanPlaceItemsInWallInterface
{


    public function canPlaceItemOnPosition($position, GridItemInterface $item = null);

    public function gridPositionExistsIncludeWalls($x, $y);

    public function placeItemOnGrid(GridItemInterface $item, $position);
}
