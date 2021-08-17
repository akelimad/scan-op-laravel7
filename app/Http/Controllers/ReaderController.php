<?php

/**
 * Reader Controller Class
 * 
 * PHP version 5.4
 *
 * @category PHP
 * @package  Controller
 * @author   cyberziko <cyberziko@gmail.com>
 * @license  commercial http://getcyberworks.com/
 * @link     http://getcyberworks.com/
 */
class ReaderController extends BaseController
{

    /**
     * The Reader
     * 
     * @param type $mangaSlug   manga slug
     * @param type $chapterSlug chapter slug
     * @param type $pageSlug    page slug
     * 
     * @return view
     */
    public function reader($mangaSlug, $chapterSlug, $pageSlug = 1)
    {
        $columns = [
            'manga.id as manga_id',
            'manga.name as manga_name',
            'manga.slug as manga_slug',
            'manga.summary as manga_desc',
            'chapter.id as chapter_id',
            'chapter.name as chapter_name',
            'chapter.number as chapter_number',
            'chapter.slug as chapter_slug',
            'chapter.volume as chapter_volume',
        ];
        
        $currentChapter = Chapter::join('manga', 'manga.id', '=', 'manga_id')
            ->where('manga.slug', '=', $mangaSlug)
            ->where('chapter.slug', '=', $chapterSlug)
            ->select($columns)
            ->first();
        
        $chapters = Manga::join('chapter', 'chapter.manga_id', '=', 'manga.id')
            ->where('manga.slug', '=', $mangaSlug)
            ->select(
                [
                    'chapter.id as chapter_id',
                    'chapter.name as chapter_name',
                    'chapter.number as chapter_number',
                    'chapter.slug as chapter_slug',
                    'chapter.volume as chapter_volume',
                ]
            )
            ->get();

        $allPages = Manga::join('chapter', 'chapter.manga_id', '=', 'manga.id')
                ->join('page', 'page.chapter_id', '=', 'chapter.id')
                ->where('manga.slug', '=', $mangaSlug)
                ->where('chapter.slug', '=', $chapterSlug)
                ->select([
                    'page.image as page_image',
                    'page.slug as page_slug',
                    'page.external as external'
                ])
                ->get();
        
        $page = null;
        foreach ($allPages as $curpage) {
            if($curpage['page_slug'] == (int)$pageSlug) {
                $page = $curpage;
                break;
            }
        }
        
        $allPagesSorted=$allPages->sortBy('page_slug');

        // sort chapters
        $nextChapter = null;
        $prevChapter = null;
        $prevChapterLastPage = 1;
        
        $sortedChapters = array();
        $keys = array();
        foreach ($chapters as $chapter) {
            array_push($sortedChapters, $chapter);
            array_push($keys, $chapter->chapter_number);
        }

        array_multisort(
            $keys,
            SORT_DESC,
            SORT_NATURAL,
            $sortedChapters
        );
        
        for ($i = 0; $i < count($sortedChapters); $i++) {
            $chapter = $sortedChapters[$i];
            if ($chapter->chapter_slug == $chapterSlug) {
                if (isset($sortedChapters[$i - 1])) {
                    $nextChapter = $sortedChapters[$i - 1];
                }
                if (isset($sortedChapters[$i + 1])) {
                    $prevChapter = $sortedChapters[$i + 1];
                    $counter = Page::where('chapter_id', '=', $prevChapter->chapter_id)
                            ->count();
                    if ($counter > 0) {
                        $prevChapterLastPage = $counter;
                    }
                }

                break;
            }
        }

        // ad placement
        $reader = Placement::where('page', '=', 'READER')->first();
        $ads = array();

        foreach ($reader->ads()->get() as $key => $ad) {
            $ads[$ad->pivot->placement] = $ad->code;
        }
		
        $settings = Cache::get('options');
        $advancedSEO = json_decode($settings['seo.advanced']);
        
        return View::make(
            'front.reader', 
            [
                'settings' => $settings,
                'page' => $page,
                'current' => $currentChapter,
                'chapters' => $sortedChapters,
                'allPages' => $allPagesSorted,
                'nextChapter' => $nextChapter,
                'prevChapter' => $prevChapter,
                'prevChapterLastPage' => $prevChapterLastPage,
                'ads' => $ads,
                'seo' => $advancedSEO
            ]
        );
    }
    
    public function reportBug()
    {
        if (HelperController::isValidCaptcha(Input::all())) {
            $data = array();
            $data['broken-image'] = filter_input(INPUT_POST, 'broken-image');
            $data['email'] = filter_input(INPUT_POST, 'email');
            $data['subject'] = filter_input(INPUT_POST, 'subject');

            $user = User::find(1);

            Mail::send('admin.emails.report-bug', compact('data'), function($message) use ($data,$user)
            {
              $message->to($user->email, $user->username)
                      ->subject('Report Broken Image');
            });

            return Redirect::back()->with('sentSuccess', 'Message sent');
        } else {
            return Redirect::back()->with('sentError', 'Invalid Captcha!');
        }
    }
}
