<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Post Model Class
 * 
 * PHP version 5.4
 *
 * @category PHP
 * @package  Controller
 * @author   cyberziko <cyberziko@gmail.com>
 * @license  commercial http://getcyberworks.com/
 * @link     http://getcyberworks.com/
 */
class Post extends Model
{

    public $fillable = ['title', 'slug', 'content', 'status', 'manga_id', 'keywords'];
    public static $rules = [
        'title' => 'required', 
    ];
    public $errors;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'posts';

    /**
     * chapter owner
     * 
     * @return type
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
	
    public function manga()
    {
        return $this->belongsTo(Manga::class);
    }
	
    /**
     * Validate chapter
     * 
     * @param type $mangaid manga id
     * 
     * @return boolean
     */
    public function isValid()
    {
        $validation = \Validator::make($this->attributes, static::$rules);

        if ($validation->passes()) {
            return true;
        }

        $this->errors = $validation->messages();
        return false;
    }

}
