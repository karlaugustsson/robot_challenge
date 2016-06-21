<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use App\MyClasses\Classes\Grid as Grid;


class GridTest extends TestCase
{	
	private $_grid;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testgetGridDimensionsValues()
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
    public function testFailingGrids(){

    	$grid = new Grid("maju","cow");
    }
}