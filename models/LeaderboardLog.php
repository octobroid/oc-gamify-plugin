<?php namespace Octobro\Gamify\Models;

use Model;
use Carbon\Carbon;

/**
 * LeaderboardLog Model
 */
class LeaderboardLog extends Model
{
    /**
     * @var string The database table used by the model.
     */
    public $table = 'octobro_gamify_leaderboard_logs';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = ['type', 'date', 'data'];

    /**
     * @var array Jsoanble fields
     */
    protected $jsonable = ['data'];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    public function getPeriodAttribute()
    {
        if ($this->type == 'weekly') {
            return Carbon::parse($this->date)->startOfWeek()->format('d F, Y') . " - " . Carbon::parse($this->date)->endOfWeek()->format('d F, Y');
        } else {
            return Carbon::parse($this->date)->startOfMonth()->format('d F, Y') . " - " . Carbon::parse($this->date)->endOfMonth()->format('d F, Y');
        }
    }
}
