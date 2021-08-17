<?php
/**
 * Admin Dashborad Controller Class
 * 
 * PHP version 5.4
 *
 * @category PHP
 * @package  Controller
 * @author   cyberziko <cyberziko@gmail.com>
 * @license  commercial http://getcyberworks.com/
 * @link     http://getcyberworks.com/
 */
class DashboardController extends BaseController
{

    /**
     * Load dashboard page
     * 
     * @return view
     */
    public function index()
    {
        $hotmanga = Manga::whereNotNull('hot')
            ->orderBy('created_at', 'desc')
            ->get();
        $mangas = Manga::orderBy('created_at', 'desc')->take(10)->get();
        $chapters = Chapter::join('manga', 'manga.id', '=', 'manga_id')
                        ->join('users', 'users.id', '=', 'chapter.user_id')
                        ->select(
                            [
                                'manga.name as manga_name',
                                'chapter.number',
                                'chapter.name',
                                'chapter.created_at',
                                'chapter.manga_id',
                                'chapter.id',
                                'users.username',
                            ]
                        )
                    ->orderBy('created_at', 'desc')->take(10)->get();

        $sitename = Option::findByKey('site.name')->first();
        Session::put("sitename", $sitename['value']);

        return View::make(
            'admin.index', 
            [
                "hotmanga" => $hotmanga,
                "mangas" => $mangas,
                "chapters" => $chapters
            ]
        );
    }
}
