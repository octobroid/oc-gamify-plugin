<?php namespace Octobro\Gamify\Models;

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

    private static function setLeaderboardArray($data, $type) {
        $dataArray = array();
        foreach($data as $user) {
            array_push($dataArray, [
                'rank' => $user->{$type . "ly_rank"},
                'name' => $user->name,
                'user_avatar' => $user->avatar ? $user->avatar->getPath() : null,
                'state' => $user->state ? $user->state->name : null,
                'points' => $user->{"this_" . $type . "_points"}
            ]);
        }
        return $dataArray;
    }

    public static function setWeeklyLeaderboard() {
        $startDate = Carbon::yesterday()->startOfWeek()->format('Y-m-d');
        $endDate = Carbon::yesterday()->endOfWeek()->format('Y-m-d');
        $data = User::orderBy('this_week_points', 'desc')->take(100)->get();

        $rankArray = ["1st", "2nd", "3rd"];

        foreach($data as $user) {
            Mission::whereCode("weekly-top-100")->first()->achieve($user, 1, null);
            if (in_array($user->weekly_rank, $rankArray)) {
                try {
                    Mission::whereCode("weekly-" . $rankArray[$user->weekly_rank - 1])->first()->achieve($user, 1, null);
                } catch(Exception $e) {
                    continue;
                }
            }
        }

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
    }

    public static function setMonthlyLeaderboard() {
        $startDate = Carbon::yesterday()->startOfMonth()->format('Y-m-d');
        $endDate = Carbon::yesterday()->endOfMonth()->format('Y-m-d');
        $data = User::orderBy('this_month_points', 'desc')->take(100)->get();

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
    }
}
