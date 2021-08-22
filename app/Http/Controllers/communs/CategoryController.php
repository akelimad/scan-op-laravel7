<?php

namespace App\Http\Controllers\Communs;

use App\Category;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;

/**
 * Category Controller Class
 * 
 * PHP version 5.4
 *
 * @category PHP
 * @package  Controller
 * @author   cyberziko <cyberziko@gmail.com>
 * @license  commercial http://getcyberworks.com/
 * @link     http://getcyberworks.com/
 */
class CategoryController extends BaseController
{

    protected $category;

    /**
     * Constructor
     * 
     * @param Category $category current category
     */
    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    /**
     * Category page
     * 
     * @return type
     */
    public function index()
    {
        $categories = Category::all();

        return view(
            'admin.category.index', 
            ["categories" => $categories]
        );
    }

    /**
     * Save category
     * 
     * @return type
     */
    public function store()
    {
        $input = request()->all();

        if (!$this->category->fill($input)->isValid()) {
            return Redirect::back()
                ->withInput()->withErrors($this->category->errors);
        }

        $this->category->save();
        return Redirect::back()
            ->with('msgSuccess', Lang::get('messages.admin.category.create-success'));
    }

    /**
     * Edit page
     * 
     * @param type $id category id
     * 
     * @return type
     */
    public function edit($id)
    {
        $category = Category::find($id);
        $categories = Category::all();

        return view(
            'admin.category.edit', 
            ['category' => $category, "categories" => $categories]
        );
    }

    /**
     * Update category
     * 
     * @param type $id category id
     * 
     * @return type
     */
    public function update($id)
    {
        $input = request()->all();
        $this->category = Category::find($id);

        if (!$this->category->fill($input)->isValid()) {
            return Redirect::back()
                ->withInput()->withErrors($this->category->errors);
        }

        $this->category->save();
        return Redirect::route('category.index')
            ->with('msgSuccess', Lang::get('messages.admin.category.update-success'));
    }

    /**
     * Delete category
     * 
     * @param type $id category id
     * 
     * @return type
     */
    public function destroy($id)
    {
        $this->category = Category::find($id);

        $this->category->delete();

        return Redirect::route('category.index')
            ->with('msgSuccess', Lang::get('messages.admin.category.delete-success'));
    }

}
