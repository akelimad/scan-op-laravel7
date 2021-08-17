<?php

/**
 * Role Controller Class
 * 
 * PHP version 5.4
 *
 * @category PHP
 * @package  Controller
 * @author   cyberziko <cyberziko@gmail.com>
 * @license  commercial http://getcyberworks.com/
 * @link     http://getcyberworks.com/
 */
class RoleController extends BaseController
{

    protected $role;

    /**
     * Constructor
     * 
     * @param Chapter $chapter current chapter
     */
    public function __construct(Role $role)
    {
        $this->role = $role;
    }

    /**
     * Load Manga page
     * 
     * @return manga page
     */
    public function index()
    {
    	$roles = Role::all();
		
        return View::make(
            'admin.users.role.index',
            ['roles' => $roles]
        );
    }

    /**
     * Load Manga chapter create page
     *  
     * @return view
     */
    public function create()
    {
    	$permissions = Permission::all();
		
        return View::make(
            'admin.users.role.create', 
            ['permissions' => $permissions]
        );
    }

    /**
     * Edit page
     * 
     * @param type $id identifier
     * 
     * @return view
     */
    public function edit($id)
    {
    	$role = Role::find($id);
        $permissions = Permission::all();

        if(count($role->perms)>0){
            foreach ($role->perms as $perm) {
                foreach ($permissions as $key=>$permission) {
                    if($permission->id === $perm->id) {
                        unset($permissions[$key]);
                    }
                }
            }
	}
				
        return View::make(
            'admin.users.role.edit',
            [
            	'permissions' => $permissions,
                'role' => $role,
            ]
        );
    }
	
    /**
     * Save the new chapter
     * 
     * @return detail page
     */
    public function store()
    {
        $inputs = Input::all();
        $role = new Role();

        if (!$role->fill($inputs)->isValid()) {
            return Redirect::back()->withInput()->withErrors($role->errors);
        }
		
        $role->save();

        $permsList = $inputs['perms'];
        $perms = explode(",", $permsList);

        if (count($perms)>=1 && $perms[0] != "") {
            $role->perms()->sync($perms);	 
        }

        return Redirect::route('admin.role.index')
            ->with('createSuccess', Lang::get('messages.admin.users.role.create-success'));;
    }

    /**
     * Load Manga chapter edit page
     * 
     * @param type $mangaId   manga identifier
     * @param type $chapterId chapter identifier
     * 
     * @return edit page with message
     */
    public function update($id)
    {
        $inputs = Input::all();
        $role = Role::find($id);

        if (!$role->fill($inputs)->isValid()) {
            return Redirect::back()->withInput()->withErrors($role->errors);
        }

        $permsList = $inputs['perms'];
        $perms = explode(",", $permsList);

        if (count($perms)==1 && $perms[0] == "") {
            $role->perms()->detach();	 
        } else {
            $role->perms()->sync($perms);	 
        }

        $role->save();

        return Redirect::back()
            ->with('updateSuccess', Lang::get('messages.admin.users.role.update-success'));
    }

    /**
     * Delete chapter
     * 
     * @param type $mangaId   manga identifier
     * @param type $chapterId chapter identifier
     * 
     * @return view
     */
    public function destroy($id)
    {
        $role = Role::find($id);

        $role->delete();

        return Redirect::route('admin.role.index');
    }

}
