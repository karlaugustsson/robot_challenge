<?php namespace App\MyClasses\Classes;
use App\MyClasses\Exceptions\MoveableException as MoveableException;
use App\MyClasses\Classes\GridObject as GridObject;
use App\MyClasses\Classes\Grid as Grid;
class MoveableObject extends GridObject{

	protected $_faceingDirection ;
	protected $_allowed_facing_directions = array( "north" , "south" , "east" , "west");
	
	public function __construct($facingDirection , $typeOfGridObject , grid $GridObject){

		if ( ! in_array ( strtolower($facingDirection) , $this->_allowed_facing_directions ) ) {
			throw new MoveableException("allowed facing directions for this class is " . 
				implode(",", $this->_allowed_facing_directions) . " you gave : " . $facingDirection );
		}

		parent::__construct($typeOfGridObject,$GridObject);

		$this->facing_direction = $facingDirection ;
	}

	

}