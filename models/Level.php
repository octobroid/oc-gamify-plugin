<?php namespace Octobro\Gamify\Models;

use Model;

/**
 * Level Model
 */
class Level extends Model
{
    use \October\Rain\Database\Traits\Validation;

    public $rules = [
        'name'                  => 'required|between:4,30',
        'min_points'            => 'required|integer'
    ];
    /**
     * @var string The database table used by the model.
     */
    public $table = 'octobro_gamify_levels';

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
        'next_level' => 'Octobro\Gamify\Models\Level',
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [
        'icon' => 'System\Models\File'
    ];
    public $attachMany = [
        'images' => 'System\Models\File'
    ];
}
