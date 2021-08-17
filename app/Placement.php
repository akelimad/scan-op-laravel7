<?php

/**
 * Ad Model Class
 * 
 * PHP version 5.4
 *
 * @category PHP
 * @package  Controller
 * @author   cyberziko <cyberziko@gmail.com>
 * @license  commercial http://getcyberworks.com/
 * @link     http://getcyberworks.com/
 */
class Placement extends Eloquent
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'placement';

    /**
     * Ads
     * 
     * @return type
     */
    public function ads()
    {
        return $this->belongsToMany('Ad')->withPivot('placement');
    }
}
