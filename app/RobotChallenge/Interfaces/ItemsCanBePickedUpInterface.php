<?php namespace App\RobotChallenge\Interfaces ;

interface ItemsCanBePickedUpInterface
{

    public function IsPassableItemFoundOnPosition($position);
    public function passOverItem($position);
}
