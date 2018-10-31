<?php namespace Octobro\Gamify\Models;

use Model;

/**
 * Reward Model
 */
class Reward extends Model
{
    use \October\Rain\Database\Traits\Validation;

    public $rules = [
        'name'                  => 'required|between:4,30',
        'points'                => 'required|integer'
    ];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'octobro_gamify_rewards';

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
        'min_level' => 'Octobro\Gamify\Models\Level'
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [
        'image' => 'System\Models\File'
    ];
    public $attachMany = [];
}
