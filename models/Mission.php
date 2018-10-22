<?php namespace Octobro\Gamify\Models;

use Db;
use Model;
use Event;
use Exception;
use ApplicationException;
use RainLab\User\Models\User;

/**
 * Mission Model
 */
class Mission extends Model
{
    use \October\Rain\Database\Traits\Validation;

    protected $defaultClass = 'Octobro\Gamify\Classes\MissionBase';

    public $rules = [
        'name'                  => 'required|between:4,30',
        'points'                => 'required|integer',
        'code'                  => 'required|string',
        'type'                  => 'required|string',
        'target'                => 'required|integer'
    ];
    /**
     * @var string The database table used by the model.
     */
    public $table = 'octobro_gamify_missions';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    public function getMissionClass()
    {
        $class = $this->class ?: $this->defaultClass;

        return new $class($this);
    }

    public function setForUser(User $user)
    {
        switch ($this->type) {
            case 'daily':
                $this->user_progress = Achievement::getDailyMissionData($user->id, $this->id)->first();
                break;
            case 'weekly':
                $this->user_progress = Achievement::getWeeklyMissionData($user->id, $this->id)->first();
                break;
            default:
                $this->user_progress = Achievement::getAnytimeMissionData($user->id, $this->id)->first();
        }
    }

    public function achieve(User $user, $count = 1, $data = null)
    {
        // Find achievement by mission
        switch ($this->type) {
            case 'daily':
                $achievement = Achievement::getDailyMissionData($user->id, $this->id)->first();
                break;
            case 'weekly':
                $achievement = Achievement::getWeeklyMissionData($user->id, $this->id)->first();
                break;
            default:
                $achievement = Achievement::getAnytimeMissionData($user->id, $this->id)->first();
        }

        // If achievement already achieved
        if ($achievement && $achievement->is_achieved) {
            throw new ApplicationException('Mission already completed');
        }

        try {
            Db::beginTransaction();

            // If achievement not found, create new one
            if (!$achievement) {
                $achievement                 = new Achievement();
                $achievement->user_id        = $user->id;
                $achievement->mission_id     = $this->id;
                $achievement->mission_type   = $this->type;
                $achievement->mission_date   = date('Y-m-d');
                $achievement->achieved_count = 0;
                $achievement->is_achieved    = false;
                $achievement->is_collected   = false;
                $achievement->data           = [];
            }

            $achievement->addData($data);

            // Extensibility
            Event::fire('octobro.gamify.mission.beforeAchieve', [$this, $achievement, $count, $data]);
            $this->getMissionClass()->beforeAchieve($achievement, $count, $data);

            // Add achievement count
            $achievement->achieved_count += $count;
            $achievement->save();

            // Extensibility
            Event::fire('octobro.gamify.mission.afterAchieve', [$this, $achievement, $count, $data]);
            $this->getMissionClass()->afterAchieve($achievement, $count, $data);

            Db::commit();
        }
        catch (Exception $e) {
            Db::rollBack();
            throw $e;
        }

        return $achievement;
    }
}
