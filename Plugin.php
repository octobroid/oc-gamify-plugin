<?php namespace Octobro\Gamify;

use Yaml;
use File;
use Backend;
use System\Classes\PluginBase;
use RainLab\User\Models\User;
use RainLab\User\Controllers\Users as UsersController;
use Octobro\Gamify\Models\LeaderboardLog;

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

        UsersController::extend(function($controller) {
            $controller->implement[] = 'Backend.Behaviors.RelationController';
            $controller->relationConfig = '$/octobro/gamify/controllers/users/relationConfig.yaml';
        });

        UsersController::extendFormFields(function($form, $model, $context) {
            if (!$model instanceof User) return;
            
            $configFile = __DIR__ .'/config/user_fields.yaml';
            $config = Yaml::parse(File::get($configFile));
            $form->addTabFields($config);
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
                    'achievements' => [
                        'label'       => 'Achievement Logs',
                        'icon'        => 'icon-trophy',
                        'url'         => Backend::url('octobro/gamify/achievements'),
                        'permissions' => ['octobro.gamify.*']
                    ],
                    'pointlogs' => [
                        'label'       => 'Point Logs',
                        'icon'        => 'icon-history',
                        'url'         => Backend::url('octobro/gamify/pointlogs'),
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
                    'rewards' => [
                        'label'       => 'Rewards',
                        'icon'        => 'icon-gift',
                        'url'         => Backend::url('octobro/gamify/rewards'),
                        'permissions' => ['octobro.gamify.*']
                    ],
                ]
            ],
        ];
    }

    public function registerSchedule($schedule)
    {
        $schedule->call(function () {
            LeaderboardLog::setWeeklyLeaderboard();
        })->weekly()->mondays()->at('00:00');

        $schedule->call(function () {
            LeaderboardLog::setMonthlyLeaderboard();
        })->cron('* * 1 * *');
    }
}
