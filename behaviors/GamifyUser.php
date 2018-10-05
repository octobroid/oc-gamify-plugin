<?php namespace Octobro\Gamify\Behaviors;

use RainLab\User\Models\User;
use October\Rain\Database\Collection;
use October\Rain\Extension\ExtensionBase;

class GamifyUser extends ExtensionBase
{
	/**
     * @var \October\Rain\Database\Model Reference to the extended model.
     */
    protected $model;

    /**
     * Constructor
     * @param \RainLab\User\Models\User $model The extended model.
     */
    public function __construct($model)
    {
        $this->model = $model;

        $model->belongsTo['level'] = 'Octobro\Gamify\Models\Level';
        $model->hasMany['achievements'] = 'Octobro\Gamify\Models\Achievement';
        $model->hasMany['level_logs'] = 'Octobro\Gamify\Models\LevelLog';
        $model->hasMany['point_logs'] = 'Octobro\Gamify\Models\PointLog';
    }

    public function getRank()
    {
        return User::where('points', '>', $this->model->points)->count() + 1;
    }

}