<?php namespace App\MyClasses\Interfaces ;

interface GridObjectsCanBePickedUpInterface
{

    public function PassabaleObjectFoundOnPosition($position);
    public function PassOverObjectFromPosition($position);
}
