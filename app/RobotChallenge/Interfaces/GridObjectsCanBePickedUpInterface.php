<?php namespace RobotChallenge\Interfaces ;

interface GridObjectsCanBePickedUpInterface
{

    public function PassabaleObjectFoundOnPosition($position);
    public function PassOverObjectFromPosition($position);
}
