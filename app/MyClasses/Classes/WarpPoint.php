<?php namespace App\MyClasses\Classes ; 


use App\MyClasses\Interfaces\GridObjectInterface as GridObjectInterface ;
use App\MyClasses\Interfaces\WallObjectInterface as WallObjectInterface;


use App\MyClasses\Exceptions\NoGridObjectFoundException as NoGridObjectFoundException ; 
use App\MyClasses\Exceptions\GridPositionNotSetException as GridPositionNotSetException; 
use App\MyClasses\Exceptions\WarpOutputNotSetException as WarpOutputNotSetException; 
use App\MyClasses\Exceptions\IntialGridStartPositionCanOnlyBeSetOnceException as IntialGridStartPositionCanOnlyBeSetOnceException;
//use App\MyClasses\Interfaces\MoveableObjectInterface as MoveableObjectInterface ;

class WarpPoint implements GridObjectInterface, WallObjectInterface{
	private $_grid_obj = null;
	private $_x_position;
	private $_y_position;
	private $_typeOfObject;
	private $_warpOutput;

	public function __construct(Grid $grid , $warpInput , $warpOutput){
		$this->_typeOfObject = "warppoint" ; 
		$this->setGrid($grid);
		$this->setInitialGridPosition($warpInput);
		$this->setwarpEndpointPosition($warpOutput);
		
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

	public function setInitialGridPosition($position ){

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

	public function getTypeOfGridObject(){
		return $this->_typeOfObject ; 
	}

	public function getGridPosition(){

		if($this->_x_position === null || $this->_y_position == null){
			throw new NoGridObjectFoundException("No position on grid has been set");
			return false;
		}

		return array($this->_x_position , $this->_y_position);

	}

	public function IsBlockable(){
		return false;
	}

	public function setwarpEndPointPosition($warpOutput){
		if($this->getGrid()->canPlaceObjectOnPosition($warpOutput));
		$this->_warpOutput = $warpOutput ; 
	}
	public function getWarpEndPointPosition(){
		if($this->_warpOutput === null){
		 throw new WarpOutputNotSetException("sorry nowhere to warp no warpoutput is set");
		}
		return $this->_warpOutput;
	}
}