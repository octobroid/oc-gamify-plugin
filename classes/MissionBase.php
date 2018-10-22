<?php namespace Octobro\Gamify\Classes;

use Octobro\Gamify\Models\Mission;

class MissionBase {

    protected $mission;

    public function __construct(Mission $mission)
    {
        $this->mission = $mission;
    }

    public function beforeAchieve($achievement, $count, $data) { }
    
    public function afterAchieve($achievement, $count, $data) { }

    public function beforeCollect($achievement) { }

    public function afterCollect($achievement) { }
}