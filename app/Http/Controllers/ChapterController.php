<?php

/**
 * Chapter Controller Class
 * 
 * PHP version 5.4
 *
 * @category PHP
 * @package  Controller
 * @author   cyberziko <cyberziko@gmail.com>
 * @license  commercial http://getcyberworks.com/
 * @link     http://getcyberworks.com/
 */
class ChapterController extends BaseController
{

    protected $chapter;

    /**
     * Constructor
     * 
     * @param Chapter $chapter current chapter
     */
    public function __construct(Chapter $chapter)
    {
        $this->chapter = $chapter;
    }

    /**
     * Load Manga page
     * 
     * @return manga page
     */
    public function index()
    {
        $mangas = Manga::all();

        return View::make('admin.manga.index', ["mangas" => $mangas]);
    }

    /**
     * Load Manga chapter create page
     * 
     * @param type $mangaId manga identifier
     * 
     * @return view
     */
    public function create($mangaId)
    {
        $manga = Manga::find($mangaId);

        return View::make('admin.manga.chapter.create', ['manga' => $manga]);
    }

    /**
     * Save the new chapter
     * 
     * @return detail page
     */
    public function store()
    {
        $input = Input::all();
        $mangaId = Input::get('mangaId');

        if (!$this->chapter->fill($input)->isValid($mangaId)) {
            return Redirect::back()->withInput()->withErrors($this->chapter->errors);
        }

        $this->chapter->user_id = Auth::user()->id;

        $manga = Manga::find($mangaId);
        $chapter = $manga->chapters()->save($this->chapter);

        // queue send notification
        $date = Carbon\Carbon::now()->addMinutes(5);
        Queue::later($date, 'SendNotification', array(
            'mangaId' => $mangaId, 
            'chapter_number' => $chapter->number,
            'chapter_url' => URL::to("manga/".$manga->slug."/".$chapter->number))
        );
        
        return Redirect::route(
            'admin.manga.chapter.show', 
            ['mangaId' => $mangaId, 'chapterId' => $chapter->id]
        );
    }

    /**
     * Load Manga chapter detail page
     * 
     * @param type $manga   manga identifier
     * @param type $chapter chapter identifier
     * 
     * @return view
     */
    public function show($manga, $chapter)
    {
        $mangaInfo = Manga::find($manga);
        $chapterInfo = Chapter::find($chapter);
        $settings = Cache::get('options');

        return View::make(
            'admin.manga.chapter.show', 
            ['manga' => $mangaInfo, 'chapter' => $chapterInfo, 'settings' => $settings]
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
    public function update($mangaId, $chapterId)
    {
        $input = Input::all();
        $chapter = Chapter::find($chapterId);

        $slugDiff = false;
        $newSlug = $input['slug'];
        $oldSlug = $chapter->slug;
        if ($newSlug !== $oldSlug) {
            $slugDiff = true;
        }

        if (!$chapter->fill($input)->isValid($mangaId)) {
            return Redirect::back()->withInput()->withErrors($chapter->errors);
        }

        $chapter->user_id = Auth::user()->id;
        $chapter->save();

        // rename directory
        if ($slugDiff) {
            $manga = Manga::find($mangaId);
            $oldPath = 'uploads/manga/' . $manga->slug . '/chapters/' . $oldSlug;
            $newPath = 'uploads/manga/' . $manga->slug . '/chapters/' . $newSlug;
            if (File::isDirectory($oldPath)) {
                File::move($oldPath, $newPath);
            }
        }

        return Redirect::back()->with(
            'updateSuccess', Lang::get('messages.admin.chapter.update-success')
        );
    }

    /**
     * Delete chapter
     * 
     * @param type $mangaId   manga identifier
     * @param type $chapterId chapter identifier
     * 
     * @return view
     */
    public function destroy($mangaId, $chapterId)
    {
        $manga = Manga::find($mangaId);
        $chapter = Chapter::find($chapterId);

        $destinationPath = 'uploads/manga/' . $manga->slug . '/chapters/' 
            . $chapter->slug;

        if (File::isDirectory($destinationPath)) {
            File::deleteDirectory($destinationPath);
        }

        $chapter->deleteMe();

        return Redirect::route('admin.manga.show', ['mangaId' => $mangaId]);
    }

    public function destroyChapters($mangaId)
    {
        $ids = Input::get("chapters-ids");
        $manga = Manga::find($mangaId);
        if (strlen(trim($ids)) > 0) {
            $tab_ids = explode(',', $ids);

            foreach ($tab_ids as $id) {
                if ($id != 'all') {
                    $chapter = Chapter::find($id);

                    $destinationPath = 'uploads/manga/' . $manga->slug . '/chapters/'
                            . $chapter->slug;

                    if (File::isDirectory($destinationPath)) {
                        File::deleteDirectory($destinationPath);
                    }

                    $chapter->deleteMe();
                }
            }
        }

        return Redirect::route('admin.manga.show', ['mangaId' => $mangaId]);
    }
    
    /**
     * Create Pages/Scans of chapter
     * 
     * @param type $mangaId manga identifier
     * @param type $chapter chapter identifier
     * 
     * @return void
     */
    public function createChapterPages($mangaId, $chapter)
    {
        $pagesTmpPath = 'uploads/tmp/mangachapter/' . $mangaId . '/';
        $pagesNewPath = 'uploads/manga/' . $mangaId . '/' . $chapter->id . '/';

        if (!File::isDirectory($pagesNewPath)) {
            File::makeDirectory($pagesNewPath, 0755, true);
        }

        $counter = 0;
        $files = scandir($pagesTmpPath);
        if (false !== $files) {
            foreach ($files as $file) {
                if ('.' != $file && '..' != $file) {
                    $counter++;
                    $fileExtension = strrchr($file, '.');

                    if ($counter < 10) {
                        $newName = '0' . $counter . $fileExtension;
                    } else {
                        $newName = '' . $counter . $fileExtension;
                    }

                    $page = new Page();
                    $page->image = $newName;
                    $page->save();
                    $chapter->pages()->save($page);
                    File::move($pagesTmpPath . $file, $pagesNewPath . $newName);
                }
            }
        }
    }
    
    /**
     * Manually notify users about updates
     */
    public function notifyUsers(){
        $mangaId = filter_input(INPUT_POST, 'mangaId');
        $mangaSlug = filter_input(INPUT_POST, 'mangaSlug');
        
        // queue send notification
        $date = Carbon\Carbon::now()->addMinutes(5);
        Queue::later($date, 'SendNotification', array(
            'mangaId' => $mangaId, 
            'chapter_number' => null,
            'chapter_url' => URL::to("manga/".$mangaSlug))
        );
        
        return Response::json(
            [
                'status' => 'ok'
            ]
        );
    }
}
