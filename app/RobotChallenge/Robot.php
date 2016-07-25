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
    /**
     * this property will contain the instance of the grid to where the warppoint will be placed
     * @var gridInstance will contain an instace of Grid
     */
    private $gridInstance;
    /**
     * once instaciated will contain an instance of Position
     * @var Position
     */
    private $position = null;
    /**
     * once given value will cointain string of type of item
     * @var string
     */
    private $type;
    /**
     * a string representation of the direction the robot is faceing
     * @var string
     */
    private $faceingDirection ;
    /**
     * an array cointaing the allwood directions the robot may take
     * @var array
     */
    private $allowedDirections = array( "north" , "south" , "east" , "west");
    /**
     * an array containing shorts for walking-commands the robot understands
     * @var array
     */
    private $validWalkCommands = array("f","b","l","r") ;
    /**
     *  once set a bollean representing if the robt can move/walk
     * @var boolean
     */
    private $canMove ;
    /**
     * an array containing instances of items the robot have grabbed from the grid while walking
     * @var array
     */
    private $inventory = array() ;
    /**
     * first argumnet for the robot is the direction is should be faceing when created
     * second argumnet is the grid to which the robot will be placed on
     * third argument is an optional array of x and coordinates the robot should be placed on
     * when instanciated
     * @param string
     * @param Grid
     * @param array
     * @return  void
     */
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
    /**
     * sets a new grid instace
     * @param Grid
     */
    public function setGrid(Grid $grid)
    {
        $this->gridInstance = $grid;
    }
    /**
     * returns the grid of this class instance
     * @return [Grid]
     */
    public function getGrid()
    {

        if ($this->gridInstance === null) {
            throw new NogridInstanceectFoundException("no grid instance has been set");
            return false;
        }
        return $this->gridInstance;
    }
    /**
     *
     * This function takes an array with two values one for the the x postion on the grid and one for the y position,
     *  it creates a new instance on position with  an array of the y and y values , it also tells the gridInstance to place the robot in the gridItems array
     *  the grid instance will check if the positon wich you are trying to place your robot exist or is blocked by another item
     *
     *
     *
     * @param array $postion
     * @throws  IntialGridPositionCanOnlyBeSetOnceException is thrown it finds a postion insstance object,
     *          GridPathIsBlockedException is thrown from the gridInstance if the position wich your are trying to place the robot on is blocked
     *          GridPositionOutOfBoundsException is thrown from the grid instance if the position wich you are trying to place the robot does not exist
     */
    public function setInitialGridPosition($position)
    {
        if ($this->getPositionInstance() !== null) {
            throw new IntialGridPositionCanOnlyBeSetOnceException("initial startValue can only be set once");
        }
        $this->setPositionInstance($position[0], $position[1]);
        $this->getGrid()->placeItemOnGrid($this, $this->getCurrentPosition());
        return true;

    }
    /**
     * will try to place the robot on the grid-position provided by argument
     * the function will check if position is valid or else it will throw one of many exceptions
     * which will result in the robot stopping on its current position
     * if the robot is to stop on a warpointposition it will be transfered to the wappoint destination
     * the method will also look for one grabble item on the positon and grab it if it exist
     * @param  array an array with x and y coordinates of the new desired position for the robot
     * @return void
     * @throws   GridPositionOutOfBoundsException|GridPathIsBlockedException|GridPathIsBlockedException
     */
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
    /**
     * this will return what type of item the flag is this is represented by a string
     *
     * @return string
     */
    public function getTypeOfItem()
    {
        return $this->type;
    }

    /**
     *  returns an array with the x position and the y postion represented in ints
     *
     * @return array with two ints-values
     */
    public function getGridPosition()
    {
        if ($this->getPositionInstance() === null || $this->getCurrentPosition() === false) {
            throw new GridPositionNotSetException("No position on grid has been set");
            return false;
        }

        return $this->getPositionInstance()->getPosition();
    }
    /**
     * returns a bool to say if the item will block the position its standing on
     *
     * @return boolean
     */
    public function isBlockable()
    {
        return true;
    }
    /**
     * sets the CanMove variable to false and makes further walkcommands fail
     * @return bool
     */
    public function stop()
    {
        $this->canMove = false;
    }
    /**
     * takes a parameter (BOOLEAN) of whether or not the robot shoudl be alble to move
     * @param [type]
     */
    public function setCanMove($bool = null)
    {
        $this->canMove = $bool || true;
    }
    /**
     * returns whever or not the robot can move
     * @return bolean
     */
    public function canMove()
    {
        return $this->canMove ;
    }
    /**
     * will try to execute the argument-array of walking related commands given to the robot
     * such as changedirection of the robot , walk forward/backwards
     * @param  array takes an array with x and y coordinates
     * @return array returns the position of where the robot is currently at
     * @throw InvalidWalkCommandException will be thrown if bad command has been given
     */
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
            throw new InvalidWalkCommandException("WalkCommands are expected to be a string or array");
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
    /**
     * based on which direction the robot is currently faceing
     *  this function will set a new direction to the left
     * @return void
     */
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
    /**
     * based on which direction the robot is currently faceing
     *  this function will set a new direction to the right
     * @return void
     */
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
    /**
     * based on the robots faceing direction,
     * this function will calculate and return an array of a  new position
     *  representing the robot taking a step forward
     * @return array
     */
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
    /**
     * based on the robots faceing direction,
     * this function will calculate and return an array of a  new position
     *  representing the robot taking a step backwards
     * @return array
     */
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
    /**
     * returns a string representation of the the direction its faceing
     * @return string
     */
    public function getDirection()
    {
        return $this->direction ;
    }
    /**
     * sets string representation of the the direction its faceing
     * @param string
     */
    public function setDirection($direction)
    {
        $this->direction = $direction ;
    }
    /**
     * will check if the string is found in the valid faceingdirection array
     * if so returns true else throws an exception
     * @param  string
     * @return bollean
     * @throws  InvalidDirectionCommandException faceingdirection given is not allowed
     */
    public function validDirection($direction)
    {
        if (in_array(strtolower($direction), $this->allowedDirections)) {
            return true;
        }

        throw new InvalidDirectionCommandException("allowed facing directions for this class is " .
                implode(",", $this->allowedDirections) . " you gave : " . $facingdirection);
    }
    /**
     * will check if the string is found in the valid walk-command array
     * if so returns true else throws an exception
     * @param  string
     * @return bollean
     * @throws  InvalidWalkCommandException walkcommand not valid
     */
    public function validWalkCommand($command)
    {
        if (!in_array(strtolower($command), $this->validWalkCommands)) {
            throw new InvalidWalkCommandException("you have supplied invalid walk commands");

            return false;
        }


        return true;
    }
    /**
     * will add the item-instance to an array
     * and will return true or false if added
     * @param  CanBeGrabbedInterface
     * @return boolean
     */
    public function grabItem(CanBeGrabbedInterface $item)
    {
        $item = $this->getGrid()->passOverItem($item);
        return array_push($this->inventory, $item) ;
    }
    /**
     * returns the array of item-instances
     * @return array
     */
    public function getInventory()
    {
        return $this->inventory;
    }
    /**
     * returns instance of position
     * @return Position|null
     */
    private function getPositionInstance()
    {
        return $this->position;
    }
    /**
     * if argument on is array it will be treated as it has both x and y cooordinates
     *  if not array it will use both parameter to create a new postion instance
     * @param int|array
     * @param int|null
     */
    private function setPositionInstance($x, $y = null)
    {
        if (is_array($x) && $y === null) {
             $this->position = new Position($x[0], $y[1]);
        } else {
            $this->position = new Position($x, $y);
        }

    }
    /**
     * will return an array with ints represetning the robots position on the grids
     * @return array
     */
    private function getCurrentPosition()
    {
        return $this->getPositionInstance()->getPosition();
    }
    /**
     *  this will set a Position instance you can choose to give the function an array with two ints or two int values.
     *
     * @param array or int
     * @param int or null
     * @return  void
     */
    private function setPosition($x, $y = null)
    {

        if (is_array($x) && $y === null) {
             $this->getPositionInstance()->setPosition($x[0], $x[1]);
        } else {
            $this->getPositionInstance()->setPosition($x[0], $y[1]);
        }
    }
    /**
     * retuns an int wich will represent the x postion of the robot
     * @return int
     */
    private function getXposition()
    {
        return $this->getPositionInstance()->getXposition();
    }
    /**
     * retuns an int wich will represent the y postion of the flag
     * @return int
     */
    private function getYposition()
    {
        return $this->getPositionInstance()->getYposition();
    }
}
