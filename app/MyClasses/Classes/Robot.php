<?php namespace App\MyClasses\Classes;

use App\MyClasses\Interfaces\GridObjectInterface as GridObjectInterface;

use App\MyClasses\Interfaces\MoveableObjectInterface as MoveableObjectInterface;

use App\MyClasses\Exceptions\MoveableException as MoveableException; 

use App\MyClasses\Exceptions\GridPositionOutOfBoundsException as GridPositionOutOfBoundsException;


class Robot Implements MoveableObjectInterface , GridObjectInterface {
	
	protected $_x_position = null;

	protected $_y_position = null;

	protected $_type;

	protected $_grid_obj;
	
	protected $_faceing_direction ;
	
	protected $_allowed_facing_directions = array( "north" , "south" , "east" , "west");

	protected $_valid_walk_commands = array("f","b","l","r") ;

	protected $_can_move ; 

	
	public function __construct($faceingDirection , Grid $gridObj){

		$this->_type = "Robot" ; 
		$this->setGrid($gridObj);
		$this->setcanMove();
		if ( ! $this->ValidFacingDirection($faceingDirection) ) {
			
			return false;
		}

		$this->_faceing_direction = strtolower($faceingDirection) ;

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

	public function tryNewPosition($direction){
		try {

			$new_position = $this->determineNextGridPosition($direction);

			$this->_grid_obj->canPlaceObjectOnPosition($new_position);
			$this->_x_position = $new_position[0];
			$this->_y_position = $new_position[1];
			return $new_position;

			} catch (GridPathIsBlockedException $e) {

				$this->stop();
				return $e->getMessage();
			} catch( GridPositionOutOfBoundsException $e){
			
				$this->stop();

				return $this->getGridPosition();
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
	public function stop(){
		$this->_can_move = false;
	}
	public function setCanMove($bool = null){
		$this->_can_move = $bool || true;
	}
	public function canMove(){
		return $this->_can_move ; 
	}
	public function executeWalkCommand($walkCommands){
		

		if ( is_string($walkCommands) ){

	
			$convertedwalkCommands = array();
			
			for ($i=0; $i < strlen($walkCommands); $i++) { 
				$convertedwalkCommands[] = substr($walkCommands,$i , 1);
			}

			$walkCommands = $convertedwalkCommands;
		}

		if ( !is_array($walkCommands)){
			throw new MoveableException("WalkCommands are expected to be a string or array");
			return false;
		}

		foreach ($walkCommands as $command) {
			
			if( !$this->validWalkCommand($command) ){
			
			return false;
			}
		}


		foreach ($walkCommands as $key => $command) {
			
			if(!$this->canMove()){
				break;
			}
			
			switch (strtolower($command)) {
				case 'l':
					$this->changeFacingDirection("l");
					break;
				case 'r':
					$this->changeFacingDirection("r");
					break;
				case 'f':
					$this->tryNewPosition("f");
					
				break;
				
				default:
					$this->tryNewPosition("b");
				break;
			}
		}

		return $this->getGridPosition();


	}
	public function changeFacingDirection($robotTurningDirection){
		
		if( !$this->validWalkCommand($robotTurningDirection) ){

			return false;
		}
		switch ($this->_faceing_direction) {
			case 'south':
				if( $robotTurningDirection == "l" ){
					
					$this->_faceing_direction = "west";

				}else{

					$this->_faceing_direction = "east";
				}
			break;
			
			case 'north':
				if( $robotTurningDirection == "l" ){
					$this->_faceing_direction = "west";
				}else{
					$this->_faceing_direction = "east";
				}
			break;

			case 'west':
				if ( $robotTurningDirection == "l" ){
					$this->faceing_direction = "south";
				}else{
					$this->_faceing_direction = "north";
				}
			break;

			default:
				if ( $robotTurningDirection == "l" ){
					$this->_faceing_direction = "north";
				}else{
					$this->_faceing_direction = "south";
				}
			break;
	}
}

public function determineNextGridPosition($direction){
		
		if( !$this->validWalkCommand($direction) ){
			return false;
		}
	
		switch ($this->_faceing_direction) {
			case 'south':
				if($direction == "f"){
					
			
					return array($this->_x_position , $this->_y_position + 1);
				}else{
					return array($this->_x_position , $this->_y_position - 1);
				}
			break;
			
			case 'north':
				if($direction == "f"){
	
					return array($this->_x_position , $this->_y_position - 1);
				}else{
					return array($this->_x_position, $this->_y_position + 1);
				}
			break;
			case 'east':
				if($direction == "f"){

					return array($this->_x_position - 1 , $this->_y_position);
				}else{
					return array($this->_x_position + 1, $this->_y_position);
				}
			break;

			default:
				if($direction == "f"){
			
					return array($this->_x_position + 1 , $this->_y_position);
				}else{
					return array($this->_x_position - 1, $this->_y_position);
				}
			break;
		}
}
	public function validFacingDirection($faceingDirection){

		if ( in_array ( strtolower($faceingDirection) , $this->_allowed_facing_directions ) ){
			return true;
		}

		throw new MoveableException("allowed facing directions for this class is " . 
				implode(",", $this->_allowed_facing_directions) . " you gave : " . $faceingDirection );
		return false;
	}
	public function validWalkCommand($walkCommand){

	
		
		if ( !in_array( strtolower($walkCommand), $this->_valid_walk_commands ) ){
			
			throw new MoveableException("you have supplied invalid walkCommands");
			
			return false;			
		}
			

		return true;
	}
		
		


}