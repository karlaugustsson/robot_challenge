<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use App\MyClasses\Classes\Robot as Robot;

class RobotTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testnewRobots()
    {
    	for ($i=0; $i < 10; $i++) { 
    		$robot = New Robot();
    		 $this->assertTrue(true);
    	}
       
    }
}
