<?php namespace App\MyClasses\Classes;
use App\MyClasses\Exceptions\GridException as GridException;
use App\MyClasses\Exceptions\GridPositionOutOfBoundsException as GridPositionOutOfBoundsException;
use App\MyClasses\Exceptions\GridPathIsBlockedException  as GridPathIsBlockedException;
use App\MyClasses\Interfaces\GridObjectInterface as GridObjectInterface;

class Grid{
	private $_height,$_width;
	private $_objects_on_the_grid = array();
	public function __construct($width,$height){


		if( (int)$height == 0 ){
			
			throw new GridException("Grid height must be heigher than 0");	
		}

		if ( (int)$width == 0 ){
			throw new GridException("Grid Width Must be wider than 0");	
		}


		$this->_height = (int)$height;
		
		$this->_width = (int)$width;
		
		if($this->_height < 0 ){
			$this->_height = abs($height);

		}

		if($this->_width < 0){
			$this->_width = abs($width);
		}
	}

	public function getGridDimensions(){
		return array($this->_width,$this->_height);
	}

	public function getGridHeight(){
		return $this->_height;
	}

	public function getGridWidth(){
		return $this->_width;
	}

	public function gridPositionExists($x,$y){
		if($x <= $this->_width && $y <= $this->_height && $x >= 0 && $y >= 0){
			return true;
	}
		return false;
	
	}

	public function canPlaceObjectOnPosition($position){

		if($this->PositionArrayIsValid($position)){
			
		if ( !$this->gridPositionExists( $position[0], $position[1] ) ){
				throw new GridPositionOutOfBoundsException("the position requested does not exist on this grid");
				return false;
		}

			
		if ( $this->gridPositionIsBlocked($position) ){
				throw new GridPathIsBlockedException("the position (" . $position[0].",". $position[1] .") on the grid was blocked");
				return false;
		}

			return true;
		}
		return false;	
	}

	public function placeObjectOnGrid(GridObjectInterface $object , $position ){
		

		if ($this->canPlaceObjectOnPosition($position)){
			return array_push( $this->_objects_on_the_grid , $object );
		}

		return false;


	}

	private function PositionArrayIsValid($position){
		
		if ( !is_array( $position) ){
			throw new GridException("argument position is expected to be an array", 1);
			return false;
			
		}
		if( $position[0] === null || $position[1] === null ){
			
			throw new GridException("position-array should have two keys for x and y position");
			return false;
		}

		return true;
	}
	public function changePositionOfObject(Moveable $object){
		// todo
	}
	public function gridPositionIsBlocked($position){

			

			if($this->gridPositionExists($position[0],$position[1])){
			
				foreach ($this->_objects_on_the_grid as $key => $grid_obj) {
					
					if($grid_obj->getGridPosition() === $position && $grid_obj->IsBlockable()){

						return true;
					}
				}
			}
			
			return false;
		

		return false;


	}
}