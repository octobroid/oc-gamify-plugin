<?php namespace Octobro\Gamify\Models;

use Model;
use Db;
use Carbon\Carbon;
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

    private static function setLeaderboardArray($data) {
        $dataArray = array();
        $count = 1;
        foreach($data as $log) {
            array_push($dataArray, [
                'rank' => $count,
                'name' => $log->user->name,
                'state' => $log->user->state->name,
                'points' => $log->amount
            ]);
            $count++;
        }
        return $dataArray;
    }

    public static function setWeeklyLeaderboard() {
        $startDate = Carbon::now()->startOfWeek()->format('Y-m-d');
        $endDate = Carbon::now()->endOfWeek()->format('Y-m-d');
        $data = self::whereBetween('created_at', [$startDate, $endDate])->select('user_id', Db::raw('SUM(amount) as amount'))->groupBy('user_id')->orderBy('amount', 'desc')->get();

        $dataArray = self::setLeaderboardArray($data);

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
    }

    public static function setMonthlyLeaderboard() {
        $startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
        $data = self::whereBetween('created_at', [$startDate, $endDate])->select('user_id', Db::raw('SUM(amount) as amount'))->groupBy('user_id')->orderBy('amount', 'desc')->get();

        $dataArray = self::setLeaderboardArray($data);

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
    }
}
