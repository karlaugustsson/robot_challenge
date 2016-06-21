<?php namespace App\MyClasses\Classes;

class GridObject{
	
	protected $_grid;

	protected $_x_position = null;

	protected $_y_position = null;

	public function constructor(){

	}

	public function setGrid($grid){
		$this->_grid = $grid;
	}

	public function getGrid(){
		try {
			if($this->_grid){

			}else{
				throw new Exception("no grid has been set further actions requires an gridobject");
			}
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

	public function setGridPosition($position){

		$this->_x_position = $position[0];
		$this->_y_position = $position[1];
	}

	public function getGridPosition(){
		if ( !$this->_x_position ){
			return false;
		}
		return array($this->_x_position , $this->_y_position);
	}

}