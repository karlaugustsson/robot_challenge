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
    /**
     * The constructor takes the gridInstance to which the obstacle will be placed on
     * it also takes an optional initial position-array for the obstacle to be placed on the grid
     * @param Grid
     * @param array/null
     */
    public function __construct(Grid $gridInstance, $initalGridPosition = null)
    {

        $this->type = "Obstacle";

        $this->setGrid($gridInstance);

        if ($initalGridPosition !== null) {
            $this->setInitialGridPosition($initalGridPosition);
        }
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
            throw new NoGridInstanceFoundException("cant set position becouse no grid instance was found");
            return false;
        }
        return $this->gridInstance;
    }
    /**
     *
     * This function takes an array with two values one for the the x postion on the grid and one for the y position,
     *  it creates a new instance on position with  an array of the y and y values , it also tells the gridInstance to place the object in the gridItems array
     *  the grid instance will check if the positon wich you are trying to place your obstacle exist or is blocked by another item
     *
     *
     *
     * @param array $postion
     * @throws  IntialGridPositionCanOnlyBeSetOnceException is thrown it finds a postion insstance object,
     *          GridPathIsBlockedException is thrown from the gridInstance if the position wich your are trying to place the obstacle on is blocked
     *          GridPositionOutOfBoundsException is thrown from the grid instance if the position wich you are trying to place the obstacle does not exist
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
     * this will return what type of item the obstacle is this is represented by a string
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

    public function isBlockable()
    {
        return true;
    }
    private function getPositionInstance()
    {
        return $this->position;
    }
    /**
     *  this will set a Position instance you can choose to give the function an array with two ints or two int values.
     *
     * @param array or int
     * @param int or null
     * @return  void
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
     * this funtion returs an array with the x and y position (both values of the array is ints) for where the obstacle is positioned
     * @return array
     */
    private function getCurrentPosition()
    {
        return $this->getPositionInstance()->getPosition();
    }
    /**
     * this will update the position for the obstacle with new values it takes an array with x and y values
     * or two arguments with x and y position
     * @param array/int
     * @param int/null
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
     * retuns an int wich will represent the x postion of the obstacle
     * @return int
     */
    private function getXposition()
    {
        return $this->getPositionInstance()->getXposition();
    }
    /**
     * retuns an int wich will represent the y postion of the obstacle
     * @return int
     */
    private function getYposition()
    {
        return $this->getPositionInstance()->getYposition();
    }
}
