<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;

use App\RobotChallenge\Grid ;

use App\RobotChallenge\Robot ;

use App\RobotChallenge\Obstacle;


use App\RobotChallenge\WarpPoint ;

use App\RobotChallenge\Flag;

class GridTest extends TestCase
{
	private $_grid;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGetGridDimensionsValues()
    {
    	// test valid grid dimennions
    		$grid = new Grid(1,1);

        	$this->assertEquals(array(1,1),$grid->getGridDimensions());

    		$grid = new Grid(-10,10);

        	$this->assertEquals(array(10,10),$grid->getGridDimensions());

        	$grid = new Grid(50,"20");

        	$this->assertEquals(array(50,20),$grid->getGridDimensions());

            $grid = new Grid("maju","cow");

            $this->assertEquals(array(0,0),$grid->getGridDimensions());


	}

    /**
     * Test position exist
     *
     * @return void
     */
    public function testGridPositionExist(){

    	$grid = new Grid(50,50);

    	$this->assertEquals(true,$grid->GridPositionExists(20,30));

    	$this->assertEquals(true,$grid->GridPositionExists("cow","mau"));


    }
    public function testInvalidGridPositions(){
        $grid = new Grid(50,50);
        try {
        $grid->GridPositionExists(-20,-30);
        }
        catch (Exception $e) {
            $this->assertEquals( "position:(-20,-30) requested does not exist on this grid", $e->getMessage());
        }
        try {
        $grid->GridPositionExists(50,51);
        }
        catch (Exception $e) {
            $this->assertEquals( "position:(50,51) requested does not exist on this grid", $e->getMessage());
        }
    }


    public function testAddingRobotsToGrid(){

    	$grid = new Grid(50,50);

    	$robot = new Robot("north",$grid);

		$this->assertEquals(true,$robot->setInitialGridPosition(array(2,2)));

		//alse throws error position not found
        $robot = new Robot("north",$grid);
        try {
        $robot->setInitialGridPosition(array(-60,2));
        }
        catch (Exception $e) {
            $this->assertEquals( "position:(-60,2) requested does not exist on this grid", $e->getMessage());
        }


    }


    public function testRobotIsBlockingGridPosition(){

    	$grid = new Grid(50,50);

    	$robot = new Robot("north",$grid);



		$this->assertEquals(true,$robot->setInitialGridPosition(array(2,2)));

		$this->assertEquals(true,$grid->gridPositionIsBlocked(array(2,2)));


    }

    public function testPlaceObstacleOnGrid(){


    	$grid = new Grid(50,50);

    	$obstacle = new Obstacle($grid);

		$this->assertEquals(true,$obstacle->setInitialGridPosition(array(2,2)));

		$this->assertEquals(true,$grid->gridPositionIsBlocked(array(2,2)));

    }

    public function testValidWalkCommands(){
        $grid = new Grid(100,100);
        $robot = new Robot("South",$grid);


        $this->assertEquals(true,$robot->setInitialGridPosition(array(0,0)));

        $this->assertEquals(array(2,2),$robot->executeWalkCommand("fflff"));

        $grid = new Grid(50,50);
        $robot = new Robot("North",$grid);


        $this->assertEquals(true,$robot->setInitialGridPosition(array(1,1)));
        $this->assertEquals(array(1,0),$robot->executeWalkCommand(array("f","f","l","f","f")));

    }

    /**
     * Test failing grid
     *
     * @expectedException Exception
     */
    public function testInValidWalkCommands(){
        $grid = new Grid(50,50);
        $robot = new Robot("North",$grid,array(0,0));



        $this->assertEquals(false,$robot->executeWalkCommand("cow"));
        $this->assertEquals(false,$robot->executeWalkCommand(array("f","l","y","f","b")));

    }

    public function testRobotWalkCommandsWithObstacle(){
        $grid = new Grid(100,100);
        $robot = new Robot("North",$grid,array(50,50));
        $obstacle = new Obstacle($grid,array(48,50));

        $this->assertEquals(array(48,49),$robot->executeWalkCommand("fflffrbb"));


    }

    public function testRobotWalkCommandsWithWarpPoint(){
        $grid = new Grid(100,100);
        $robot = new Robot("North",$grid , array(50,0));
        $warpPoint = new WarpPoint($grid , array(50,-1) , array(50,100));
        $this->assertEquals(array(52,99),$robot->executeWalkCommand("ffrff"));


    }
    public function testRobotHaltIfBlockedWarpEndPoint(){
        $grid = new Grid(100,100);
        $robot = new Robot("North",$grid , array(50,0));
        $warpPoint = new WarpPoint($grid , array(50,-1) , array(50,100));
        $obstacle = new Obstacle($grid,array(50,100));

        $this->assertEquals(array(50,-1),$robot->executeWalkCommand("ffrff"));


    }
    public function testRemoveItemFromGrid(){
        $grid = new Grid(100,100);
        $flag = new Flag($grid , array(50,0));
        $this->assertEquals(true,$grid->isPassableItemFoundOnPosition(array(50,0)));
        $this->assertEquals(1,count($grid->getItemsOnGrid()));
        $this->assertEquals($flag,$grid->passOverItem($flag));
        $this->assertEquals(0,count($grid->getItemsOnGrid()));

    }
    public function testPassoverFlagToRobot(){
        $grid = new Grid(100,100);
        $flag = new Flag($grid , array(50,0));
        $robot = new Robot("North",$grid , array(50,0));
        $this->assertEquals(true,$grid->isPassableItemFoundOnPosition(array(50,0)));
        $this->assertEquals(2,count($grid->getItemsOnGrid()));
        $this->assertEquals(true,$robot->grabItem($flag));
        $this->assertEquals($flag,$robot->getInventory()[0]);
        $this->assertEquals(1,count($grid->getItemsOnGrid()));

    }

    public function testmoveRobotoverFlagToPickItUp(){
        $grid = new Grid(100,100);
        $flag = new Flag($grid , array(51,0));
        $flag2 = new Flag($grid , array(52,0));
        $robot = new Robot("east",$grid , array(50,0));
        $this->assertEquals(array(54,0),$robot->executeWalkCommand("ffff"));

        $this->assertEquals($flag,$robot->getInventory()[0]);
        $this->assertEquals($flag2,$robot->getInventory()[1]);

    }


}
