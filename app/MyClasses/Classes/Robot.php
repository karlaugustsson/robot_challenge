<?php namespace App\MyClasses\Classes;

use App\MyClasses\Interfaces\GridObjectInterface as GridObjectInterface;

use App\MyClasses\Interfaces\MoveableObjectInterface as MoveableObjectInterface;

use App\MyClasses\Exceptions\MoveableException as MoveableException; 


class Robot Implements MoveableObjectInterface , GridObjectInterface {
	
	protected $_x_position = null;

	protected $_y_position = null;

	protected $_type;

	protected $_grid_obj;
	
	protected $_faceingDirection ;
	
	protected $_allowed_facing_directions = array( "north" , "south" , "east" , "west");

	
	public function __construct($facingDirection , Grid $gridObj){

		$this->_type = "Robot" ; 
		$this->setGrid($gridObj);
		
		if ( ! in_array ( strtolower($facingDirection) , $this->_allowed_facing_directions ) ) {
			
			throw new MoveableException("allowed facing directions for this class is " . 
				implode(",", $this->_allowed_facing_directions) . " you gave : " . $facingDirection );
		}

		$this->facing_direction = $facingDirection ;

	}


	public function setGrid($grid){
		$this->_grid_obj = $grid;
	}

	public function getGrid(){
		return $this->_grid_obj;
	}

	public function setInitialGridPosition($position ){
		
		if( $this->getGrid()->placeObjectOnGrid($this,$position) ){

			$this->_x_position = $position[0];
			$this->_y_position = $position[1];
			return true;

		}else{
			return false;
		}

	}

	public function getTypeOfGridObject(){
		return $this->_type;
	}

	public function getGridPosition(){
		if ( !$this->_x_position ){
			return false;
		}
		return array($this->_x_position , $this->_y_position);
	}

	public function IsBlockable(){
		return true;
	}

	public function executeWalkCommand($walkCommands){
		
		if ( is_string($walkCommands) ){

	
			$convertedwalkCommands = array();
			
			for ($i=0; $i < strlen($walkCommands) -1; $i++) { 
				$convertedwalkCommands[] = substr($walkCommands,$i , 1);
			}

			$walkCommands = $convertedwalkCommands;
		}

		if ( !is_array($walkCommands)){
			throw new MoveableException("WalkCommands are expected to be a string or array");
			return false;
		}

		if( !$this->validWalkCommands($walkCommands) ){
			throw new MoveableException("you have supplied invalid walkCommands");
			return false;
		}

		return true;


	}
	public function changeFacingDirection($facingDirection){

	}
	public function validWalkCommands($walkCommands){

		foreach ($walkCommands as $key => $command) {
		
		if ( !in_array( strtolower($command), array("f","b","l","r") ) )
			return false;
	
		}

		return true;
		
		;
	}

}