<?php namespace App\MyClasses\Classes;
use App\MyClasses\Exceptions\GridException as GridException;
use App\MyClasses\Exceptions\GridPositionOutOfBoundsException as GridPositionOutOfBoundsException;
use App\MyClasses\Exceptions\GridPathIsBlockedException  as GridPathIsBlockedException;

use App\MyClasses\Interfaces\GridObjectInterface as GridObjectInterface;
use App\MyClasses\Interfaces\CanPlaceObjectsInWallInterface as CanPlaceObjectsInWallInterface;
use App\MyClasses\Interfaces\GridWarpPointInterface as GridWarpPointInterface;
use App\MyClasses\Interfaces\WallObjectInterface as WallObjectInterface;

class Grid implements CanPlaceObjectsInWallInterface , GridWarpPointInterface {
	private $_height,$_width;
	private $_objects_on_the_grid = array();
	public function __construct($width,$height){


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

	private function getGridHeight(){
		return $this->_height;
	}

	private function getGridWidth(){
		return $this->_width;
	}

	public function gridPositionExists($x,$y){
		if($x <= $this->getGridWidth() && $y <= $this->getGridHeight() && $x >= 0 && $y >= 0  || $this->positionHasWarpPoint(array($x,$y)) == true )
			return true;
		

		return false;
	
	}
	public function gridPositionExistsIncludeWalls($x,$y){
		
		if($x <= $this->getGridWidth() + 1 && $y <= $this->getGridHeight() + 1 && $x >= -1 && $y >= -1 )
			return true;
		
		return false;
	
	}
	public function canPlaceObjectOnPosition($position , GridObjectInterface $object = null){

		if($this->PositionArrayIsValid($position)){

		if ( $object && $object instanceof  WallObjectInterface ){

			if( !$this->gridPositionExistsIncludeWalls( $position[0], $position[1] ) ){

				throw new GridPositionOutOfBoundsException("the position requested does not exist on this grid");
				return false;	
			}
		}else{

			if ( !$this->gridPositionExists( $position[0], $position[1] )  ){
			
					return false;
			}

		}
			


			
		if ( $this->gridPositionIsBlocked($position) ){
				throw new GridPathIsBlockedException("the position (" . $position[0].",". $position[1] .") on the grid has already been taken by an blockable object");
				return false;
		}

			return true;
		}

		return false;	
	}
	public function positionHasWarpPoint($position){

		foreach ($this->_objects_on_the_grid as $key => $grid_obj) {
					
		if($grid_obj->getGridPosition() === $position && $grid_obj instanceof WarpPoint ){

			return true;
		}

		}	
		return false;
	}
	public function getWarpPointPosition($position){

		foreach ($this->_objects_on_the_grid as $key => $grid_obj) {
					
			if($grid_obj->getGridPosition() === $position && $grid_obj instanceof WarpPoint ){

				return $grid_obj->getWarpPointOutputPosition();
			}

		}
		return false;
	}
	public function placeObjectOnGrid(GridObjectInterface $object , $position ){
		
		if(  $object instanceof WallObjectInterface ){

		if ($this->canPlaceObjectOnPosition($position , $object)){
			return array_push( $this->_objects_on_the_grid , $object );
		}

		}

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
	public function gridPositionIsBlocked($position){

			


			
				foreach ($this->_objects_on_the_grid as $key => $grid_obj) {
					
					if($grid_obj->getGridPosition() === $position && $grid_obj->IsBlockable()){

						return true;
					}
				}
			

		return false;


	}
}