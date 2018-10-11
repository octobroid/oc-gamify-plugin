<?php namespace Octobro\Gamify\Models;

use Model;
use Carbon\Carbon;
use RainLab\User\Models\User;
use Octobro\Gamify\Models\PointLog;
use Octobro\Gamify\Models\LevelLog;
use Octobro\Gamify\Models\Level;

/**
 * Achievement Model
 */
class Achievement extends Model
{
    /**
     * @var string The database table used by the model.
     */
    public $table = 'octobro_gamify_achievements';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [
        'user_id',
        'mission_id',
        'mission_type',
        'mission_date',
        'data',
        'achieved_count',
        'is_achieved',
        'is_collected'
    ];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [
        'user'    => 'RainLab\User\Models\User',
        'mission' => 'Octobro\Gamify\Models\Mission',
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    public function afterSave()
    {
        if ($this->mission->target == $this->achieved_count && $this->is_achieved == false) {
            $this->is_achieved = true;
            $this->save();
        }
    }

    public static function getDailyMissionData($userId, $missionId) {
        return self::where('user_id', $userId)->where('mission_id', $missionId)->where('mission_type', 'daily')->where('is_achieved', true)->where('mission_date', date('Y-m-d'));
    }

    public static function getWeeklyMissionData($userId, $missionId, $startDate, $endDate) {
        return self::where('user_id', $userId)->where('mission_id', $missionId)->where('mission_type', 'weekly')->where('is_achieved', true)->whereBetween('mission_date', [$startDate, $endDate]);
    }

    public static function getOneTimeMissionData($userId, $missionId) {
        return self::where('user_id', $userId)->where('mission_id', $missionId)->where('mission_type', 'one_time');
    }

    public static function collect($user, $mission)
    {
        $startDate = Carbon::now()->startOfWeek()->format('Y-m-d');
        $endDate = Carbon::now()->endOfWeek()->format('Y-m-d');

        // Collect point
        if ($mission->type == 'daily') {
            $achievement = self::getDailyMissionData($user->id, $mission->id);
        } else if ($mission->type == 'weekly') {
            $achievement = self::getWeeklyMissionData($user->id, $mission->id, $startDate, $endDate);
        } else {
            $achievement = self::getOneTimeMissionData($user->id, $mission->id);
        }

        $achievement->update([
            'is_collected' => true
        ]);

        // Create point log
        $pointLog = new PointLog();
        $pointLog->user_id = $user->id;
        $pointLog->description = $mission->name;
        $pointLog->amount = $mission->points;
        $pointLog->related = $mission;
        $pointLog->save();

        // Update points to user
        User::find($user->id)->update([
            'points' => (int) ($user->points + $mission->points),
            'spendable_points' => (int) ($user->spendable_points + $mission->points),
            'points_updated_at' => date('Y-m-d H:i:s'),
        ]);

        // Reset leaderboard
        PointLog::setWeeklyLeaderboard();
        PointLog::setMonthlyLeaderboard();
    }
}
