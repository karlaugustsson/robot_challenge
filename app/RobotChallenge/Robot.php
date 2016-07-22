<?php
namespace App\RobotChallenge;

use App\RobotChallenge\Interfaces\GridItemInterface ;
use App\RobotChallenge\Interfaces\ItemCanMoveInterface ;
use App\RobotChallenge\Interfaces\CanGrabItemsInterface ;
use App\RobotChallenge\Interfaces\CanBeGrabbedInterface ;

use App\RobotChallenge\Exceptions\InvalidWalkCommandException ;
use App\RobotChallenge\Exceptions\InvalidCommandException ;
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

    private $position = null;
    private $type;
    private $gridInstance;
    private $faceingDirection ;
    private $allowedDirections = array( "north" , "south" , "east" , "west");
    private $validWalkCommands = array("f","b","l","r") ;
    private $canMove ;
    private $inventory = array() ;

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
            throw new NogridInstanceectFoundException("no grid instance has been set");
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

    public function tryNewPosition($newPosition)
    {
        try {
            if ($this->getGrid()->canPlaceItemOnPosition($newPosition)) {
                $this->setPosition($newPosition);

                if ($this->getGrid()->IsPassableItemFoundOnPosition($this->getCurrentPosition())) {
                    $this->grabItem($this->getGrid()->getPassableItemOnPosition($this->getCurrentPosition()));
                }

                if ($this->getGrid()->positionHasWarpPoint($this->getCurrentPosition())) {
                    $this->setPosition($this->getGrid()->getWarpPointEndPosition($this->getCurrentPosition()));
                }
            }
        } catch (GridPositionOutOfBoundsException $e) {
            $this->stop();
            $message = "robot stopped becouse it hit a wall, current position:%s \n\r";
            printf($message, implode($this->getCurrentPosition(), ","));
        } catch (GridPathIsBlockedException $e) {
            $this->stop();
            print $e->getMessage();
            printf(" current position (%s)", implode($this->getCurrentPosition()));
        } catch (\Exception $e) {
            $this->stop();
            print $e->getMessage();
        }


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
                return array($this->position->getXPosition() , $this->position->getYPosition() + 1);

            break;

            case 'north':
                return array($this->position->getXPosition() , $this->position->getYPosition() - 1);

            break;
            case 'east':
                return array($this->position->getXPosition() + 1 , $this->position->getYPosition());

            break;

            default:
                return array($this->position->getXPosition() - 1 , $this->position->getYPosition());
            break;
        }
    }

    public function moveBackwards()
    {
        switch ($this->getDirection()) {
            case 'south':
                return array($this->position->getXPosition() , $this->position->getYPosition() - 1);

            break;

            case 'north':
                return array($this->position->getXPosition() , $this->position->getYPosition() + 1);

            break;
            case 'east':
                return array($this->position->getXPosition()  - 1, $this->position->getYPosition());
            break;

            default:
                return array($this->position->getXPosition()  + 1, $this->position->getYPosition());

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

        throw new InvalidDirectionCommandException("allowed facing directions for this class is " .
                implode(",", $this->allowedDirections) . " you gave : " . $facingdirection);
    }

    public function validWalkCommand($command)
    {
        if (!in_array(strtolower($command), $this->validWalkCommands)) {
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

    public function getInventory()
    {
        return $this->inventory;
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
