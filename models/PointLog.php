<?php namespace Octobro\Gamify\Models;

use Model;
use Db;
use Carbon\Carbon;
use RainLab\User\Models\User;
use Octobro\Gamify\Models\LeaderboardLog as Leaderboard;

/**
 * PointLog Model
 */
class PointLog extends Model
{
    /**
     * @var string The database table used by the model.
     */
    public $table = 'octobro_gamify_point_logs';

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
    public $belongsTo = [
        'user' => 'RainLab\User\Models\User',
    ];
    public $belongsToMany = [];
    public $morphTo = [
        'related' => [],
    ];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    public function beforeCreate()
    {
        $this->previous_amount = $this->user->points;
        $this->updated_amount = $this->previous_amount + $this->amount;
    }

    public function afterSave()
    {
        $this->user->refreshLevel();
    }

    public static function collectPoint($user, $relatedEvent, $description = null)
    {
        // Update points to user
        User::find($user->id)->update([
            'points' => (int) ($user->points + $relatedEvent->points),
            'spendable_points' => (int) ($user->spendable_points + $relatedEvent->points),
            'points_updated_at' => date('Y-m-d H:i:s'),
            'this_week_points' => (int) ($user->this_week_points + $relatedEvent->points),
            'this_month_points' => (int) ($user->this_month_points + $relatedEvent->points)
        ]);

        // Create point log
        $pointLog = new self();
        $pointLog->user_id = $user->id;
        $pointLog->description = $description;
        $pointLog->amount = $relatedEvent->points;
        $pointLog->related = $relatedEvent;
        $pointLog->save();
    }
}
