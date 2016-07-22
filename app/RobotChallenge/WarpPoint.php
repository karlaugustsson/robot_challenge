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
        if ($this->getPositionInstance() !== null) {
            throw new IntialGridPositionCanOnlyBeSetOnceException("initial startValue can only be set once");
        }
        $this->setPositionInstance($position[0], $position[1]);
        $this->getGrid()->placeItemOnGrid($this, $this->getCurrentPosition());
        return true;

    }

    public function getTypeofItem()
    {
        return $this->type ;
    }

    public function getGridPosition()
    {

        if ($this->position === null || $this->position->getPosition() === false) {
            throw new GridPositionNotSetException("No position on grid has been set");
            return false;
        }

        return $this->getPositionInstance()->getPosition();
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
        return $this->warpOutput;
    }
    private function getPositionInstance()
    {
        return $this->position;
    }
    private function setPositionInstance($x, $y = null)
    {
        if (is_array($x) && $y === null) {
             $this->position = new Position($x[0], $y[1]);
        } else {
            $this->position = new Position($x, $y);
        }

    }
    private function getCurrentPosition()
    {
        return $this->getPositionInstance()->getPosition();
    }
    private function setPosition($x, $y = null)
    {

        if (is_array($x) && $y === null) {
             $this->getPositionInstance()->setPosition($x[0], $x[1]);
        } else {
            $this->getPositionInstance()->setPosition($x[0], $y[1]);
        }
    }
    private function getXposition()
    {
        return $this->getPositionInstance()->getXposition();
    }
    private function getYposition()
    {
        return $this->getPositionInstance()->getYposition();
    }
}
