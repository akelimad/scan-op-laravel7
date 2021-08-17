<?php

/**
 * Chapter Model Class
 * 
 * PHP version 5.4
 *
 * @category PHP
 * @package  Controller
 * @author   cyberziko <cyberziko@gmail.com>
 * @license  commercial http://getcyberworks.com/
 * @link     http://getcyberworks.com/
 */
class Chapter extends Eloquent
{

    public $fillable = ['name', 'slug', 'number', 'volume'];
    public static $rules = [
        //'name' => 'required', 
        'number' => 'required|unique:chapter,number,:id,id,manga_id,:mangaid', 
        'slug' => 'required|unique:chapter,slug,:id,id,manga_id,:mangaid'
    ];
    public $errors;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'chapter';

    /**
     * Pages of chapter
     * 
     * @return type
     */
    public function pages()
    {
        return $this->hasMany('Page')->orderBy('slug');
    }

    /**
     * Last page
     * 
     * @return int
     */
    public function lastPage()
    {
        if (count($this->pages())) {
            return $this->pages()->getResults()->last();
        } else {
            return 0;
        }
    }

    /**
     * Delete chapter
     * 
     * @return type
     */
    public function deleteMe()
    {
        // delete all related pages 
        $this->pages()->delete();

        // delete the chapter
        return parent::delete();
    }

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
     * manga chapter
     * 
     * @return type
     */
    public function manga()
    {
        return $this->belongsTo('Manga');
    }
    
    /**
     * Validate chapter
     * 
     * @param type $mangaid manga id
     * 
     * @return boolean
     */
    public function isValid($mangaid)
    {
        static::$rules = str_replace(':mangaid', $mangaid, static::$rules);
        static::$rules = str_replace(':id', $this->id, static::$rules);

        $validation = Validator::make($this->attributes, static::$rules);

        if ($validation->passes()) {
            return true;
        }

        $this->errors = $validation->messages();
        return false;
    }

}
