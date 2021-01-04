<?php namespace Octobro\Gamify\Models;

use Event;
use Exception;
use Model;
use Carbon\Carbon;
use Octobro\Gamify\Models\Mission;
use RainLab\User\Models\User;

/**
 * LeaderboardLog Model
 */
class LeaderboardLog extends Model
{
    /**
     * @var string The database table used by the model.
     */
    public $table = 'octobro_gamify_leaderboard_logs';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = ['type', 'date', 'data'];

    /**
     * @var array Jsoanble fields
     */
    protected $jsonable = ['data'];

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

    public function getPeriodAttribute()
    {
        if ($this->type == 'weekly') {
            return Carbon::parse($this->date)->startOfWeek()->format('d F, Y') . " - " . Carbon::parse($this->date)->endOfWeek()->format('d F, Y');
        } else {
            return Carbon::parse($this->date)->startOfMonth()->format('d F, Y') . " - " . Carbon::parse($this->date)->endOfMonth()->format('d F, Y');
        }
    }

    /**
     * Set leaderboard array
     * Could be overriden
     */
    private static function setLeaderboardArray($data, $type) {
        $dataArray = array();

        foreach ($data as $user) {
            array_push($dataArray, [
                'rank' => $user->{$type . "ly_rank"},
                'name' => $user->name,
                // 'user_id' => $user->id,
                'user_avatar' => $user->avatar ? $user->avatar->getPath() : null,
                'points' => $user->{"this_" . $type . "_points"}
            ]);
        }

        return $dataArray;
    }

    public static function setWeeklyLeaderboard() {
        $startDate = Carbon::yesterday()->startOfWeek()->format('Y-m-d');
        $endDate = Carbon::yesterday()->endOfWeek()->format('Y-m-d');
        $data = User::where('this_week_points', '>', 0)->orderBy('this_week_points', 'desc')->take(100)->get();

        $dataArray = self::setLeaderboardArray($data, 'week');

        $leaderboard = self::whereBetween('date', [$startDate, $endDate])->first();

        if ($leaderboard) {
            $leaderboard->update([
                'data' => json_encode($dataArray)
            ]);
        } else {
            $leaderboardData = new self();
            $leaderboardData->type = 'weekly';
            $leaderboardData->date = Carbon::yesterday()->startOfWeek()->format('Y-m-d');
            $leaderboardData->data = json_encode($dataArray);
            $leaderboardData->save();
        }

        User::query()->update([
            'this_week_points' => 0
        ]);

        Event::fire('octobro.gamify.leaderboard.afterSetWeeklyLeaderboard', [$data]);
    }

    public static function setMonthlyLeaderboard() {
        $startDate = Carbon::yesterday()->startOfMonth()->format('Y-m-d');
        $endDate = Carbon::yesterday()->endOfMonth()->format('Y-m-d');
        $data = User::where('this_month_points', '>', 0)->orderBy('this_month_points', 'desc')->take(100)->get();

        $dataArray = self::setLeaderboardArray($data, 'month');

        $leaderboard = self::whereBetween('date', [$startDate, $endDate])->first();

        if ($leaderboard) {
            $leaderboard->update([
                'data' => json_encode($dataArray)
            ]);
        } else {
            $leaderboardData = new self();
            $leaderboardData->type = 'monthly';
            $leaderboardData->date = Carbon::yesterday()->startOfMonth()->format('Y-m-d');
            $leaderboardData->data = json_encode($dataArray);
            $leaderboardData->save();
        }

        User::query()->update([
            'this_month_points' => 0
        ]);

        Event::fire('octobro.gamify.leaderboard.afterSetMonthlyLeaderboard', [$data]);
    }
}
