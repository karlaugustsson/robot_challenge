<?php
namespace RobotChallenge;

use RobotChallenge\Interfaces\GridObjectInterface as GridObjectInterface;

use RobotChallenge\Interfaces\MoveableObjectInterface as MoveableObjectInterface;

use RobotChallenge\Interfaces\CanGrabObjectsInterface as CanGrabObjectsInterface;

use RobotChallenge\Interfaces\GrabbableObjectInterface as GrabbableObjectInterface;

use RobotChallenge\Exceptions\MoveableException as MoveableException;

use RobotChallenge\Exceptions\GridPositionOutOfBoundsException as GridPositionOutOfBoundsException;

use RobotChallenge\Exceptions\GridPathIsBlockedException  as GridPathIsBlockedException;

use RobotChallenge\Exceptions\NoGridObjectFoundException as NoGridObjectFoundException ;

use RobotChallenge\Exceptions\GridPositionNotSetException as GridPositionNotSetException;

use RobotChallenge\Exceptions\IntialGridStartPositionCanOnlyBeSetOnceException
as IntialGridStartPositionCanOnlyBeSetOnceException;

class Robot implements
    MoveableObjectInterface,
    GridObjectInterface,
    CanGrabObjectsInterface
{

    protected $x_position = null;
    protected $y_position = null;
    protected $type;
    protected $grid_obj;
    protected $faceing_direction ;
    protected $allowed_facing_directions = array( "north" , "south" , "east" , "west");
    protected $valid_walk_commands = array("f","b","l","r") ;
    protected $can_move ;
    protected $inventory = array() ;

    public function __construct($faceing_direction, Grid $grid_obj, $initial_grid_position = null)
    {

        $this->type = "Robot" ;
        $this->setGrid($grid_obj);
        $this->setcanMove();

        if (!$this->ValidFacingDirection($faceing_direction)) {
            return false;
        }

        if ($initial_grid_position !== null) {
            $this->setInitialGridPosition($initial_grid_position);
        }
        $this->faceing_direction = strtolower($faceing_direction) ;

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

    public function tryNewPosition($new_position)
    {
        try {
            if ($this->getGrid()->canPlaceObjectOnPosition($new_position)) {
                if ($this->getGrid()->PassabaleObjectFoundOnPosition($new_position)) {
                    $this->grabObject($this->getGrid()->PassOverObjectFromPosition($new_position));
                }

                $warpPosition = $this->grid_obj->getWarpPointPosition($new_position) ;

                if ($warpPosition != false) {
                    $this->x_position = $warpPosition[0];
                    $this->y_position = $warpPosition[1];
                } else {
                    $this->x_position = $new_position[0];
                    $this->y_position = $new_position[1];
                }

                return $new_position;
            } else {
                $this->stop();
                print "robot stopped becouse it hit a wall on position ("
                    . $this->x_position . "," . $this->y_position . ")\n\r" ;
                return $this->getGridPosition();
            }
        } catch (GridPathIsBlockedException $e) {
            $this->stop();
            print $e->getMessage();
            print "robot stopped on position (" . $this->x_position . "," . $this->y_position . ") \n\r" ;
            return $e->getMessage();
        }
    }

    public function getTypeOfGridObject()
    {
        return $this->type;
    }


    public function getGridPosition()
    {

        if ($this->x_position === null || $this->y_position === null) {
            throw new GridPositionNotSetException("No position on grid has been set");
            return false;
        }

        return array($this->x_position , $this->y_position);
    }

    public function isBlockable()
    {
        return true;
    }

    public function stop()
    {
        $this->can_move = false;
    }

    public function setCanMove($bool = null)
    {
        $this->can_move = $bool || true;
    }

    public function canMove()
    {
        return $this->can_move ;
    }

    public function executeWalkCommand($walk_commands)
    {

        if (is_string($walk_commands)) {
            $convertedwalkCommands = array();

            for ($i=0; $i < strlen($walk_commands); $i++) {
                $convertedwalkCommands[] = substr($walk_commands, $i, 1);
            }

            $walk_commands = $convertedwalkCommands;
        }

        if (!is_array($walk_commands)) {
            throw new MoveableException("WalkCommands are expected to be a string or array");
            return false;
        }

        foreach ($walk_commands as $command) {
            if (!$this->validWalkCommand($command)) {
                return false;
            }
        }


        foreach ($walk_commands as $key => $command) {
            if (!$this->canMove()) {
                break;
            }

            switch (strtolower($command)) {
                case 'l':
                    $this->changeDirectionLeft();
                    break;
                case 'r':
                    $this->changeDirectionRight();
                    break;
                case 'f':
                    $this->tryNewPosition($this->moveForward());

                    break;

                default:
                    $this->tryNewPosition($this->moveBackwards());
                    break;
            }
        }

        return $this->getGridPosition();
    }

    public function changeDirectionLeft()
    {
        switch ($this->getFacingDirection()) {
            case 'west':
                $this->setFacingdirection("south");
                break;

            case 'east':
                $this->setFacingdirection("north");
                break;
            case "north":
                $this->setFacingdirection("west");
                break;
            default:
                $this->setFacingdirection("east");
                break;
        }
    }

    public function changeDirectionRight()
    {
        switch ($this->getFacingDirection()) {
            case 'west':
                $this->setFacingdirection("north");
                break;

            case 'east':
                $this->setFacingdirection("south");
                break;
            case "north":
                $this->setFacingdirection("east");
                break;
            default:
                $this->setFacingdirection("west");
                break;
        }
    }

    public function moveForward()
    {
        switch ($this->getFacingDirection()) {
            case 'south':
                return array($this->x_position , $this->y_position + 1);

            break;

            case 'north':
                return array($this->x_position , $this->y_position - 1);

            break;
            case 'east':
                return array($this->x_position + 1 , $this->y_position);

            break;

            default:
                return array($this->x_position - 1 , $this->y_position);
            break;
        }
    }

    public function moveBackwards()
    {
        switch ($this->getFacingDirection()) {
            case 'south':
                return array($this->x_position , $this->y_position - 1);

            break;

            case 'north':
                return array($this->x_position, $this->y_position + 1);

            break;
            case 'east':
                return array($this->x_position - 1, $this->y_position);
            break;

            default:
                return array($this->x_position + 1, $this->y_position);

            break;
        }
    }

    public function getFacingDirection()
    {
        return $this->faceing_direction ;
    }

    public function setFacingdirection($direction)
    {
        $this->faceing_direction = $direction ;
    }

    public function validFacingDirection($facing_direction)
    {
        if (in_array(strtolower($facing_direction), $this->allowed_facing_directions)) {
            return true;
        }

        throw new MoveableException("allowed facing directions for this class is " .
                implode(",", $this->allowed_facing_directions) . " you gave : " . $facing_direction);
        return false;
    }

    public function validWalkCommand($command)
    {
        if (!in_array(strtolower($command), $this->valid_walk_commands)) {
            throw new MoveableException("you have supplied invalid walkCommands");

            return false;
        }


        return true;
    }

    public function grabObject(GrabbableObjectInterface $object)
    {
        return array_push($this->inventory, $object) ;
    }

    public function inventory()
    {
        return $this->inventory;
    }
}
