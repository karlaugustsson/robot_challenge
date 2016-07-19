<?php namespace RobotChallenge;

use RobotChallenge\Interfaces\GridObjectInterface ;
use RobotChallenge\Interfaces\WallObjectInterface ;
use RobotChallenge\Exceptions\NoGridObjectFoundException ;
use RobotChallenge\Exceptions\GridPositionNotSetException;
use RobotChallenge\Exceptions\WarpOutputNotSetException ;
use RobotChallenge\Exceptions\IntialGridStartPositionCanOnlyBeSetOnceException ;


class WarpPoint implements GridObjectInterface, WallObjectInterface
{
    private $grid_obj = null;
    private $x_position;
    private $y_position;
    private $type_of_oject;
    private $warp_output;

    public function __construct(Grid $grid, $warp_input, $warp_output)
    {
        $this->type_of_oject = "warppoint" ;
        $this->setGrid($grid);
        $this->setInitialGridPosition($warp_input);
        $this->setwarpEndpointPosition($warp_output);
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
        return $this->type_of_oject ;
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

    public function setwarpEndPointPosition($warp_output)
    {
        if ($this->getGrid()->canPlaceObjectOnPosition($warp_output)) {
            $this->warp_output = $warp_output ;
        }
        return false;

    }

    public function getWarpEndPointPosition()
    {
        if ($this->warp_output === null) {
            throw new WarpOutputNotSetException("sorry nowhere to warp no warpoutput is set");
        }
        return $this->warp_output;
    }
}
