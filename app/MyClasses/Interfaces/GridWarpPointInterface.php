<?php namespace App\MyClasses\Interfaces ;

interface GridWarpPointInterface
{
    public function positionHasWarpPoint($position);
    public function getWarpPointPosition($position);
}
