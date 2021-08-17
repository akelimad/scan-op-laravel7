<?php

/**
 * ComicType Controller Class
 * 
 * PHP version 5.4
 *
 * @category PHP
 * @package  Controller
 * @author   cyberziko <cyberziko@gmail.com>
 * @license  commercial http://getcyberworks.com/
 * @link     http://getcyberworks.com/
 */
class ComicTypeController extends BaseController
{

    protected $type;

    /**
     * Constructor
     * 
     * @param Category $type current type
     */
    public function __construct(ComicType $type)
    {
        $this->type = $type;
    }

    /**
     * Type page
     * 
     * @return type
     */
    public function index()
    {
        $types = ComicType::all();

        return View::make(
            'admin.type.index', 
            ["types" => $types]
        );
    }

    /**
     * Save type
     * 
     * @return type
     */
    public function store()
    {
        $input = Input::all();

        if (!$this->type->fill($input)->isValid()) {
            return Redirect::back()
                ->withInput()->withErrors($this->type->errors);
        }

        $this->type->save();
        return Redirect::back()
            ->with('msgSuccess', Lang::get('messages.admin.comictype.create-success'));
    }

    /**
     * Edit page
     * 
     * @param type $id type id
     * 
     * @return type
     */
    public function edit($id)
    {
        $type = ComicType::find($id);
        $types = ComicType::all();

        return View::make(
            'admin.type.edit', 
            ['type' => $type, "types" => $types]
        );
    }

    /**
     * Update type
     * 
     * @param type $id type id
     * 
     * @return type
     */
    public function update($id)
    {
        $input = Input::all();
        $this->type = ComicType::find($id);

        if (!$this->type->fill($input)->isValid()) {
            return Redirect::back()
                ->withInput()->withErrors($this->type->errors);
        }

        $this->type->save();
        return Redirect::route('admin.comictype.index')
            ->with('msgSuccess', Lang::get('messages.admin.comictype.update-success'));
    }

    /**
     * Delete type
     * 
     * @param type $id type id
     * 
     * @return type
     */
    public function destroy($id)
    {
        $this->type = ComicType::find($id);

        $this->type->delete();

        return Redirect::route('admin.comictype.index')
            ->with('msgSuccess', Lang::get('messages.admin.comictype.delete-success'));
    }

}
