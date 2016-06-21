<?php namespace App\MyClasses\Classes;

use App\MyClasses\Classes\Grid as Grid;

class GridObject{
	
	//protected $_grid;

	protected $_x_position = null;

	protected $_y_position = null;

	protected $_type;

	protected $_grid_obj;

	public function __construct($TypeString , Grid $gridObj){
		$this->_type = $TypeString ; 

		$this->setGrid($gridObj);
	}

	public function setGrid($grid){
		$this->_grid_obj = $grid;
	}

	public function getGrid(){
		return $this->_grid_obj;
	}

	public function setInitialGridPosition( $position ){

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

}