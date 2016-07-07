<?php namespace RobotChallenge;

use RobotChallenge\Interfaces\GridObjectInterface as GridObjectInterface;

use RobotChallenge\Interfaces\MoveableObjectInterface as MoveableObjectInterface;
use RobotChallenge\Interfaces\GrabbableObjectInterface as GrabbableObjectInterface;

use RobotChallenge\Exceptions\NoGridObjectFoundException as NoGridObjectFoundException;

use RobotChallenge\Exceptions\IntialGridStartPositionCanOnlyBeSetOnceException
as IntialGridStartPositionCanOnlyBeSetOnceException;

class Flag implements GridObjectInterface, GrabbableObjectInterface
{

    protected $x_position = null;
    protected $y_position = null;
    protected $type;
    protected $grid_obj;

    public function __construct(Grid $grid_object, $initial_grid_position = null)
    {

        $this->type = "Flag";

        $this->setGrid($grid_object);

        if ($initial_grid_position !== null) {
            $this->setInitialGridPosition($initial_grid_position);
        }
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
        return $this->type;
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
        return false;
    }
}
