<?php namespace Octobro\Gamify\Components;

use Cache;
use Cms\Classes\ComponentBase;
use RainLab\User\Models\User;
use Octobro\Gamify\Models\LeaderboardLog;

class Leaderboard extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'        => 'Leaderboard Component',
            'description' => 'No description provided yet...'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    public function onRun()
    {
        $this->page['leaderboard'] = [
            'this_week'     => $this->getLeaderboardByType('leaderboard_this_week'),
            'last_week'     => json_decode($this->getLeaderboardByType('leaderboard_last_week')),
            'this_month'    => $this->getLeaderboardByType('leaderboard_this_month'),
            'all'           => $this->getLeaderboardByType('leaderboard_all'),
        ];
    }

    private function getLeaderboardByType(string $name)
    {
        if ($cache = Cache::get($name)) {
            return $cache;
        }

        switch ($name) {
            case 'leaderboard_this_week':
                $data = User::where('this_week_points', '>', 0)->orderBy('this_week_points', 'desc')->take(100)->get()->toArray();
                break;
            case 'leaderboard_last_week':
                $data = LeaderboardLog::select('data')->where('type', 'weekly')->orderBy('date', 'desc')->first()->data;
                break;
            case 'leaderboard_this_month':
                $data = User::where('this_month_points', '>', 0)->orderBy('this_month_points', 'desc')->take(100)->get()->toArray();
                break;
            case 'leaderboard_all':
                $data = User::where('points', '>', 0)->orderBy('points', 'desc')->take(100)->get()->toArray();
                break;
        }

        Cache::put($name, $data, now()->addDays(1));

        return $data;
    }
}
