<?php

use Zizaco\Entrust\EntrustRole;

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
class Role extends EntrustRole
{
	public $fillable = ['name'];
    public static $rules = ['name' => 'required|unique:roles,name,:id'];
	public $errors;
	
	/**
     * Validate chapter
     * 
     * @param type $mangaid manga id
     * 
     * @return boolean
     */
    public function isValid()
    {
    	static::$rules = str_replace(':id', $this->id, static::$rules);
		
        $validation = Validator::make($this->attributes, static::$rules);

        if ($validation->passes()) {
            return true;
        }

        $this->errors = $validation->messages();
        return false;
    }
}
