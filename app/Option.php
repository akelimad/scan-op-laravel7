<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Option Model Class
 * 
 * PHP version 5.4
 *
 * @category PHP
 * @package  Controller
 * @author   cyberziko <cyberziko@gmail.com>
 * @license  commercial http://getcyberworks.com/
 * @link     http://getcyberworks.com/
 */
class Option extends Model
{

    public $fillable = ['key', 'value'];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'options';

    /**
     * Option by key
     * 
     * @param type $query query
     * @param type $key   key
     * 
     * @return type
     */
    public function scopeFindByKey($query, $key)
    {
        return $query->whereKey($key);
    }

    public static function get($key, $default = "")
    {
        $option = Option::where("key", $key)->first();

        return $option != null ? $option->value : $default;
    }

}
