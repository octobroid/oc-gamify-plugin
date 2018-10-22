<?php namespace Octobro\Gamify\Models;

use Model;

/**
 * Voucher Model
 */
class Voucher extends Model
{
    use \October\Rain\Database\Traits\Validation;

    public $rules = [
        'points'                => 'required|integer'
    ];
    /**
     * @var string The database table used by the model.
     */
    public $table = 'octobro_gamify_vouchers';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = ['points', 'quantity', 'used'];

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

    public function beforeCreate()
    {
        $this->used = 0;
    }

    public function setCodeAttribute($value)
    {
        $value = trim($value);

        $this->attributes['code'] = $value ? strtoupper($value) : $this->generateCode();
    }

    private function generateCode($length = 6)
    {
        $characters = '1234567890ABCDEFGHJKLMNPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $code = '';

        for ($i = 0; $i < $length; ++$i) {
            $code .= $characters[rand(0, $charactersLength - 1)];
        }

        while (static::whereCode($code)->count()) {
            $code = $this->generateCode();
        }

        return $code;
    }

}
