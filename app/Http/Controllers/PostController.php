<?php

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
        
        return View::make('admin.posts.index', [
            'posts' => $posts,
        ]);
    }

    public function create()
    {
    	$categories = array(0 => 'General') + Manga::lists('name', 'id');
        return View::make(
            'admin.posts.create', ['categories' => $categories]
        );
    }

    /**
     * Create post
     * 
     * @return view
     */
    public function store()
    {
        $input = Input::all();

        if (!$this->post->fill($input)->isValid()) {
            return Redirect::back()
                ->withInput()
                ->withErrors($this->post->errors);
        }
        
        $this->post->slug = Str::slug($this->post->title);
        $this->post->user_id = Auth::user()->id;
        $this->post->save();

        return Redirect::route('admin.posts.index');
        
    }

    public function edit($id)
    {
        $post = Post::find($id);
        $categories = array(0 => 'General') + Manga::lists('name', 'id');
		
        return View::make('admin.posts.edit', ['post' => $post, 'categories' => $categories]);
    }
    
    public function update($id)
    {
        $input = Input::all();
        $this->post = Post::find($id);

        if (!$this->post->fill($input)->isValid()) {
            return Redirect::back()
                ->withInput()
                ->withErrors($this->post->errors);
        }

        $this->post->slug = Str::slug($this->post->title);
		         
        $this->post->save();

        return Redirect::route('admin.posts.index');
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
        $post->delete();

        return Redirect::route('admin.posts.index');
    }
	
	/**
     * CKeditor upload image
     */
    public function uploadImage()
    {
        $file = Input::file('upload');
        $uploadDestination = public_path() . '/uploads/posts/'.Auth::user()->team_id;
        $filename = preg_replace('/\s+/', '', $file->getClientOriginalName());
        $file->move($uploadDestination, $filename);

        $CKEditorFuncNum = Input::get('CKEditorFuncNum');
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

        return View::make('admin.posts.imageuploader.imgbrowser',[
                'UploadTeamPath' => $UploadTeamPath,
                'CKEditorFuncNum' => $CKEditorFuncNum
        ]);
    }
    
    /**
     * CKeditor delete uploaded image
     */
    public function deletePostImage()
    {
        $imgSrc = Input::get('imgSrc');
		
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

