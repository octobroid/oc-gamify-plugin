<?php namespace Octobro\Gamify\Models;

use Model;
use ApplicationException;
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
        if (!$this->is_achieved && $this->mission->target <= $this->achieved_count) {
            $this->is_achieved = true;
            $this->save();
        }
    }

    public static function getDailyMissionData($userId, $missionId)
    {
        return self::where('user_id', $userId)->where('mission_id', $missionId)->where('mission_type', 'daily')->where('mission_date', date('Y-m-d'));
    }

    public static function getWeeklyMissionData($userId, $missionId)
    {
        $startDate = Carbon::now();
        $endDate = Carbon::now()->endOfWeek();
        return self::where('user_id', $userId)->where('mission_id', $missionId)->where('mission_type', 'weekly')->whereBetween('mission_date', [$startDate, $endDate]);
    }

    public static function getOneTimeMissionData($userId, $missionId)
    {
        return self::where('user_id', $userId)->where('mission_id', $missionId)->where('mission_type', 'one_time');
    }

    public static function achieve($user, $mission)
    {
        // Create/update mission progress
        switch ($mission->type) {
            case 'daily':
                $data = self::getDailyMissionData($user->id, $mission->id)->first();
                break;
            case 'weekly':
                $data = self::getWeeklyMissionData($user->id, $mission->id)->first();
                break;
            default:
                $data = self::getOneTimeMissionData($user->id, $mission->id)->first();
        }

        if ($data) {
            if ($data->achieved_count < $mission->target) {
                $achievement = self::find($data->id)->update([
                    'achieved_count' => (int) $data->achieved_count + 1
                ]);
            } else {
                throw new ApplicationException('Mission already completed');
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

        return $achievement;
    }

    public static function collect($user, $mission)
    {
        // Collect point
        switch ($mission->type) {
            case 'daily':
                $achievement = self::getDailyMissionData($user->id, $mission->id)->first();
                break;
            case 'weekly':
                $achievement = self::getWeeklyMissionData($user->id, $mission->id)->first();
                break;
            default:
                $achievement = self::getOneTimeMissionData($user->id, $mission->id)->first();
        }

        if (!$achievement) {
            throw new ApplicationException("Mission haven't started");
        }

        if ($achievement->is_collected == true) {
            throw new ApplicationException('Points already collected');
        } else if ($achievement->is_collected == false && $achievement->is_achieved == false && $achievement->achieved_count < $mission->target) {
            throw new ApplicationException("Mission haven't completed");
        } else {
            $achievement->update([
                'is_collected' => true
            ]);
        }

        PointLog::collectPoint($user, $mission, $mission->name);

        return $achievement;
    }
}
