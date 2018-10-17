<?php namespace Octobro\Gamify\Models;

use Model;

/**
 * Voucher Model
 */
class Voucher extends Model
{
    use \October\Rain\Database\Traits\Validation;

    public $rules = [
        'points'                => 'required|integer',
        'quantity'              => 'required|integer'
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
    protected $fillable = [];

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
        $this->code = $this->generateCode();
        $this->used = 0;
    }

    private function generateCode()
    {
        return sprintf('JKW%s-%s-%s', $this->randomChar(6), $this->randomChar(5), $this->randomChar(4));
    }

    private function randomChar($length = 5)
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $string = '';

        for ($i = 0; $i < $length; $i++) {
            $string .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $string;
    }
}
