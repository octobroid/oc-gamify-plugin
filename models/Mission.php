<?php namespace Octobro\Gamify\Models;

use Model;
use Octobro\Gamify\Models\Achievement;

/**
 * Mission Model
 */
class Mission extends Model
{
    use \October\Rain\Database\Traits\Validation;

    public $rules = [
        'name'                  => 'required|between:4,30',
        'points'                => 'required|integer',
        'class'                 => 'required',
        'type'                  => 'required|string:daily|weekly|one-time|always',
        'target'                => 'required'
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

    public static function achieve($user, $mission)
    {
        // Create/update mission progress
        if ($mission->type == 'daily') {
            $data = Achievement::getDailyMissionData($user->id, $mission->id)->first();
        } else if ($mission->type == 'weekly') {
            $data = Achievement::getWeeklyMissionData($user->id, $mission->id)->first();
        } else {
            $data = Achievement::getOneTimeMissionData($user->id, $mission->id)->first();
        }

        if ($data) {
            if ($data->achieved_count < $mission->target) {
                Achievement::find($data->id)->update([
                    'achieved_count' => (int) $data->achieved_count + 1
                ]);
            }
        } else {
            $achievement = new Achievement();
            $achievement->user_id = $user->id;
            $achievement->mission_id = $mission->id;
            $achievement->mission_type = $mission->type;
            $achievement->mission_date = date('Y-m-d');
            $achievement->achieved_count = 1;
            $achievement->is_achieved = false;
            $achievement->is_collected = false;
            $achievement->save();
        }
    }
}
