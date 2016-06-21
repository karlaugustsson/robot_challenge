<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use App\MyClasses\Classes\Grid as Grid;
use App\MyClasses\Classes\Robot as Robot;

use App\MyClasses\Classes\Obstacle as Obstacle;

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

    		
	}
    /**
     * Test failing grid
     *
     * @expectedException Exception
     */
    public function testInstanciatingFailingGrids(){

    	$grid = new Grid("maju","cow");
    }
    /**
     * Test failing grid
     *
     * @return void
     */
    public function testGridPositionExist(){

    	$grid = new Grid(50,50);

    	$this->assertEquals(true,$grid->GridPositionExists(20,30));

    	$this->assertEquals(false,$grid->GridPositionExists(-20,-30));

    	$this->assertEquals(false,$grid->GridPositionExists(51,50));

    	$this->assertEquals(false,$grid->GridPositionExists("cow","mau"));
    	
    }
    /**
     * Test failing grid
     *
     * @expectedException Exception
     */

    public function testAddingRobotsToGrid(){
    	
    	

    	$grid = new Grid(50,50);

    	$robot = new Robot("north",$grid);

		$this->assertEquals(true,$robot->setInitialGridPosition(array(2,2)));

		//alse throws error position not found
		$this->assertEquals(true,$robot->setInitialGridPosition(array(-60,2)));


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

		$this->assertEquals(false,$grid->gridPositionIsBlocked(array(60,40)));

    }
}