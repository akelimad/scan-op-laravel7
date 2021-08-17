<?php

/**
 * Bookmark Model Class
 * 
 * PHP version 5.4
 *
 * @category PHP
 * @package  Controller
 * @author   cyberziko <cyberziko@gmail.com>
 * @license  commercial http://getcyberworks.com/
 * @link     http://getcyberworks.com/
 */
class Bookmark extends Eloquent {

    public $fillable = ['user_id', 'manga_id', 'chapter_id', 'page_id'];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'bookmarks';

    static function myBookmarks($user_id, $status = "currently-reading") {
        $columns = [
            'bookmarks.id as id',
            'bookmarks.manga_id as manga_id',
            'bookmarks.chapter_id as chapter_id',
            'bookmarks.page_id as page_id',
            'bookmarks.created_at as created_at',
            'manga.name as manga_name',
            'manga.slug as manga_slug',
        ];
        
        return Bookmark::join('manga', 'manga.id', '=', 'bookmarks.manga_id')
            ->select($columns)
            ->where('bookmarks.user_id', '=', $user_id)
            ->where('bookmarks.status', 'like', "%$status%")
            ->orderBy('manga_id', 'asc')
            ->orderBy('chapter_id', 'asc')
            ->get();
    }

    static function bookmarkExist($user_id, $manga_id, $chapter_id) {
        return Bookmark::where('user_id', '=', $user_id)
            ->where('manga_id', '=', $manga_id)
            ->where('chapter_id', '=', $chapter_id)
            ->first();
    }
}
