<?php

/**
 * Comment Model Class
 * 
 * PHP version 5.4
 *
 * @category PHP
 * @package  Controller
 * @author   cyberziko <cyberziko@gmail.com>
 * @license  commercial http://getcyberworks.com/
 * @link     http://getcyberworks.com/
 */
class Comment extends Eloquent
{

    public $fillable = ['comment', 'user_id', 'post_id', 'post_type', 'parent_comment'];
    public static $rules = [
        'comment' => 'required', 
    ];
    public $errors;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'comments';

    /**
     * chapter owner
     * 
     * @return type
     */
    public function user()
    {
        return $this->belongsTo('User');
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
        $validation = Validator::make($this->attributes, static::$rules);

        if ($validation->passes()) {
            return true;
        }

        $this->errors = $validation->messages();
        return false;
    }

}
