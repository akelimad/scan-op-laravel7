<?php

namespace App\Http\Controllers;

use App\Manga;
use App\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

/**
 * Post Controller Class - manga-cat
 * 
 * PHP version 5.4
 *
 * @category PHP
 * @package  Controller
 * @author   cyberziko <cyberziko@gmail.com>
 * @license  commercial http://getcyberworks.com/
 * @link     http://getcyberworks.com/
 */
class PostController extends BaseController
{

    protected $post;
	
    /**
     * Constructor
     * 
     * @param Post $post current post
     */
    public function __construct(Post $post)
    {
        $this->middleware("auth");
        $this->post = $post;
    }
    
    public function index() {
        //$powerUser = User::isPowerUser();
        //$posts = null;
        //if($powerUser) {
            $posts = Post::paginate(15);
        /*} else {
            $ids = array();
            foreach(Auth::user()->team->users as $user) {
                array_push($ids, $user->id);
            }
            $posts = Post::whereIn('user_id', $ids)->paginate(15);
        }*/
        
        return view('admin.posts.index', [
            'posts' => $posts,
        ]);
    }

    public function create()
    {
    	$categories = array(0 => 'General') + Manga::pluck('name', 'id')->toArray();
        return view('admin.posts.create', ['categories' => $categories]
        );
    }

    /**
     * Create post
     * 
     * @return view
     */
    public function store()
    {
        $input = request()->all();

        if (!$this->post->fill($input)->isValid()) {
            return Redirect::back()
                ->withInput()
                ->withErrors($this->post->errors);
        }
        
        $this->post->slug = Str::slug($this->post->title);
        $this->post->user_id = Auth::user()->id;
        $this->post->save();

        return Redirect::route('posts.index');
        
    }

    public function edit($id)
    {
        $post = Post::find($id);
        $categories = array(0 => 'General') + Manga::pluck('name', 'id')->toArray();
		
        return view('admin.posts.edit', ['post' => $post, 'categories' => $categories]);
    }
    
    public function update($id)
    {
        $input = request()->all();
        $this->post = Post::find($id);

        if (!$this->post->fill($input)->isValid()) {
            return Redirect::back()
                ->withInput()
                ->withErrors($this->post->errors);
        }

        $this->post->slug = Str::slug($this->post->title);
		         
        $this->post->save();

        return Redirect::route('posts.index');
    }
    
    /**
     * Delete post
     * 
     * @param type $id post id
     * 
     * @return view
     */
    public function destroy($id)
    {
        $post = Post::find($id);
        if ($post) $post->delete();

        return Redirect::route('posts.index');
    }
	
	/**
     * CKeditor upload image
     */
    public function uploadImage(Request $request)
    {
        $file = $request->file('upload');

        $uploadDestination = public_path() . '/uploads/posts/'.Auth::user()->team_id;
        $filename = preg_replace('/\s+/', '', $file->getClientOriginalName());
        $file->move($uploadDestination, $filename);

        $CKEditorFuncNum = request()->get('CKEditorFuncNum');
        return Redirect::route('admin.posts.browseImage', ['CKEditorFuncNum'=>$CKEditorFuncNum]);
    }
	
	/**
     * CKeditor upload image
     */
    public function browseImage()
    {
        $CKEditorFuncNum = $_GET['CKEditorFuncNum'];
		
        $UploadTeamPath = 'uploads/posts/'.Auth::user()->team_id.'/';

        if (!File::isDirectory($UploadTeamPath)) {
            File::makeDirectory($UploadTeamPath, 0777, true);
        }

        return view('admin.posts.imageuploader.imgbrowser',[
                'UploadTeamPath' => $UploadTeamPath,
                'CKEditorFuncNum' => $CKEditorFuncNum
        ]);
    }
    
    /**
     * CKeditor delete uploaded image
     */
    public function deletePostImage()
    {
        $imgSrc = request()->get('imgSrc');

        if (File::exists($imgSrc)) {
            File::delete($imgSrc);
        }
		
        return Response::json(
            [
                'status' => 'ok'
            ]
        );
    }
}

