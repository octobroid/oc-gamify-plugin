<?php namespace Octobro\Gamify\Models;

use Model;
use Carbon\Carbon;
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

    private static function getDailyMissionData($userId, $missionId) {
        return self::where('user_id', $userId)->where('mission_id', $missionId)->where('mission_type', 'daily')->where('mission_date', date('Y-m-d'));
    }

    private static function getWeeklyMissionData($userId, $missionId, $startDate, $endDate) {
        return self::where('user_id', $userId)->where('mission_id', $missionId)->where('mission_type', 'weekly')->whereBetween('mission_date', [$startDate, $endDate]);
    }

    private static function getOneTimeMissionData($userId, $missionId) {
        return self::where('user_id', $userId)->where('mission_id', $missionId)->where('mission_type', 'one_time');
    }

    public static function achieve($user, $mission)
    {
        // Create/update mission progress
        if ($mission->type == 'daily') {
            $data = self::getDailyMissionData($user->id, $mission->id)->first();
        } else if ($mission->type == 'weekly') {
            $startDate = Carbon::now()->startOfWeek()->format('Y-m-d');
            $endDate = Carbon::now()->endOfWeek()->format('Y-m-d');
            $data = self::getWeeklyMissionData($user->id, $mission->id, $startDate, $endDate)->first();
        } else {
            $data = self::getOneTimeMissionData($user->id, $mission->id)->first();
        }

        if ($data) {
            if ($data->achieved_count < $mission->target) {
                self::find($data->id)->update([
                    'achieved_count' => (int) $data->achieved_count + 1
                ]);
            } else {
                return "You've already completed the mission";
            }
        } else {
            $achievement = new self();
            $achievement->user_id = $user->id;
            $achievement->mission_id = $mission->id;
            $achievement->mission_type = $mission->type;
            $achievement->mission_date = date('Y-m-d');
            $achievement->achieved_count = 1;
            $achievement->is_achieved = false;
            $achievement->is_collected = false;
            $achievement->save();
        }
        return "Mission progress has been made";
    }

    public static function collect($user, $mission)
    {
        // Collect point
        if ($mission->type == 'daily') {
            $achievement = self::getDailyMissionData($user->id, $mission->id)->first();
        } else if ($mission->type == 'weekly') {
            $startDate = Carbon::now()->startOfWeek()->format('Y-m-d');
            $endDate = Carbon::now()->endOfWeek()->format('Y-m-d');
            $achievement = self::getWeeklyMissionData($user->id, $mission->id, $startDate, $endDate)->first();
        } else {
            $achievement = self::getOneTimeMissionData($user->id, $mission->id)->first();
        }

        if ($achievement->is_collected == true) {
            return "You've already collected the point";
        } else if ($achievement->is_collected == false && $achievement->is_achieved == false && $achievement->achieved_count < $mission->target) {
            return "You haven't completed the mission";
        } else {
            $achievement->update([
                'is_collected' => true
            ]);
        }

        PointLog::collectPoint($user, $mission, $mission->name, $mission->points);

        return 'Point has been collected';
    }
}
