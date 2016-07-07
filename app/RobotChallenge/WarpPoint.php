<?php namespace RobotChallenge;

use RobotChallenge\Interfaces\GridObjectInterface as GridObjectInterface ;
use RobotChallenge\Interfaces\WallObjectInterface as WallObjectInterface;
use RobotChallenge\Exceptions\NoGridObjectFoundException as NoGridObjectFoundException ;
use RobotChallenge\Exceptions\GridPositionNotSetException as GridPositionNotSetException;
use RobotChallenge\Exceptions\WarpOutputNotSetException as WarpOutputNotSetException;
use RobotChallenge\Exceptions\IntialGridStartPositionCanOnlyBeSetOnceException
as IntialGridStartPositionCanOnlyBeSetOnceException;

class WarpPoint implements GridObjectInterface, WallObjectInterface
{
    private $grid_obj = null;
    private $x_position;
    private $y_position;
    private $typeOfObject;
    private $warpOutput;

    public function __construct(Grid $grid, $warpInput, $warpOutput)
    {
        $this->typeOfObject = "warppoint" ;
        $this->setGrid($grid);
        $this->setInitialGridPosition($warpInput);
        $this->setwarpEndpointPosition($warpOutput);
    }

    public function setGrid($grid)
    {
        $this->grid_obj = $grid;
    }


    public function getGrid()
    {

        if ($this->grid_obj === null) {
            throw new NoGridObjectFoundException("cant set position becouse no grid object has been set");
            return false;
        }
        return $this->grid_obj;
    }

    public function setInitialGridPosition($position)
    {

        if ($this->x_position !== null) {
            throw new IntialGridStartPositionCanOnlyBeSetOnceException("initial startValue can onky be set once");
        }

        if ($this->getGrid()->placeObjectOnGrid($this, $position)) {
            $this->x_position = $position[0];
            $this->y_position = $position[1];
            return true;
        } else {
            return false;
        }

    }

    public function getTypeOfGridObject()
    {
        return $this->typeOfObject ;
    }

    public function getGridPosition()
    {

        if ($this->x_position === null || $this->y_position == null) {
            throw new NoGridObjectFoundException("No position on grid has been set");
            return false;
        }

        return array($this->x_position , $this->y_position);

    }

    public function isBlockable()
    {
        return false;
    }

    public function setwarpEndPointPosition($warpOutput)
    {
        if ($this->getGrid()->canPlaceObjectOnPosition($warpOutput)) {
            $this->warpOutput = $warpOutput ;
        }
        return false;

    }

    public function getWarpEndPointPosition()
    {
        if ($this->warpOutput === null) {
            throw new WarpOutputNotSetException("sorry nowhere to warp no warpoutput is set");
        }
        return $this->warpOutput;
    }
}
