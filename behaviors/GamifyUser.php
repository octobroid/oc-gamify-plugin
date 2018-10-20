<?php namespace Octobro\Gamify\Behaviors;

use RainLab\User\Models\User;
use Octobro\Gamify\Models\Level;
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

        $model->addFillable([
            'points',
            'spendable_points',
            'points_updated_at',
            'spendable_points_updated_at',
            'this_week_points',
            'this_month_points'
        ]);

        $model->belongsTo['level'] = 'Octobro\Gamify\Models\Level';
        $model->hasMany['achievements'] = 'Octobro\Gamify\Models\Achievement';
        $model->hasMany['level_logs'] = 'Octobro\Gamify\Models\LevelLog';
        $model->hasMany['point_logs'] = 'Octobro\Gamify\Models\PointLog';
    }

    public function afterCreate()
    {
        $this->refreshLevel();
    }

    public function refreshLevel()
    {
        $level = Level::where('min_points', '<=', $this->model->points)->orderBy('min_points', 'desc')->first();

        if ($this->model->level_id != $level->id) {
            $this->model->level = $level;
            $this->model->save();
        }
    }

    public function getWeeklyRankAttribute()
    {
        return User::where('this_week_points', '>', $this->model->this_week_points)->count() + 1;
    }

    public function getMonthlyRankAttribute()
    {
        return User::where('this_month_points', '>', $this->model->this_month_points)->count() + 1;
    }

    public function getRankAttribute()
    {
        return User::where('points', '>', $this->model->points)->count() + 1;
    }

}
