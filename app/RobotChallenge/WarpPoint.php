<?php namespace App\RobotChallenge;

use App\RobotChallenge\Interfaces\GridItemInterface ;
use App\RobotChallenge\Interfaces\CanBePlacedInsideWallInterface ;


use App\RobotChallenge\Exceptions\NoGridItemFoundException ;
use App\RobotChallenge\Exceptions\GridPositionNotSetException;
use App\RobotChallenge\Exceptions\WarpOutputNotSetException ;
use App\RobotChallenge\Exceptions\IntialGridPositionCanOnlyBeSetOnceException ;

class WarpPoint implements GridItemInterface, CanBePlacedInsideWallInterface
{
    /**
     * this property will contain the instance of the grid to where the warppoint will be placed
     * @var gridInstance will contain an instace of Grid
     */
    private $gridInstance = null;
    /**
     * once instaciated will contain an instance of Position
     * @var Position
     */
    private $position = null;
    /**
     * once given value will cointain string of type of item
     * @var string
     */
    private $type = null;
    /**
     * will contain an array with x and y value of wich position the warppoint will take you
     * @var [type]
     */
    private $warpOutput;

    /**
     * takes an instace of grid and an array cointaing x and y cordinates for where warpoint will be
     * last parameter is to which position the warppoint will take you (x,y)
     * @param Grid
     * @param array
     * @param array
     */
    public function __construct(Grid $gridInstance, $warpInput, $warpOutput)
    {
        $this->type = "warppoint" ;
        $this->setGrid($gridInstance);
        $this->setInitialGridPosition($warpInput);
        $this->setwarpEndpointPosition($warpOutput);
    }
    /**
     * sets instance of Grid class
     * @param Grid
     */
    public function setGrid(Grid $gridInstance)
    {
        $this->gridInstance = $gridInstance;
    }

    /**
     *  returns the grid Instance
     * @return Grid
     * @throws an NoGridItemFoundException if no instance of Grid is found
     */
    public function getGrid()
    {

        if ($this->gridInstance === null) {
            throw new NoGridItemFoundException("cant set position becouse no grid instance has been set");
            return false;
        }
        return $this->gridInstance;
    }
    /**
     *  sets an instance of grid and returns true on success
     *  it also update the grid instance of the item and its position
     *  throws an error if positon for the grid has already been set
     * @param array
     * @return bolean f initial postion was set
     * @throws IntialGridPositionCanOnlyBeSetOnceException will thow exception if gridInstance has already been set
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
     * returns an string represting what type of item this is
     * @return string
     */
    public function getTypeofItem()
    {
        return $this->type ;
    }
    /**
     * will try to get the postion of the item on the grid
     * will return an array with x and y coordinates or throw an error if not found
     *
     * @return array
     * @throws GridPositionNotSetException if it cant find an position for the item on the gridInstancce
     */
    public function getGridPosition()
    {

        if ($this->position === null || $this->position->getPosition() === false) {
            throw new GridPositionNotSetException("No position on grid has been set");
        }

        return $this->getPositionInstance()->getPosition();
    }
    /**
     * tells whever this item is blocking its position on the grid (true|false)
     * @return boolean
     */
    public function isBlockable()
    {
        return false;
    }
    /**
     * will set the output of the warpPoint store it in an array with x and y values
     * returns false on failure
     * @param  array
     * @return boolean
     */
    public function setwarpEndPointPosition($warpOutput)
    {
        if ($this->getGrid()->canPlaceItemOnPosition($warpOutput)) {
            $this->warpOutput = $warpOutput ;
            return true;
        }

        return false;

    }
    /**
     * returns array with x and y cooordinates for the end destionation of the warppoint
     * @return array
     */
    public function getWarpEndPointPosition()
    {
        return $this->warpOutput;
    }
    /**
     * returns stored instace of Position wich holds this items positioninfo
     * @return Position
     */
    private function getPositionInstance()
    {
        return $this->position;
    }
    /**
     * if first parameter is array it expect it to hold x and coordinates for the item
     * else it exceptcts both parameterss to be x and y coordinates
     * it will create a new instace of Position
     * @param int|array
     * @param int|null
     * @return void
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
     * returns an array with the current postion of the warpoint
     * @return array
     */
    private function getCurrentPosition()
    {
        return $this->getPositionInstance()->getPosition();
    }
    /**
     * will give the position instance a new position value
     * can be given an array (int,int) or two values (INT) respresenting x and y
     * @param int|array
     * @param int
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
     * returns the x-position for the item
     * @return int
     */
    private function getXposition()
    {
        return $this->getPositionInstance()->getXposition();
    }
    /**
     * returns the y positon for the item
     * @return int
     */
    private function getYposition()
    {
        return $this->getPositionInstance()->getYposition();
    }
}
