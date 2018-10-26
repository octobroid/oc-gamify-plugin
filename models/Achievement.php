<?php namespace Octobro\Gamify\Models;

use Db;
use Model;
use Event;
use Exception;
use Carbon\Carbon;
use ApplicationException;

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

    public $jsonable = ['data'];

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
        // Is target reached?
        if (!$this->is_achieved && $this->mission->target <= $this->achieved_count) {
            $this->is_achieved = true;
            $this->save();

            // Is auto collect?
            if ($this->is_auto_collect) {
                $this->collect();
            }
        }
    }

    public function collect()
    {
        if ($this->is_collected) throw new ApplicationException('Points already collected.');

        if (!$this->is_achieved && $this->achieved_count < $this->mission->target) {
            throw new ApplicationException('Mission hasn\'t complete yet');
        }

        try {
            Db::beginTransaction();

            // Extensibility
            Event::fire('octobro.gamify.mission.beforeCollect', [$this]);

            $this->is_collected = true;
            $this->save();

            PointLog::collectPoint($this->user, $this->mission, $this->mission->name, $this->mission->points);

            // Extensibility
            Event::fire('octobro.gamify.mission.afterCollect', [$this]);

            Db::commit();
        }
        catch (Exception $e) {
            Db::rollBack();
            throw $e;
        }

        return $this;
    }

    public function addData($datum)
    {
        if (!$datum) return;

        $data = $this->data ?: [];
        $data[] = $datum;
        $this->data = $data;
    }

    public static function getDailyMissionData($userId, $missionId)
    {
        return self::where('user_id', $userId)->where('mission_id', $missionId)->where('mission_type', 'daily')->where('mission_date', date('Y-m-d'));
    }

    public static function getWeeklyMissionData($userId, $missionId)
    {
        $startDate = Carbon::now()->startOfWeek();
        $endDate = Carbon::now()->endOfWeek();
        return self::where('user_id', $userId)->where('mission_id', $missionId)->where('mission_type', 'weekly')->whereBetween('mission_date', [$startDate, $endDate]);
    }

    public static function getAnytimeMissionData($userId, $missionId)
    {
        return self::where('user_id', $userId)->where('mission_id', $missionId)->where('mission_type', 'anytime');
    }

}
