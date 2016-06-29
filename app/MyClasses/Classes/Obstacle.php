<?php namespace App\MyClasses\Classes;

use App\MyClasses\Interfaces\GridObjectInterface as GridObjectInterface;

use App\MyClasses\Interfaces\MoveableObjectInterface as MoveableObjectInterface;

use App\MyClasses\Exceptions\NoGridObjectFoundException as NoGridObjectFoundException ;
use App\MyClasses\Exceptions\IntialGridStartPositionCanOnlyBeSetOnceException as IntialGridStartPositionCanOnlyBeSetOnceException;

class Obstacle implements GridObjectInterface{

	protected $_x_position = null;

	protected $_y_position = null;
	protected $_type;

	protected $_grid_obj;

	public function __construct(Grid $GridObject , $InitialGridPosition  = null ){

		$this->type = "Obstacle";

		$this->setGrid($GridObject);

		if ( $InitialGridPosition !== null ){
			$this->setInitialGridPosition($InitialGridPosition);
		}
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

	public function setInitialGridPosition( $position ){

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

}
