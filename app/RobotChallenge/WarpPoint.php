<?php namespace App\RobotChallenge;

use App\RobotChallenge\Interfaces\GridItemInterface ;
use App\RobotChallenge\Interfaces\CanBePlacedInsideWallInterface ;

use App\RobotChallenge\Exceptions\NoGridItemFoundException ;
use App\RobotChallenge\Exceptions\GridPositionNotSetException;
use App\RobotChallenge\Exceptions\WarpOutputNotSetException ;
use App\RobotChallenge\Exceptions\IntialGridPositionCanOnlyBeSetOnceException ;


class WarpPoint implements GridItemInterface, CanBePlacedInsideWallInterface
{
    private $gridInstance = null;
    private $position = null;
    private $type;
    private $warpOutput;

    public function __construct(Grid $gridInstance, $warpInput, $warpOutput)
    {
        $this->type = "warppoint" ;
        $this->setGrid($gridInstance);
        $this->setInitialGridPosition($warpInput);
        $this->setwarpEndpointPosition($warpOutput);
    }

    public function setGrid($gridInstance)
    {
        $this->gridInstance = $gridInstance;
    }


    public function getGrid()
    {

        if ($this->gridInstance === null) {
            throw new NoGridItemFoundException("cant set position becouse no grid instance has been set");
            return false;
        }
        return $this->gridInstance;
    }

    public function setInitialGridPosition($position)
    {
        if ($this->position !== null ) {
            throw new IntialGridPositionCanOnlyBeSetOnceException("initial startValue can only be set once");
        }
        if ($this->getGrid()->placeItemOnGrid($this, $position)) {

            $this->position = new Position($position[0],$position[1]);
            return true;
        } else {
            return false;
        }

    }

    public function getTypeofItem()
    {
        return $this->type ;
    }

    public function getGridPosition()
    {

        if ($this->position === null || $this->position->getPosition() === false ) {
            throw new GridPositionNotSetException("No position on grid has been set");
            return false;
        }

        return $this->position->getPosition();
    }

    public function isBlockable()
    {
        return false;
    }

    public function setwarpEndPointPosition($warpOutput)
    {
        if ($this->getGrid()->canPlaceItemOnPosition($warpOutput)) {
            $this->warpOutput = $warpOutput ;
            return true;
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
