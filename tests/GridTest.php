<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;

use App\MyClasses\Classes\Grid as Grid;
use App\MyClasses\Classes\Robot as Robot;

use App\MyClasses\Classes\Obstacle as Obstacle;


use App\MyClasses\Classes\WarpPoint as WarpPoint;

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

    	$this->assertEquals(false,$grid->GridPositionExists(-20,-30));

    	$this->assertEquals(false,$grid->GridPositionExists(51,50));

    	$this->assertEquals(true,$grid->GridPositionExists("cow","mau"));

    	
    }


    public function testAddingRobotsToGrid(){
    	
    	

    	$grid = new Grid(50,50);

    	$robot = new Robot("north",$grid);

		$this->assertEquals(true,$robot->setInitialGridPosition(array(2,2)));

		//alse throws error position not found
        $robot = new Robot("north",$grid);
		$this->assertEquals(false,$robot->setInitialGridPosition(array(-60,2)));


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


}