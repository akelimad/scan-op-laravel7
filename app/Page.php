<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Page Model Class
 * 
 * PHP version 5.4
 *
 * @category PHP
 * @package  Controller
 * @author   cyberziko <cyberziko@gmail.com>
 * @license  commercial http://getcyberworks.com/
 * @link     http://getcyberworks.com/
 */
class Page extends Model
{

    public $fillable = ['image', 'slug'];
    public $errors;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'page';

}
