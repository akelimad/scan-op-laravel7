<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\BaseController;
use App\Option;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

/**
 * Settings Controller Class
 * 
 * PHP version 5.4
 *
 * @category PHP
 * @package  Controller
 * @author   cyberziko <cyberziko@gmail.com>
 * @license  commercial http://getcyberworks.com/
 * @link     http://getcyberworks.com/
 */
class SettingsController extends BaseController
{

    protected $settings;

    /**
     * Constructor
     * 
     * @param Option $settings current settings
     */
    public function __construct(Option $settings)
    {
        $this->middleware('auth');
        $this->settings = $settings;
    }

    /**
     * General page
     * 
     * @return type
     */
    public function general()
    {
        $options = Option::pluck('value', 'key');

        $langRootDirectory = base_path() . "/resources/lang";
        $languagesDirectories = File::directories($langRootDirectory);

        $languages = array();
        foreach ($languagesDirectories as $directory) {
            $language = substr(
                $directory, 
                strrpos($directory, DIRECTORY_SEPARATOR) + 1
            );
            
            $languages[$language] = $language;
        }
	
        $pagination = json_decode($options['site.pagination']);
        $menus = json_decode($options['site.menu']);
        $comment = json_decode($options['site.comment']);
        
        return view('admin.settings.general',
            [
                "options" => $options, 
                "languages" => $languages,
                "pagination" => $pagination,
                "menus" => $menus,
                "comment" => $comment
            ]
        );        
    }

    /**
     * Save General settings
     * 
     * @return type
     */
    public function saveGeneral(Request $request)
    {
        $input = $request->all();

        foreach ($input as $key => $value) {
            $option = str_replace('_', '.', $key);

            if($option == "site.pagination" || $option == "site.menu" || $option == "site.comment") {
                $value = json_encode($value);
            }
            
            Option::where("key", $option)->update(['value' => $value]);
        }

        Session::put("sitename", isset($input['site_name']) ? $input['site_name'] : "");
        
        // clean cache
        Cache::forget('options');
        
        return Redirect::back()->with(
                'updateSuccess',
                Lang::get('messages.admin.settings.update.success')
            );
    }

    /**
     * SEO page
     * 
     * @return type
     */
    public function seo()
    {
        $options = Option::pluck('value', 'key')->toArray();
        $advanced = json_decode($options['seo.advanced']);
        
        return view('admin.settings.seo',
                [
                    "options" => $options,
                    "advanced" => $advanced,
                ]
            );
    }

    /**
     * Save SEO settings
     * 
     * @return type
     */
    public function saveSeo(Request $request)
    {
        $input = $request->all();

        foreach ($input as $key => $value) {
            $option = str_replace('_', '.', $key);

            if($option == "seo.advanced") {
                $value = json_encode($value);
            }
            if ($value == "") $value = "";
            Option::where("key", $option)->update(['value' => $value]);
        }

        // clean cache
        Cache::forget('options');
        
        return Redirect::back()->with(
                'updateSuccess', 
                Lang::get('messages.admin.settings.update.success')
            );
    }

    /**
     * Profile page
     * 
     * @return type
     */
    public function profile()
    {
        $user = User::find(Auth::user()->id);

        return view('admin.settings.profile', ["user" => $user]);
    }

    /**
     * Save Profile settings
     * 
     * @return type
     */
    public function saveProfile(Request $request)
    {
        $input = $request->all();
        $user = User::find(Auth::user()->id);
        $password = $input['password'];
        unset($input['password']);
        $user->fill($input);
        if ($password != "") $user->password = Hash::make($password);
		
        if ($user->save() === false) {
            return Redirect::back()->withInput()->withErrors($user->errors);
        }

        return Redirect::back()
            ->with('updateSuccess', Lang::get('messages.admin.settings.update.profile-success'));
    }

    /**
     * Theme page
     * 
     * @return type
     */
    public function theme()
    {
        $options = Option::pluck('value', 'key')->toArray();
        $themeRootDirectory = base_path() . "/resources/views/front/themes";

        $themesDirectories = File::directories($themeRootDirectory);

        $themes = array();
        foreach ($themesDirectories as $directory) {
            $themeName = substr($directory, strrpos($directory, DIRECTORY_SEPARATOR) + 1);

            if ($themeName != 'default') {
                $themes[$themeName] = ucfirst($themeName);
            }
        }

        $bootswatch = [
            'default.cerulean' => 'Cerulean',
            'default.cosmo' => 'Cosmo',
            'default.flatly' => 'Flatly',
            'default.journal' => 'Journal',
            'default.lumen' => 'Lumen',
            'default.paper' => 'Paper',
            'default.readable' => 'Readable',
            'default.sandstone' => 'Sandstone',
            'default.simplex' => 'Simplex',
            'default.spacelab' => 'Spacelab',
            'default.united' => 'United',
            'default.yeti' => 'Yeti'
        ];
        $themes['default - color variation'] = $bootswatch;

        return view('admin.settings.theme', ["options" => $options, "themes" => $themes]);
    }

    /**
     * Save Theme settings
     * 
     * @return type
     */
    public function saveTheme(Request $request)
    {
        $input = $request->all();

        foreach ($input as $key => $value) {
            $option = str_replace('_', '.', $key);

            Option::where('key', $option)->update(['value' => $value]);
        }

        // clean cache
        Cache::forget('options');
        Cache::forget('theme');
        Cache::forget('variation');
        
        return Redirect::back()
            ->with(
                'updateSuccess', 
                Lang::get('messages.admin.settings.update.success')
            );
    }
    
    public function widgets()
    {
        $options = Option::pluck('value', 'key')->toArray();
        $widgets = json_decode($options['site.widgets']);
        
        return view(
                'admin.settings.widgets', 
                [
                    "widgets" => $widgets,
                ]
            );
    }

    public function saveWidgets(Request $request)
    {
        $input = $request->all();

        foreach ($input as $key => $value) {
            $option = str_replace('_', '.', $key);

            if($option == "site.widgets") {
                $value = json_encode($value);
            }

            Option::where('key', $option)->update(['value' => $value]);
        }

        // clean cache
        Cache::forget('options');
        
        return Redirect::back()
            ->with(
                'updateSuccess', 
                Lang::get('messages.admin.settings.update.success')
            );
    }
    
    public function cache()
    {
        $options = Option::pluck('value', 'key');
        $cache = json_decode($options['site.cache']);
        
        return view('admin.settings.cache', [
            "cache" => $cache,
        ]);
    }

    public function saveCache(Request $request)
    {
        $input = $request->except('_token');
        foreach ($input as $key => $value) {
            $option = str_replace('_', '.', $key);

            if($option == "site.cache") {
                $value = json_encode($value);
            }

            Option::where('key', $option)->update(['value' => $value]);
        }

        // clean cache
        Cache::forget('options');
        
        return Redirect::back()->with('updateSuccess', 
            Lang::get('messages.admin.settings.update.success')
        );
    }
    
    public function clearCache()
    {
        // clean cache
        Cache::flush();
        
        return Redirect::route('admin.index')
            ->with('updateSuccess', Lang::get('messages.admin.settings.cache.cleared'));
    }
    
    public function clearDownloads()
    {
        $destinationPath = 'uploads/tmp/downloads/';

        if (File::isDirectory($destinationPath)) {
            File::deleteDirectory($destinationPath, true);
            return Redirect::route('admin.index')->with('updateSuccess', Lang::get('messages.admin.settings.downloads.cleared'));
        } else {
            return Redirect::back();
        }
    }
}
