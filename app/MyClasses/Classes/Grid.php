<?php namespace App\MyClasses\Classes;
use App\MyClasses\Exceptions\GridException as GridException;

class Grid{
	private $_height,$_width;

	public function __construct($height,$width){


			if( (int)$height == 0 ){
			
				throw new GridException("Grid height must be heiger than 0");	
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
		return array($this->_height,$this->_width);
	}

	public function getGridHeight(){
		return $this->_height;
	}

	public function getGridWidth(){
		return $this->_width;
	}
}