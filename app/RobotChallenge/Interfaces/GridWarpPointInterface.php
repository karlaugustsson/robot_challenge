<?php namespace App\RobotChallenge\Interfaces ;

interface GridWarpPointInterface
{
    public function positionHasWarpPoint($position);
    public function getWarpPointEndPosition($position);
}
