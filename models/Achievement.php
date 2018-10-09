<?php namespace Octobro\Gamify\Models;

use Model;
use RainLab\User\Models\User;
use Octobro\Gamify\Models\PointLog;
use Octobro\Gamify\Models\LevelLog;

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

    public static function collect($user, $mission)
    {
        // Collect point
        $achievement = Achievement::where('user_id', $user->id)->where('mission_id', $mission->id)
        ->where('mission_date', date('Y-m-d'))
        ->update([
            'is_collected' => true
        ]);

        // Create point log
        $pointLog = new PointLog();
        $pointLog->user_id = $user->id;
        $pointLog->description = $mission->name;
        $pointLog->amount = $mission->points;
        $pointLog->related = $achievement;
        $pointLog->save();

        // Update points to user
        User::find($user->id)->update([
            'points' => (int) ($user->points + $mission->points),
            'spendable_points' => (int) ($user->spendable_points + $mission->points),
            'points_updated_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
