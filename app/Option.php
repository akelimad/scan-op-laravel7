<?php

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
class Option extends Eloquent
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

}
