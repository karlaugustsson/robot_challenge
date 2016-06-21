<?php namespace App\MyClasses\Classes;

class Robot{
	private $_grid;
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
}