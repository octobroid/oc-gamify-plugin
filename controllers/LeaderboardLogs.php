<?php namespace Octobro\Gamify\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use Octobro\Gamify\Models\LeaderboardLog;

/**
 * Leaderboard Logs Back-end Controller
 */
class LeaderboardLogs extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Octobro.Gamify', 'gamify', 'leaderboardlogs');
    }

    public function preview($leaderboardId = null)
    {
        $this->vars['leaderboardLog'] = $leaderboardLog = LeaderboardLog::find($leaderboardId);

        return $this->asExtension('FormController')->preview($leaderboardId);
    }
}
