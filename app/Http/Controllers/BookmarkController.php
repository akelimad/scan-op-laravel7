<?php

namespace App\Http\Controllers;

use App\Bookmark;
use App\Chapter;
use App\Manga;
use App\Option;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;

/**
 * Bookmark Controller Class
 * 
 * PHP version 5.4
 *
 * @category PHP
 * @package  Controller
 * @author   cyberziko <cyberziko@gmail.com>
 * @license  commercial http://getcyberworks.com/
 * @link     http://getcyberworks.com/
 */
class BookmarkController extends BaseController
{

    public function index()
    {
        $bookmarks0 = Bookmark::myBookmarks(Auth::user()->id);
        $settings = Option::lists('value', 'key');

        $bookmarks = [];
        foreach ($bookmarks0 as $bookmark) {
            if (!array_key_exists($bookmark->manga_id, $bookmarks)) {
                $chapters = [
                    [
                        'id' => $bookmark->id,
                        'chapter' => Chapter::find($bookmark->chapter_id),
                        'page_id' => $bookmark->page_id,
                        'created_at' => $bookmark->created_at,
                    ]
                ];
                
                $bookmarks[$bookmark->manga_id] =
                    [
                        'id' => $bookmark->id,
                        'manga_id' => $bookmark->manga_id,
                        'manga_slug' => $bookmark->manga_slug,
                        'manga_name' => $bookmark->manga_name,
                        'created_at' => $bookmark->created_at,
                        'last_chapter' => Manga::find($bookmark->manga_id)->lastChapter(),
                        'chapters' => $chapters
                    ];
            } else {
                array_push($bookmarks[$bookmark->manga_id]['chapters'], [
                    'id' => $bookmark->id,
                    'chapter' => Chapter::find($bookmark->chapter_id),
                    'page_id' => $bookmark->page_id,
                    'created_at' => $bookmark->created_at,
                ]);
            }
        }

        // bootswatch variation
        $theme = $settings['site.theme'];
        $variation = "";
        if (strpos($theme, 'default') !== false) {
            $tab = explode('.', $theme);
            $theme = $tab[0];
            $variation = $tab[1];
        }
        
        Session::put('theme', $theme);
        Session::put('variation', $variation);
        Session::put('settings', $settings);
        
        return view(
            'front.themes.' . $theme . '.blocs.bookmark', 
            [
                "theme" => $theme,
                "variation" => $variation,
                "settings" => $settings,
                "bookmarks" => $bookmarks,
            ]
        );
    }

    public function loadTabData()
    {
        $status = filter_input(INPUT_GET, 'status');
        
        $bookmarks0 = Bookmark::myBookmarks(Auth::user()->id, $status);

        $bookmarks = [];
        foreach ($bookmarks0 as $bookmark) {
            if (!array_key_exists($bookmark->manga_id, $bookmarks)) {
                $chapters = [
                    [
                        'id' => $bookmark->id,
                        'chapter' => Chapter::find($bookmark->chapter_id),
                        'page_id' => $bookmark->page_id,
                        'created_at' => $bookmark->created_at,
                    ]
                ];
                
                $bookmarks[$bookmark->manga_id] =
                    [
                        'id' => $bookmark->id,
                        'manga_id' => $bookmark->manga_id,
                        'manga_slug' => $bookmark->manga_slug,
                        'manga_name' => $bookmark->manga_name,
                        'created_at' => $bookmark->created_at,
                        'last_chapter' => Manga::find($bookmark->manga_id)->lastChapter(),
                        'chapters' => $chapters
                    ];
            } else {
                array_push($bookmarks[$bookmark->manga_id]['chapters'], [
                    'id' => $bookmark->id,
                    'chapter' => Chapter::find($bookmark->chapter_id),
                    'page_id' => $bookmark->page_id,
                    'created_at' => $bookmark->created_at,
                ]);
            }
        }

        return view(
            'front.themes.' . Session::get('theme') . '.blocs.bookmark_frag', 
            [
                "bookmarks" => $bookmarks,
            ]
        )->render();
    }
    
    public function store()
    {
        $manga_id = filter_input(INPUT_POST, 'manga_id');
        $chapter_id = filter_input(INPUT_POST, 'chapter_id');
        $page_slug = filter_input(INPUT_POST, 'page_slug');
        $user_id = Auth::user()->id;

        $bookmark = Bookmark::bookmarkExist(Auth::user()->id, $manga_id, $chapter_id);
        
        if(is_null($bookmark)){
            $bookmark = new Bookmark();
            $bookmark->user_id = $user_id;
            $bookmark->manga_id = $manga_id;
            $bookmark->chapter_id = isset($chapter_id) ? $chapter_id : 0;
            $bookmark->page_id = isset($page_slug) ? $page_slug : 0;
            
            $tmp = Bookmark::where('user_id', '=', $user_id)
                ->where('manga_id', '=', $manga_id)
                ->first();
            if(is_null($tmp)) {
                $bookmark->status = "currently-reading";
            } else {
                $bookmark->status = $tmp->status;
            }
                
        } else {
            $bookmark->page_id = isset($page_slug) ? $page_slug : 0;
        }
        
        $bookmark->save();

        return Response::json(
            [
                'status' => 'ok'
            ]
        );
    }
    
    public function destroy($id)
    {
        $rootBookmark = filter_input(INPUT_POST, 'rootBookmark');
        if($rootBookmark == 'true'){
            Bookmark::where('user_id', '=', Auth::user()->id)
                ->where('manga_id', '=', $id)
                ->delete();
        } else {
            Bookmark::find($id)->delete();
        }

        return Redirect::back();
    }
    
    public function changeStatus()
    {
        $ids = filter_input(INPUT_POST, 'ids');
        $status = filter_input(INPUT_POST, 'status');

        $bookmarks = Bookmark::where('user_id', '=', Auth::user()->id)
            ->whereIn('manga_id', explode(',', $ids))
            ->get();
        
        foreach ($bookmarks as $bookmark){
            $bookmark->status = $status;
            $bookmark->save();
        }

        return Response::json(
            [
                'status' => 'ok'
            ]
        );
    }
    
    public function deleteChecked()
    {
        $ids = filter_input(INPUT_POST, 'ids');

        Bookmark::where('user_id', '=', Auth::user()->id)
            ->whereIn('manga_id', explode(',', $ids))
            ->delete();
        
        return Response::json(
            [
                'status' => 'ok'
            ]
        );
    }
    
    public function saveNotificationOption()
    {
        $notify = filter_input(INPUT_POST, 'bookmarks-notify');
        $user = User::find(Auth::user()->id);

        if($notify == 'true') {
                $user->notify = 1;
        } else {
                $user->notify = 0;
        }

        $user->save();
    }
}
