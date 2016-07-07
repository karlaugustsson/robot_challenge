<?php namespace RobotChallenge\Interfaces ;

interface GridObjectsCanBePickedUpInterface
{

    public function passabaleObjectFoundOnPosition($position);
    public function passOverObjectFromPosition($position);
}
