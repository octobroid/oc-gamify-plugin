<?php namespace Octobro\Gamify\Models;

use Model;

/**
 * Mission Model
 */
class Mission extends Model
{
    use \October\Rain\Database\Traits\Validation;

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

    public function setForUser($user)
    {
        switch ($this->type) {
            case 'daily':
                $this->user_progress = Achievement::getDailyMissionData($user->id, $this->id)->first();
                break;
            case 'weekly':
                $this->user_progress = Achievement::getWeeklyMissionData($user->id, $this->id)->first();
                break;
            default:
                $this->user_progress = Achievement::getOneTimeMissionData($user->id, $this->id)->first();
        }
    }
}
