<?php namespace App\RobotChallenge;

use App\RobotChallenge\Interfaces\GridItemInterface ;
use App\RobotChallenge\Interfaces\CanBeGrabbedInterface ;

use App\RobotChallenge\Exceptions\NoGridInstanceFoundException ;
use App\RobotChallenge\Exceptions\IntialGridStartPositionCanOnlyBeSetOnceException ;

class Flag implements GridItemInterface, CanBeGrabbedInterface
{

    protected $position = null;
    protected $type;
    protected $gridInstance;

    public function __construct(Grid $gridInstance, $initalGridPosition = null)
    {

        $this->type = "Flag";

        $this->setGrid($gridInstance);

        if ($initalGridPosition !== null) {
            $this->setInitialGridPosition($initalGridPosition);
        }
    }

    public function setGrid($grid)
    {
        $this->gridInstance = $grid;
    }

    public function getGrid()
    {


        if ($this->gridInstance === null) {
            throw new NoGridInstanceFoundException("cant set position becouse no grid instace has been set");
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

    public function getTypeOfItem()
    {
        return $this->type;
    }

    public function getGridPosition()
    {
        if ($this->position === null) {
            return false;
        }
        return $this->position->getPosition();
    }

    public function isBlockable()
    {
        return false;
    }
}
