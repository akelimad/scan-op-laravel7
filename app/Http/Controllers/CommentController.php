<?php

class CommentController extends \BaseController {

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index($post_type, $post_id)
    {
        $columns = [
            'comments.id as id',
            'comments.created_at as created_at',
            'comments.comment as comment',
            'comments.parent_comment as parent_comment',
            'users.id as user_id',
            'users.avatar as avatar',
            'users.username as username',
        ];
        
        $comments = Comment::where('post_id', '=', $post_id)
            ->join('users', 'users.id', '=', 'comments.user_id')
            ->where('post_type', '=', $post_type)
            ->whereNull('parent_comment')
            ->select($columns)
            ->get();
        
        $replies = Comment::where('post_id', '=', $post_id)
            ->join('users', 'users.id', '=', 'comments.user_id')
            ->where('post_type', '=', $post_type)
            ->whereNotNull('parent_comment')
            ->select($columns)
            ->orderBy('parent_comment')
            ->orderBy('comments.created_at')
            ->get();
        
        return Response::json(['comments' => $comments, 'replies' => $replies]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $input = Input::all();
        
        $comment = new Comment();
        $comment->fill($input);
        
        if($input['parent_comment'] == '') {
            $comment->parent_comment = null;
        }
        
        $comment->save();
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
            //
    }


}
