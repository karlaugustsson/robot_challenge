<?php
namespace App\RobotChallenge;

use App\RobotChallenge\Interfaces\GridItemInterface ;
use App\RobotChallenge\Interfaces\ItemCanMoveInterface ;
use App\RobotChallenge\Interfaces\CanGrabItemsInterface ;
use App\RobotChallenge\Interfaces\CanBeGrabbedInterface ;

use App\RobotChallenge\Exceptions\InvalidWalkCommandException ;
use App\RobotChallenge\Exceptions\GridPositionOutOfBoundsException ;
use App\RobotChallenge\Exceptions\GridPathIsBlockedException  ;
use App\RobotChallenge\Exceptions\NoGridInstanceFoundException ;
use App\RobotChallenge\Exceptions\GridPositionNotSetException ;
use App\RobotChallenge\Exceptions\IntialGridPositionCanOnlyBeSetOnceException;

class Robot implements
    ItemCanMoveInterface,
    GridItemInterface,
    CanGrabItemsInterface
{

    protected $xPosition = null;
    protected $yPosition = null;
    protected $type;
    protected $gridInstance;
    protected $faceingDirection ;
    protected $allowedDirections = array( "north" , "south" , "east" , "west");
    protected $valid_walkCommands = array("f","b","l","r") ;
    protected $canMove ;
    protected $inventory = array() ;

    public function __construct($direction, Grid $gridInstance, $initialGridPosition = null)
    {

        $this->type = "Robot" ;
        $this->setGrid($gridInstance);
        $this->setcanMove();

        if (!$this->validDirection($direction)) {
            return false;
        }

        if ($initialGridPosition !== null) {
            $this->setInitialGridPosition($initialGridPosition);
        }
        $this->direction = strtolower($direction) ;

    }

    public function setGrid($grid)
    {
        $this->gridInstance = $grid;
    }

    public function getGrid()
    {

        if ($this->gridInstance === null) {
            throw new NogridInstanceectFoundException("cant set position becouse no grid instance has been set");
            return false;
        }
        return $this->gridInstance;
    }

    public function setInitialGridPosition($position)
    {
        if ($this->xPosition !== null) {
            throw new IntialGridStartPositionCanOnlyBeSetOnceException("initial startValue can onky be set once");
        }
        if ($this->getGrid()->placeItemOnGrid($this, $position)) {
            $this->xPosition = $position[0];
            $this->yPosition = $position[1];
            return true;
        } else {
            return false;
        }

    }

    public function tryNewPosition($newPosition)
    {
        try {
            if ($this->getGrid()->canPlaceItemOnPosition($newPosition)) {

                if ($this->getGrid()->IsPassableItemFoundOnPosition($newPosition)){
                    $this->grabItem($this->getGrid()->getPassableItemOnPosition($newPosition));
                }

                $warpPosition = $this->getGrid()->getWarpPointPosition($newPosition) ;

                if ($warpPosition != false) {
                    $this->xPosition = $warpPosition[0];
                    $this->yPosition = $warpPosition[1];
                } else {
                    $this->xPosition = $newPosition[0];
                    $this->yPosition = $newPosition[1];
                }

                return $newPosition;
            } else {
                $this->stop();
                print "robot stopped becouse it hit a wall on position ("
                    . $this->xPosition . "," . $this->yPosition . ")\n\r" ;
                return $this->getGridPosition();
            }
        } catch (GridPathIsBlockedException $e) {
            $this->stop();
            print $e->getMessage();
            print "robot stopped on position (" . $this->xPosition . "," . $this->yPosition . ") \n\r" ;
            return $e->getMessage();
        }
    }

    public function getTypeOfItem()
    {
        return $this->type;
    }


    public function getGridPosition()
    {

        if ($this->xPosition === null || $this->yPosition === null) {
            throw new GridPositionNotSetException("No position on grid has been set");
            return false;
        }

        return array($this->xPosition , $this->yPosition);
    }

    public function isBlockable()
    {
        return true;
    }

    public function stop()
    {
        $this->canMove = false;
    }

    public function setCanMove($bool = null)
    {
        $this->canMove = $bool || true;
    }

    public function canMove()
    {
        return $this->canMove ;
    }

    public function executeWalkCommand($walkCommands)
    {

        if (is_string($walkCommands)) {
            $convertedwalkCommands = array();

            for ($i=0; $i < strlen($walkCommands); $i++) {
                $convertedwalkCommands[] = substr($walkCommands, $i, 1);
            }

            $walkCommands = $convertedwalkCommands;
        }

        if (!is_array($walkCommands)) {
            throw new MoveableException("WalkCommands are expected to be a string or array");
            return false;
        }

        foreach ($walkCommands as $command) {
            if (!$this->validWalkCommand($command)) {
                return false;
            }
        }


        foreach ($walkCommands as $key => $command) {
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
        switch ($this->getDirection()) {
            case 'west':
                $this->setDirection("south");
                break;

            case 'east':
                $this->setDirection("north");
                break;
            case "north":
                $this->setDirection("west");
                break;
            default:
                $this->setDirection("east");
                break;
        }
    }

    public function changeDirectionRight()
    {
        switch ($this->getDirection()) {
            case 'west':
                $this->setDirection("north");
                break;

            case 'east':
                $this->setDirection("south");
                break;
            case "north":
                $this->setDirection("east");
                break;
            default:
                $this->setDirection("west");
                break;
        }
    }

    public function moveForward()
    {
        switch ($this->getDirection()) {
            case 'south':
                return array($this->xPosition , $this->yPosition + 1);

            break;

            case 'north':
                return array($this->xPosition , $this->yPosition - 1);

            break;
            case 'east':
                return array($this->xPosition + 1 , $this->yPosition);

            break;

            default:
                return array($this->xPosition - 1 , $this->yPosition);
            break;
        }
    }

    public function moveBackwards()
    {
        switch ($this->getDirection()) {
            case 'south':
                return array($this->xPosition , $this->yPosition - 1);

            break;

            case 'north':
                return array($this->xPosition, $this->yPosition + 1);

            break;
            case 'east':
                return array($this->xPosition - 1, $this->yPosition);
            break;

            default:
                return array($this->xPosition + 1, $this->yPosition);

            break;
        }
    }

    public function getDirection()
    {
        return $this->direction ;
    }

    public function setDirection($direction)
    {
        $this->direction = $direction ;
    }

    public function validDirection($direction)
    {
        if (in_array(strtolower($direction), $this->allowedDirections)) {
            return true;
        }

        throw new MoveableException("allowed facing directions for this class is " .
                implode(",", $this->allowedDirections) . " you gave : " . $facingdirection);
        return false;
    }

    public function validWalkCommand($command)
    {
        if (!in_array(strtolower($command), $this->valid_walkCommands)) {
            throw new InvalidWalkCommandException("you have supplied invalid walk commands");

            return false;
        }


        return true;
    }

    public function grabItem(CanBeGrabbedInterface $item)

    {
        $item = $this->getGrid()->passOverItem($item);
        return array_push($this->inventory, $item) ;
    }

    public function inventory()
    {
        return $this->inventory;
    }
}
