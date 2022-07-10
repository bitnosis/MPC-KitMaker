<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trade extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id','entered_trade','exited_trade','amount','enter_price','exit_price','symbol','profit_loss_dollar','profit_loss','commission','direction','market_type','price_type','transaction_id','notes','image_id','status'
    ];

    /**
     *  User
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    /**
     *  Image
     */
    public function image()
    {
        return $this->belongsTo('App\Models\Image', 'image_id');
    }

    public static function getSymbolCommission($symbol){
        return 1.99;
    }

    public static function getSymbolTickValue($symbol, $value){
        switch($symbol){
            case "nqh2";
                $v = $value/0.25;
                return $v*5.00;
            break;
        }


        return 5.00;
    }



}
