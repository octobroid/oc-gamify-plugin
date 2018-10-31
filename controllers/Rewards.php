<?php namespace Octobro\Gamify\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

/**
 * Rewards Back-end Controller
 */
class Rewards extends Controller
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

        BackendMenu::setContext('Octobro.Gamify', 'gamify', 'rewards');
    }

    public function create($context = null)
    {
        $this->bodyClass = 'compact-container';
        return $this->asExtension('FormController')->create($context);
    }

    public function update($recordId = null, $context = null)
    {
        $this->bodyClass = 'compact-container';
        return $this->asExtension('FormController')->update($recordId, $context);
    }
}
