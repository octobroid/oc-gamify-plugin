<?php namespace Octobro\Gamify;

use Backend;
use System\Classes\PluginBase;
use RainLab\User\Models\User;
use RainLab\User\Models\PointLog;

/**
 * Gamify Plugin Information File
 */
class Plugin extends PluginBase
{
    public $require = ['RainLab.User'];

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'Gamify',
            'description' => 'No description provided yet...',
            'author'      => 'Octobro',
            'icon'        => 'icon-leaf'
        ];
    }

    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot()
    {
        User::extend(function($model) {
            $model->implement[] = 'Octobro\Gamify\Behaviors\GamifyUser';
        });
    }

    /**
     * Registers back-end navigation items for this plugin.
     *
     * @return array
     */
    public function registerNavigation()
    {
        return [
            'gamify' => [
                'label'       => 'Gamify',
                'url'         => Backend::url('octobro/gamify/leaderboardlogs'),
                'icon'        => 'icon-gamepad',
                'permissions' => ['octobro.gamify.*'],
                'order'       => 500,
                'sideMenu' => [
                    'leaderboardlogs' => [
                        'label'       => 'Leaderboard',
                        'icon'        => 'icon-certificate',
                        'url'         => Backend::url('octobro/gamify/leaderboardlogs'),
                        'permissions' => ['octobro.gamify.*']
                    ],
                    'levels' => [
                        'label'       => 'Levels',
                        'icon'        => 'icon-level-up',
                        'url'         => Backend::url('octobro/gamify/levels'),
                        'permissions' => ['octobro.gamify.*']
                    ],
                    'missions' => [
                        'label'       => 'Missions',
                        'icon'        => 'icon-check',
                        'url'         => Backend::url('octobro/gamify/missions'),
                        'permissions' => ['octobro.gamify.*']
                    ],
                    'vouchers' => [
                        'label'       => 'Vouchers',
                        'icon'        => 'icon-ticket',
                        'url'         => Backend::url('octobro/gamify/vouchers'),
                        'permissions' => ['octobro.gamify.*']
                    ],
                ]
            ],
        ];
    }

    public function registerSchedule($schedule)
    {
        $schedule->call(function () {
            PointLog::setWeeklyLeaderboard();
        })->weekly()->mondays()->at('00:00');

        $schedule->call(function () {
            PointLog::setMonthlyLeaderboard();
        })->cron('* * 1 * *');
    }
}
