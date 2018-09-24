<?php namespace Octobro\Gamify;

use Backend;
use System\Classes\PluginBase;

/**
 * Gamify Plugin Information File
 */
class Plugin extends PluginBase
{
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
     * Register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot()
    {

    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
        return []; // Remove this line to activate

        return [
            'Octobro\Gamify\Components\MyComponent' => 'myComponent',
        ];
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return []; // Remove this line to activate

        return [
            'octobro.gamify.some_permission' => [
                'tab' => 'Gamify',
                'label' => 'Some permission'
            ],
        ];
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
                'url'         => Backend::url('octobro/gamify/leaderboardlog'),
                'icon'        => 'icon-gamepad',
                'permissions' => ['octobro.gamify.*'],
                'order'       => 500,
                'sideMenu' => [
                    'leaderboard' => [
                        'label'       => 'Leaderboard',
                        'icon'        => 'icon-certificate',
                        'url'         => Backend::url('octobro/gamify/leaderboardlog'),
                        'permissions' => ['opentrip.tours.*']
                    ],
                    'levels' => [
                        'label'       => 'Levels',
                        'icon'        => 'icon-level-up',
                        'url'         => Backend::url('octobro/gamify/levels'),
                        'permissions' => ['opentrip.tours.*']
                    ],
                    'missions' => [
                        'label'       => 'Missions',
                        'icon'        => 'icon-check',
                        'url'         => Backend::url('octobro/gamify/missions'),
                        'permissions' => ['opentrip.tours.*']
                    ],
                ]
            ],
        ];
    }
}
