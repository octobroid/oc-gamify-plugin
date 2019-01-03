<?php namespace Octobro\Gamify\Models;

use Model;
use Db;
use Carbon\Carbon;
use RainLab\User\Models\User;
use Octobro\Gamify\Models\Reward;

/**
 * RedemptionLog Model
 */
class RedemptionLog extends Model
{
    /**
     * @var string The database table used by the model.
     */
    public $table = 'octobro_gamify_redemption_logs';

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
        'user'      => 'RainLab\User\Models\User',
        'reward'    => 'Octobro\Gamify\Models\Reward'
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    public static function redeem($user, $reward, $stock, $points)
    {
        // Update points to user
        User::find($user->id)->update([
            'spendable_points' => (int) ($user->spendable_points - $points),
            'spendable_points_updated_at' => date('Y-m-d H:i:s')
        ]);

        if (!is_null($reward->stock)) {
            $reward->stock -= $stock;
            $reward->save();
        }

        // Create redemption log
        $redeemLog = new self();
        $redeemLog->user_id = $user->id;
        $redeemLog->reward_id = $reward->id;
        $redeemLog->stock = $stock;
        $redeemLog->amount = $points;
        $redeemLog->save();
    }
}
