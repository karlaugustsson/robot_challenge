<?php namespace App\MyClasses\Classes;

use App\MyClasses\Interfaces\GridObjectInterface as GridObjectInterface;

use App\MyClasses\Interfaces\MoveableObjectInterface as MoveableObjectInterface;

use App\MyClasses\Interfaces\CanGrabObjectsInterface as CanGrabObjectsInterface;

use App\MyClasses\Interfaces\GrabbableObjectInterface as GrabbableObjectInterface;

use App\MyClasses\Exceptions\MoveableException as MoveableException; 

use App\MyClasses\Exceptions\GridPositionOutOfBoundsException as GridPositionOutOfBoundsException;

use App\MyClasses\Exceptions\GridPathIsBlockedException  as GridPathIsBlockedException;

use App\MyClasses\Exceptions\NoGridObjectFoundException as NoGridObjectFoundException ; 

use App\MyClasses\Exceptions\GridPositionNotSetException as GridPositionNotSetException; 

use App\MyClasses\Exceptions\IntialGridStartPositionCanOnlyBeSetOnceException as IntialGridStartPositionCanOnlyBeSetOnceException;

class Robot Implements MoveableObjectInterface , GridObjectInterface , CanGrabObjectsInterface {
	
	protected $_x_position = null;

	protected $_y_position = null;

	protected $_type;

	protected $_grid_obj;
	
	protected $_faceing_direction ;
	
	protected $_allowed_facing_directions = array( "north" , "south" , "east" , "west");

	protected $_valid_walk_commands = array("f","b","l","r") ;

	protected $_can_move ; 

	protected $_inventory = array() ; 

	
	public function __construct($faceingDirection , Grid $gridObj , $InitialGridPosition  = null){

		$this->_type = "Robot" ; 
		$this->setGrid($gridObj);
		$this->setcanMove();

		if ( ! $this->ValidFacingDirection($faceingDirection) ) {
			
			return false;
		}

		if ( $InitialGridPosition !== null ){
			$this->setInitialGridPosition($InitialGridPosition);
		}
		$this->_faceing_direction = strtolower($faceingDirection) ;

	}


	public function setGrid($grid){
		$this->_grid_obj = $grid;
	}

	public function getGrid(){

		if($this->_grid_obj === null){
			throw new NoGridObjectFoundException("cant set position becouse no grid object has been set");
			return false;
		}
		return $this->_grid_obj;
	}

	public function setInitialGridPosition($position){
		if($this->_x_position !== null){
			throw new IntialGridStartPositionCanOnlyBeSetOnceException("initial startValue can onky be set once");
		}
		if( $this->getGrid()->placeObjectOnGrid($this,$position) ){

			$this->_x_position = $position[0];
			$this->_y_position = $position[1];
			return true;

		}else{
			return false;
		}

	}

	public function tryNewPosition($new_position){
		try {


			if( $this->getGrid()->canPlaceObjectOnPosition($new_position)){

				if($this->getGrid()->PassabaleObjectFoundOnPosition($new_position)){
					$this->grabObject($this->getGrid()->PassOverObjectFromPosition($new_position));
				}
				
				$warpPosition = $this->_grid_obj->getWarpPointPosition($new_position) ; 
				
				if( $warpPosition != false ){
					$this->_x_position = $warpPosition[0];
					$this->_y_position = $warpPosition[1];	
				
				}else{

					$this->_x_position = $new_position[0];
					$this->_y_position = $new_position[1];			
				}

			return $new_position;

			}else{
				$this->stop();
				print "robot stopped becouse it hit a wall on position (" . $this->_x_position . "," . $this->_y_position . ")\n\r" ;  
				return $this->getGridPosition();
			}


			} catch (GridPathIsBlockedException $e) {
	
				$this->stop();
				print $e->getMessage();
				print "robot stopped on position (" . $this->_x_position . "," . $this->_y_position . ") \n\r" ;  
				return $e->getMessage();

			}
	}

	public function getTypeOfGridObject(){
		return $this->_type;
	}


	public function getGridPosition(){

		if($this->_x_position === null || $this->_y_position === null){

			throw new GridPositionNotSetException("No position on grid has been set");
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
					$this->changeDirectionLeft();
					break;
				case 'r':
					$this->changeDirectionRight();
					break;
				case 'f':
					$this->tryNewPosition($this->moveForward());
					
				break;
				
				default:
					$this->tryNewPosition($this->moveBackwards());
				break;
			}
		}

		return $this->getGridPosition();


	}
	public function changeDirectionLeft(){
		switch ($this->getFacingDirection()) {

			case 'west':
				$this->setFacingdirection("south");
			break;

			case 'east':
				$this->setFacingdirection("north");
			break;
			case "north":
				$this->setFacingdirection("west");
			break;
			default:
				
				$this->setFacingdirection("east");
			break;
	}
	}
	public function ChangeDirectionRight(){

		switch ($this->getFacingDirection()) {

			case 'west':
				$this->setFacingdirection("north");
			break;
			
			case 'east':
				$this->setFacingdirection("south");
			break;
			case "north":
				$this->setFacingdirection("east");
			break;
			default:
				$this->setFacingdirection("west");
			break;
	}
	}
	public function moveForward(){
		switch ($this->getFacingDirection()) {
			case 'south':

				return array($this->_x_position , $this->_y_position + 1);
				
			break;
			
			case 'north':
				
					return array($this->_x_position , $this->_y_position - 1);
				
			break;
			case 'east':
					return array($this->_x_position + 1 , $this->_y_position);

			break;

			default:

				return array($this->_x_position - 1 , $this->_y_position);
			break;
		}
	}
	public function moveBackwards(){
		
		switch ($this->getFacingDirection()) {
			case 'south':

				return array($this->_x_position , $this->_y_position - 1);

			break;
			
			case 'north':

				return array($this->_x_position, $this->_y_position + 1);
				
			break;
			case 'east':

				return array($this->_x_position - 1, $this->_y_position);	
			break;

			default:

				return array($this->_x_position + 1, $this->_y_position);
			
			break;
		}
	}
	public function getFacingDirection(){
		return $this->_faceing_direction ; 
	}
	public function setFacingdirection($direction){
		$this->_faceing_direction = $direction ; 
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

	public function grabObject(GrabbableObjectInterface $object){

		array_push($this->_inventory, $object) ;
		return true;
	}
	public function inventory(){
		return $this->_inventory; 
	}
		
		


}