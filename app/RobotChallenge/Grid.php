<?php namespace App\RobotChallenge;

use App\RobotChallenge\GridSize ;
use App\RobotChallenge\Exceptions\GridException ;
use App\RobotChallenge\Exceptions\GridPositionOutOfBoundsException ;
use App\RobotChallenge\Exceptions\GridPathIsBlockedException  ;
use App\RobotChallenge\Exceptions\PassOveritemException ;

use App\RobotChallenge\Interfaces\GridItemInterface ;
use App\RobotChallenge\Interfaces\CanPlaceItemsInWallInterface ;
use App\RobotChallenge\Interfaces\CanBePlacedInsideWallInterface ;
use App\RobotChallenge\Interfaces\ItemsCanBePickedUpInterface ;
use App\RobotChallenge\Interfaces\CanBeGrabbedInterface ;
use App\RobotChallenge\Interfaces\GridWarpPointInterface ;
use App\RobotChallenge\Interfaces\WallitemInterface ;

class Grid implements GridWarpPointInterface, CanPlaceItemsInWallInterface, ItemsCanBePickedUpInterface
{
    private $itemsOnTheGrid = array();
    private $width = null;
    private $height = null;

    public function __construct($width, $height)
    {

        $this->height = abs((int)$height);

        $this->width = abs((int)$width);


    }

    public function getGridDimensions()
    {
        return array($this->getGridWidth(),$this->getGridHeight());
    }

    private function getGridHeight()
    {
        return $this->height;
    }

    private function getGridWidth()
    {
        return $this->width;
    }
    public function getItemsOnGrid()
    {
        return $this->itemsOnTheGrid;
    }

    public function gridPositionExists($x, $y)
    {
        if ($x <= $this->getGridWidth() && $y <= $this->getGridHeight() && $x >= 0 && $y >= 0
            || $this->positionHasWarpPoint(array($x,$y)) == true) {
            return true;
        }
        throw new GridPositionOutOfBoundsException("position:({$x},{$y}) requested does not exist on this grid");


    }

    public function gridPositionExistsIncludeWalls($x, $y)
    {

        if ($x <= $this->getGridWidth() + 1 && $y <= $this->getGridHeight() + 1 && $x >= -1 && $y >= -1) {
            return true;
        }
        throw new GridPositionOutOfBoundsException("position:({$x},{$y}) requested does not exist on this grid");

    }

    public function canPlaceItemOnPosition($position, GridItemInterface $item = null)
    {

        if ($item && $item instanceof CanBePlacedInsideWallInterface) {
            $this->gridPositionExistsIncludeWalls($position[0], $position[1]);
        } else {
            $this->gridPositionExists($position[0], $position[1]);
        }




        if ($this->gridPositionIsBlocked($position)) {
            return false;
        }

            return true;
    }

    public function positionHasWarpPoint($position)
    {

        foreach ($this->itemsOnTheGrid as $key => $item) {
            if ($item->getGridPosition() === $position && $item instanceof WarpPoint) {
                return true;
            }
        }
        return false;
    }

    public function getWarpPointEndPosition($position)
    {
        $foundOutputPosition = null;

        foreach ($this->getItemsOnGrid() as $item) {
            if ($item->getGridPosition() === $position && $item instanceof WarpPoint) {
                $subject = $item;
                $foundOutputPosition = $item->getWarpEndpointPosition();
            }
        }

        if ($foundOutputPosition) {
            if (!$this->gridPositionIsBlocked($foundOutputPosition)) {
                return $foundOutputPosition;
            }
            throw new GridPathIsBlockedException("warpendpoint is blocked so you cant warp to position ("
             . implode($foundOutputPosition, ",") . ")", 1);
        }
        return false;
    }

    public function placeItemOnGrid(GriditemInterface $item, $position)
    {

        if ($item instanceof CanBePlacedInsideWallInterface) {
            if ($this->canPlaceitemOnPosition($position, $item)) {
                return array_push($this->itemsOnTheGrid, $item);
            }
        }

        if ($this->canPlaceitemOnPosition($position)) {
            return array_push($this->itemsOnTheGrid, $item);
        }

        return false;


    }

    public function gridPositionIsBlocked($position)
    {


        foreach ($this->itemsOnTheGrid as $key => $item) {
            if ($item->getGridPosition() === $position && $item->isBlockable()) {
                return true;
            }
        }


        return false;


    }

    public function isPassableItemFoundOnPosition($position)
    {

        foreach ($this->getItemsOnGrid() as $item) {
            if ($item instanceof CanBeGrabbedInterface && $position == $item->getGridPosition()) {
                return true;
            }
        }
        return false;
    }
    public function getPassableItemOnPosition($position)
    {
        foreach ($this->getItemsOnGrid() as $key => $item) {
            if ($item instanceof CanBeGrabbedInterface && $position = $item->getGridPosition()) {
                return $item;
            }
        }
    }
    public function passOverItem($item)
    {

        if ($item instanceof CanBeGrabbedInterface && in_array($item, $this->getItemsOnGrid())) {
            $index = array_search($item, $this->getItemsOnGrid());
            unset($this->itemsOnTheGrid[$index]);
            return $item;
        }

        throw new passOverItemException("there was no item found , to be transfered \n\r") ;
    }
}
