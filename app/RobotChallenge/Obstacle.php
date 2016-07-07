<?php namespace RobotChallenge;

use RobotChallenge\Interfaces\GridObjectInterface as GridObjectInterface;

use RobotChallenge\Interfaces\MoveableObjectInterface as MoveableObjectInterface;

use RobotChallenge\Exceptions\NoGridObjectFoundException as NoGridObjectFoundException ;

use RobotChallenge\Exceptions\IntialGridStartPositionCanOnlyBeSetOnceException
as IntialGridStartPositionCanOnlyBeSetOnceException;

class Obstacle implements GridObjectInterface
{

    protected $x_position = null;

    protected $y_position = null;
    protected $type;

    protected $grid_obj;

    public function __construct(Grid $GridObject, $InitialGridPosition = null)
    {

        $this->type = "Obstacle";

        $this->setGrid($GridObject);

        if ($InitialGridPosition !== null) {
            $this->setInitialGridPosition($InitialGridPosition);
        }
    }

    public function setGrid($grid)
    {
        $this->_grid_obj = $grid;
    }

    public function getGrid()
    {


        if ($this->_grid_obj === null) {
            throw new NoGridObjectFoundException("cant set position becouse no grid object has been set");
            return false;
        }
        return $this->_grid_obj;
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
        return $this->_type;
    }

    public function getGridPosition()
    {
        if (!$this->x_position) {
            return false;
        }
        return array($this->x_position , $this->y_position);
    }

    public function isBlockable()
    {
        return true;
    }
}
