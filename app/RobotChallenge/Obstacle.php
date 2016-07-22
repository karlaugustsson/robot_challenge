<?php namespace App\RobotChallenge;

use App\RobotChallenge\Interfaces\GridItemInterface ;
use App\RobotChallenge\Interfaces\ItemCanMoveInterface;

use App\RobotChallenge\Exceptions\NoGridInstanceFoundException ;
use App\RobotChallenge\Exceptions\IntialGridPositionCanOnlyBeSetOnceException ;

class Obstacle implements GridItemInterface
{

    protected $position = null;
    protected $type;
    protected $gridInstance;

    public function __construct(Grid $gridInstance, $initalGridPosition = null)
    {

        $this->type = "Obstacle";

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
            throw new NoGridInstanceFoundException("cant set position becouse no grid instance was found");
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

    public function getTypeOfItem()
    {
        return $this->type;
    }

    public function getGridPosition()
    {
        if ($this->getPositionInstance() === null || $this->getCurrentPosition() === false) {
            throw new GridPositionNotSetException("No position on grid has been set");
            return false;
        }

        return $this->getPositionInstance()->getPosition();
    }

    public function isBlockable()
    {
        return true;
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
