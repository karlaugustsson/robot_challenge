<?php namespace App\RobotChallenge;

use App\RobotChallenge\GridSize ;
use App\RobotChallenge\Exceptions\GridException ;
use App\RobotChallenge\Exceptions\GridPositionOutOfBoundsException ;
use App\RobotChallenge\Exceptions\GridPathIsBlockedException  ;
use App\RobotChallenge\Exceptions\PassOveritemException ;
use App\RobotChallenge\Grid;
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
    /**
     * the constructor takes two arguments one int will represent the width of grid,
     * the other int will represent the height of the grid
     * the function also tries to convert negative numbers to posetive numbers.
     * the constructor will try to make strings into ints
     * @param int
     * @param int
     */
    public function __construct($width, $height)
    {

        $this->height = abs((int)$height);

        $this->width = abs((int)$width);


    }
    /**
     * this function will give you an numeric array of the height and with of the grid,
     * both array-values represtented by ints
     * @return array
     */
    public function getGridDimensions()
    {
        return array($this->getGridWidth(),$this->getGridHeight());
    }
    /**
     * will return an int that will represent the height of the grid
     * @return int
     */
    private function getGridHeight()
    {
        return $this->height;
    }
    /** will return an int that will represent the width of the grid
     * @return int
     */
    private function getGridWidth()
    {
        return $this->width;
    }
    /**
     * this function retuns an array of the items that are on this grid.
     *
     * @return array
     */
    public function getItemsOnGrid()
    {
        return $this->itemsOnTheGrid;
    }

    /**
     * will check if the position provided in the arguments is found.
     * it will check the with and the height of the grid to see if the arguments are less or
     * equal to the height and weight of the grid
     * if true it will return true
     * if not it will throw an error
     *
     * @param  int
     * @param  int
     * @return boolean
     * @throws GridPositionOutOfBoundsException
     */
    public function gridPositionExists($x, $y)
    {
        if ($x <= $this->getGridWidth() && $y <= $this->getGridHeight() && $x >= 0 && $y >= 0
            || $this->positionHasWarpPoint(array($x,$y)) == true) {
            return true;
        }
        throw new GridPositionOutOfBoundsException("position:({$x},{$y}) requested does not exist on this grid");


    }
    /**
     * will do as GridPositonExist but it will allow the arguments too be one number bigger
     *  or one number smaller than the grid, will return true if conditions are met
     *  will  throw GridPositionOutOfBoundsException if the position cant be found
     * @param  int
     * @param  int
     * @return bool
     * @throws GridPositionOutOfBoundsException
     */
    public function gridPositionExistsIncludeWalls($x, $y)
    {

        if ($x <= $this->getGridWidth() + 1 && $y <= $this->getGridHeight() + 1 && $x >= -1 && $y >= -1) {
            return true;
        }
        throw new GridPositionOutOfBoundsException("position:({$x},{$y}) requested does not exist on this grid");

    }
    /**
     * this function checks if the position found in the position-array can be supplied by the grid
     * it also takes an additional parameter for the item wich will be placed on the position
     * it checks if the item can be placed inside the walls of the grid,
     *  if it can it performs a slightly diffrent check where it also checks the wall of the grid
     * @param  array
     * @param  GridItemInterface|null
     * @return boolean
     */
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
    /**
     *  check if the position array has a warppoint on it , returns true or false
     * @param  array
     * @return bool
     */
    public function positionHasWarpPoint($position)
    {

        foreach ($this->itemsOnTheGrid as $key => $item) {
            if ($item->getGridPosition() === $position && $item instanceof WarpPoint) {
                return true;
            }
        }
        return false;
    }
    /**
     * loops through all items on the grid in search of  a warpPoint-item
     * if found and it does match the position provided in the arguments,
     * it will return that warpitems end destination else retusn false
     * the method will also check if found end-destionation is blocked by antoher item
     * if so it throws an error
     * @param  array
     * @return boolean
     */
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
    /**
     *  will place the GridItem in the grids items-on-the-grid-array if position is valid
     *  it will check if item can be placed inside wall if that interface is provided,
     *  otherwise it will run the basic canPlaceItemOnPosition
     *  if is can be placed on the position requested it will push the item to the grid-items-array
     *  it will return a bool if pushing to the array was ok or not
     *
     *  the function
     * @param  GriditemInterface
     * @param  array
     * @return booleaan
     */
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
    /**
     * loops through all items on the grid in search for an item that is on the same
     *  position as provided in the arguments and if that item is blocking its position
     *  the function will return true if ietm is blocking ,
     *   or it will return false if nothing blocking the positoon was found
     *
     * @param  array
     * @return boolean
     */
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
