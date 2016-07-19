<?php namespace RobotChallenge;

use App\RobotChallenge\Exceptions\GridException ;
use App\RobotChallenge\Exceptions\GridPositionOutOfBoundsException ;
use App\RobotChallenge\Exceptions\GridPathIsBlockedException  ;
use App\RobotChallenge\Exceptions\passOverObjectException ;

use App\RobotChallenge\Interfaces\GridObjectInterface ;
use App\RobotChallenge\Interfaces\CanPlaceObjectsInWallInterface ;
use App\RobotChallenge\Interfaces\GridObjectsCanBePickedUpInterface ;
use App\RobotChallenge\Interfaces\GrabbableObjectInterface ;
use App\RobotChallenge\Interfaces\GridWarpPointInterface ;
use App\RobotChallenge\Interfaces\WallObjectInterface ;

class Grid implements GridWarpPointInterface, CanPlaceObjectsInWallInterface, GridObjectsCanBePickedUpInterface
{
    private $height;
    private $width;
    private $objects_on_the_grid = array();

    public function __construct($width, $height)
    {


        $this->height = (int)$height;

        $this->width = (int)$width;

        if ($this->height < 0) {
            $this->height = abs($height);
        }

        if ($this->width < 0) {
            $this->width = abs($width);
        }
    }

    public function getGridDimensions()
    {
        return array($this->width,$this->height);
    }

    private function getGridHeight()
    {
        return $this->height;
    }

    private function getGridWidth()
    {
        return $this->width;
    }
    public function getObjectsOnGrid()
    {
        return $this->objects_on_the_grid;
    }

    public function gridPositionExists($x, $y)
    {
        if ($x <= $this->getGridWidth() && $y <= $this->getGridHeight() && $x >= 0 && $y >= 0
            || $this->positionHasWarpPoint(array($x,$y)) == true) {
            return true;
        }


        return false;

    }

    public function gridPositionExistsIncludeWalls($x, $y)
    {

        if ($x <= $this->getGridWidth() + 1 && $y <= $this->getGridHeight() + 1 && $x >= -1 && $y >= -1) {
            return true;
        }

        return false;

    }

    public function canPlaceObjectOnPosition($position, GridObjectInterface $object = null)
    {

        if ($this->positionArrayIsValid($position)) {
            if ($object && $object instanceof  WallObjectInterface) {
                if (!$this->gridPositionExistsIncludeWalls($position[0], $position[1])) {
                    throw new GridPositionOutOfBoundsException("the position requested does not exist on this grid");
                    return false;
                }
            } else {
                if (!$this->gridPositionExists($position[0], $position[1])) {
                    return false;
                }
            }




            if ($this->gridPositionIsBlocked($position)) {
                throw new GridPathIsBlockedException("the position (" . $position[0].",". $position[1] .
                    ") on the grid has already been taken by an blockable object ");
                return false;
            }

            return true;
        }

        return false;
    }

    public function positionHasWarpPoint($position)
    {

        foreach ($this->objects_on_the_grid as $key => $grid_obj) {
            if ($grid_obj->getGridPosition() === $position && $grid_obj instanceof WarpPoint) {
                return true;
            }
        }
        return false;
    }

    public function getWarpPointPosition($position)
    {
        $found_output_positon = null;

        foreach ($this->objects_on_the_grid as $grid_obj) {
            if ($grid_obj->getGridPosition() === $position && $grid_obj instanceof WarpPoint) {
                $found_output_positon = $grid_obj->getWarpEndpointPosition();
            }
        }

        if ($found_output_positon) {
            if (!$this->gridPositionIsBlocked($found_output_positon)) {
                return $found_output_positon;
            }
            throw new GridPathIsBlockedException("warpendpoint is blocked so you cant warp to position "
             . $found_output_positon[0] . " " .$found_output_positon[1] . "\n\r", 1);
        }
        return false;
    }

    public function placeObjectOnGrid(GridObjectInterface $object, $position)
    {

        if ($object instanceof WallObjectInterface) {
            if ($this->canPlaceObjectOnPosition($position, $object)) {
                return array_push($this->objects_on_the_grid, $object);
            }
        }

        if ($this->canPlaceObjectOnPosition($position)) {
            return array_push($this->objects_on_the_grid, $object);
        }

        return false;


    }

    private function positionArrayIsValid($position)
    {

        if (!is_array($position)) {
            throw new GridException("argument position is expected to be an array", 1);
            return false;
        }
        if ($position[0] === null || $position[1] === null) {
            throw new GridException("position-array should have two keys for x and y position");
            return false;
        }

        return true;
    }

    public function gridPositionIsBlocked($position)
    {


        foreach ($this->objects_on_the_grid as $key => $grid_obj) {
            if ($grid_obj->getGridPosition() === $position && $grid_obj->isBlockable()) {
                return true;
            }
        }


        return false;


    }

    public function passabaleObjectFoundOnPosition($position)
    {

        foreach ($this->objects_on_the_grid as $grid_obj) {
            if ($grid_obj instanceof GrabbableObjectInterface && $position == $grid_obj->getGridPosition()) {
                return true;
            }
        }
        return false;
    }

    public function passOverObjectFromPosition($position)
    {

        foreach ($this->objects_on_the_grid as $key => $grid_obj) {
            if ($grid_obj instanceof GrabbableObjectInterface && $position = $grid_obj->getGridPosition()) {
                unset($this->objects_on_the_grid[$key]);
                return $grid_obj;
            }
        }

        throw new passOverObjectException("there was no object found , to be transfered \n\r") ;
    }
}
