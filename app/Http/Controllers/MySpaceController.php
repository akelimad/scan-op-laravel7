<?php

/**
 * MySpace Controller Class
 * 
 * PHP version 5.4
 *
 * @category PHP
 * @package  Controller
 * @author   cyberziko <cyberziko@gmail.com>
 * @license  commercial http://getcyberworks.com/
 * @link     http://getcyberworks.com/
 */
class MySpaceController extends BaseController
{
	
    public function userProfil($username) {
        $user = User::where('username', $username)->first();
        $settings = Cache::get('options');
        $theme = Cache::get('theme');
        $variation = Cache::get('variation');
        
        return View::make(
            'front.themes.' . $theme . '.blocs.user.profil', 
            [
                "theme" => $theme,
                "variation" => $variation,
                "settings" => $settings,
                "user" => $user,
            ]
        );
    }
    
    public function editUserProfil($id) {
        if(Auth::user()->id != $id){
            return Redirect::route('front.index');
        } 
        $user = User::find($id);
        $settings = Cache::get('options');
        $theme = Cache::get('theme');
        $variation = Cache::get('variation');
        
        return View::make(
            'front.themes.' . $theme . '.blocs.user.profil_edit', 
            [
                "theme" => $theme,
                "variation" => $variation,
                "settings" => $settings,
                "user" => $user,
            ]
        );
    }
    
    public function saveUserProfil($id)
    {
        $input = Input::all();
        $user = User::find($id);

        $user->fill($input);
        $user->password_confirmation = $input['password'];
	
        if ($user->save() === false) {
            return Redirect::back()->withInput()->withErrors($user->errors);
        }

        $avatar = $input['cover'];
        $user->avatar = 1;

        if (str_contains($avatar, 'uploads/tmp/avatar/')) {
            $coverCreated = $this->createAvatar($avatar, $id);
            if (!$coverCreated) {
                $user->avatar = null;
            }
        } else if (is_null($avatar) || $avatar == "") {
            $user->avatar = null;
            $coverPath = 'uploads/users/' . $id . '/';

            // clear cover directory
            File::deleteDirectory($coverPath);
        }
        
        $user->save();
                
        return Redirect::back()
            ->with(
                'updateSuccess', 
                Lang::get('messages.admin.settings.update.profile-success')
            );
    }
    
    private function createAvatar($cover, $id)
    {
        $coverTmpPath = 'uploads/tmp/avatar/';
        $coverNewPath = 'uploads/users/' . $id . '/';
        
        if (!File::isDirectory($coverNewPath)) {
            File::makeDirectory($coverNewPath, 0755, true);
        }
        
        $cover_name = substr(strrchr($cover, "/"), count($cover));
        $coverCreated = File::move(
            $coverTmpPath . $cover_name, $coverNewPath . 'avatar.jpg'
        );

        return $coverCreated;
    }
}
