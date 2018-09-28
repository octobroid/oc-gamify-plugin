<?php namespace Octobro\Gamify\Models;

use Model;

/**
 * LevelLog Model
 */
class LevelLog extends Model
{
    /**
     * @var string The database table used by the model.
     */
    public $table = 'octobro_gamify_level_logs';

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
        'user'           => 'RainLab\User\Models\User',
        'previous_level' => 'Octobro\Gamify\Models\Level',
        'updated_level'  => 'Octobro\Gamify\Models\Level',
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];
}
