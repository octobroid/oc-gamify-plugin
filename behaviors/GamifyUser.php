<?php namespace Octobro\Gamify\Behaviors;

use Exception;
use Carbon\Carbon;
use RainLab\User\Models\User;
use Octobro\Gamify\Models\Mission;
use Octobro\Gamify\Models\Level;
use Octobro\Gamify\Models\LevelLog;
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
        $model->hasMany['redemption_logs'] = 'Octobro\Gamify\Models\RedemptionLog';

        $model->bindEvent('model.beforeCreate', function() use ($model) {
            $model->level = $this->getDefaultLevel();
        });
    }

    public function refreshLevel()
    {
        if (!$this->model->points) {
            $this->model->points = 0;
        }

        $level = Level::where('min_points', '<=', $this->model->points)->orderBy('min_points', 'desc')->first();

        if ($this->model->level_id != $level->id) {
            $this->updateLevel($level);
        }

        return $this->model->level;
    }

    protected function getDefaultLevel()
    {
        return Level::orderBy('min_points', 'asc')->first();
    }

    public function updateLevel(Level $newLevel)
    {
        if ($this->model->level_id != $newLevel->id) {

            if ($this->model->level) {
                $levelLog                 = new LevelLog;
                $levelLog->user           = $this->model;
                $levelLog->previous_level = $this->model->level ?: null;
                $levelLog->updated_level  = $newLevel;
                $levelLog->save();
            }

            $this->model->level = $newLevel;
            $this->model->level_updated_at = Carbon::now();
            $this->model->save();

            // Get achievement by leveling up
            // Make loop in case user gets point which passing many levels above
            for($i = $newLevel->id; $i > 1; $i--) {
                try {
                    Mission::whereCode("achieve-level-" . $i)->first()->achieve($this->model, 1, null);
                } catch(Exception $e) {
                    continue;
                }
            }
        }

        return $this->model->level;
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
