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

    private static function setLeaderboardArray($data, $type) {
        $dataArray = array();
        $count = 1;
        foreach($data as $user) {
            array_push($dataArray, [
                'rank' => $count,
                'name' => $user->name,
                'user_avatar' => $user->avatar ? $user->avatar->getPath() : null,
                'state' => $user->state ? $user->state->name : null,
                'points' => $user->{$type}
            ]);
            $count++;
        }
        return $dataArray;
    }

    public static function setWeeklyLeaderboard() {
        $startDate = Carbon::now()->startOfWeek()->format('Y-m-d');
        $endDate = Carbon::now()->endOfWeek()->format('Y-m-d');
        $data = User::orderBy('this_week_points', 'desc')->take(100)->get();

        $dataArray = self::setLeaderboardArray($data, 'this_week_points');

        $leaderboard = Leaderboard::whereBetween('date', [$startDate, $endDate])->first();

        if ($leaderboard) {
            $leaderboard->update([
                'data' => json_encode($dataArray)
            ]);
        } else {
            $leaderboardData = new Leaderboard();
            $leaderboardData->type = 'weekly';
            $leaderboardData->date = date('Y-m-d');
            $leaderboardData->data = json_encode($dataArray);
            $leaderboardData->save();
        }

        User::query()->update([
            'this_week_points' => 0
        ]);
    }

    public static function setMonthlyLeaderboard() {
        $startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
        $data = User::orderBy('this_month_points', 'desc')->take(100)->get();

        $dataArray = self::setLeaderboardArray($data, 'this_month_points');

        $leaderboard = Leaderboard::whereBetween('date', [$startDate, $endDate])->first();

        if ($leaderboard) {
            $leaderboard->update([
                'data' => json_encode($dataArray)
            ]);
        } else {
            $leaderboardData = new Leaderboard();
            $leaderboardData->type = 'monthly';
            $leaderboardData->date = date('Y-m-d');
            $leaderboardData->data = json_encode($dataArray);
            $leaderboardData->save();
        }

        User::query()->update([
            'this_month_points' => 0
        ]);
    }
}
