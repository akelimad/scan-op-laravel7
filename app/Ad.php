<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
class Ad extends Model
{

    public $fillable = ['bloc_id', 'code'];
    public $errors;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'ad';

}
