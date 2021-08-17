<?php
/**
 * User Management System Controller Class
 * 
 * PHP version 5.4
 *
 * @category PHP
 * @package  Controller
 * @author   cyberziko <cyberziko@gmail.com>
 * @license  commercial http://getcyberworks.com/
 * @link     http://getcyberworks.com/
 */
class ManageUserController extends BaseController
{

    protected $user;

    /**
     * Constructor
     * 
     * @param Option $settings current settings
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * General page
     * 
     * @return type
     */
    public function permissions()
    {
    	$permissions = Permission::all();
			
        return View::make(
            'admin.users.permissions',
            ['permissions' => $permissions]
        );        
    }


    /**
     * Load Manga page
     * 
     * @return manga page
     */
    public function index()
    {
    	$users = User::all();
        $roles = array('' => '-- Nothing --') + Role::lists('name', 'id');

        $options = Option::lists('value', 'key');
        $subscription = json_decode($options['site.subscription']);
        
        return View::make(
            'admin.users.user.index',
            [
                'users' => $users, 
                'subscription' => $subscription, 
                'roles' => $roles
            ]
        );
    }

    /**
     * Load Manga chapter create page
     *  
     * @return view
     */
    public function create()
    {
        $roles = Role::all();
		
        return View::make(
            'admin.users.user.create', 
            ['roles' => $roles]
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
        $user = new User();

        $user->fill($inputs);
        $user->password_confirmation = $inputs['password'];
		
        if($user->save() === false) {
            return Redirect::back()->withInput()->withErrors($user->errors);
        }

        $rolesList = $inputs['roles'];
        $roles = explode(",", $rolesList);

        if (count($roles) >= 1 && $roles[0] != "") {
            $user->roles()->sync($roles);
        }

        return Redirect::route('admin.user.index')
            ->with('createSuccess', Lang::get('messages.admin.users.user.create-success'));
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
    	$user = User::find($id);
        $roles = Role::all();

        if(count($user->roles)>0){
            foreach ($user->roles as $userRole) {
                foreach ($roles as $key=>$role) {
                    if($role->id === $userRole->id) {
                        unset($roles[$key]);
                    }
                }
            }
	}
		
        return View::make(
            'admin.users.user.edit',
            [
            	'user' => $user,
                'roles' => $roles,
            ]
        );
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
        $user = User::find($id);
		
        $user->fill($inputs);

        if($user->save() === false) {
            return Redirect::back()->withInput()->withErrors($user->errors);
        }
        
        $rolesList = $inputs['roles'];
        $roles = explode(",", $rolesList);

        if (count($roles) == 1 && $roles[0] == "") {
            $user->roles()->detach();
        } else {
            $user->roles()->sync($roles);
        }

        return Redirect::back()
            ->with('updateSuccess', Lang::get('messages.admin.users.user.update-success'));
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
        $user = User::find($id);

        $chapters = $user->chapters()->get();
        $mangas = $user->manga()->get();
        
        $admin = User::find(1);
        
        if(count($chapters)>0) {
            foreach ($chapters as $chapter){
                $admin->chapters()->save($chapter);
            }
        }
        
        if(count($mangas)>0) {
            foreach ($mangas as $manga){
                $admin->manga()->save($manga);
            }
        }

        $user->delete();

        return Redirect::route('admin.user.index');
    }
    
    public function saveSubscription(){
        $input = Input::all();
        unset($input['_token']);

        Option::findByKey("site.subscription")
            ->update(
                [
                    'value' => json_encode($input)
                ]
            );

        // clean cache
        Cache::forget('options');
        Cache::forget('theme');
        Cache::forget('variation');
        
        return Redirect::back()
            ->with(
                'updateSuccess', 
                Lang::get('messages.admin.settings.update.success'
            )
        );
    }
}
