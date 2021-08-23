<?php

namespace App\Http\Controllers;

use App\Category;
use App\Chapter;
use App\Http\Controllers\BaseController;
use App\Http\Controllers\Utils\HelperController;
use App\ItemRating;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use App\Manga;
use App\Placement;
use App\Post;
use App\Tag;
use App\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\URL;
use ZipArchive;

/**
 * Frontpage Controller Class
 * 
 * PHP version 5.4
 *
 * @category PHP
 * @package  Controller
 * @author   cyberziko <cyberziko@gmail.com>
 * @license  commercial http://getcyberworks.com/
 * @link     http://getcyberworks.com/
 */
class FrontController extends BaseController {

    /**
     * Load Homepage
     * 
     * @return view
     */
    public function index()
    {
        if (!File::exists(base_path() . "/config/config.inc.php")) {
            return Redirect::route('install.index');
        }

        $settings = Cache::get('options', []);
        $theme = Cache::get('theme', "default");
        $variation = Cache::get('variation', "flatly");
        $limit = json_decode(isset($settings['site.pagination']) ? $settings['site.pagination'] : "") ?: 10;
        
        $latestMangaUpdates = array();
        $latestMangaUpdatesResutlSet = Manga::latestRelease($limit->homepage ?? 10);
        foreach ($latestMangaUpdatesResutlSet as $manga) {
            $key = "";
            if(date("d-n-Y", strtotime($manga->chapter_created_at)) == date("d-n-Y", strtotime('-1 day'))) {
                $key = 'Y';
            } else if (date("d-n-Y", strtotime($manga->chapter_created_at)) == date("d-n-Y", strtotime(date("d-n-Y")))) {
                $key = 'T';
            } else {
                $key = date("d/n/Y", strtotime($manga->chapter_created_at));
            }

            if(!array_key_exists($key, $latestMangaUpdates)) {
                $latestMangaUpdates[$key] = [];
            }

            if(array_key_exists($manga->manga_id, $latestMangaUpdates[$key])) {
                array_push($latestMangaUpdates[$key][$manga->manga_id]['chapters'],  
                    [
                        'chapter_number' => $manga->chapter_number, 
                        'chapter_name' => $manga->chapter_name,
                        'chapter_slug' => $manga->chapter_slug
                    ]); 
            } else {
                $latestMangaUpdates[$key][$manga->manga_id] = [
                    'manga_id' => $manga->manga_id, 
                    'manga_name' => $manga->manga_name, 
                    'manga_slug' => $manga->manga_slug,
					          'manga_status' => $manga->manga_status,
                    'hot' => $manga->hot,
                    'chapters' => [
                    	[
                            'chapter_number' => $manga->chapter_number, 
                            'chapter_name' => $manga->chapter_name,
                            'chapter_slug' => $manga->chapter_slug
                        ]
                    ]
                ];
            }
        }
        
        $hotMangaList = array();
        $hotMangaResutlSet = Manga::hotManga();
        foreach ($hotMangaResutlSet as $manga) {
            array_push($hotMangaList, $manga);
        }
        // news
        $mangaNews = Post::where('posts.status', '1')
            ->limit($limit->news_homepage ?? 10)
            ->orderBy('created_at', 'desc')
            ->with('user')
            ->with('manga')
            ->get();

        // ad placement
        $homepage = Placement::where('page', '=', 'HOMEPAGE')->first();
        $ads = array();        
        foreach ($homepage->ads()->get() as $key => $ad) {
            $ads[$ad->pivot->placement] = $ad->code;
        }
        
        // widgets
        $widgets = json_decode(isset($settings['site.widgets']) ? $settings['site.widgets'] : "");
        $topManga = array();
        $topViewsManga = array();
        $tags = array();
        
        foreach ($widgets as $widget) {
            if ($widget->type == 'top_rates' && count($topManga) == 0) {
                $topMangaResutlSet = Manga::topManga(strlen($widget->number)>0?$widget->number:10);
                foreach ($topMangaResutlSet as $manga) {
                    array_push($topManga, $manga);
                }
            }
            if ($widget->type == 'top_views' && count($topViewsManga) == 0) {
                $topViewsManga = Manga::topViewsManga(strlen($widget->number)>0?$widget->number:10);
            }
            if ($widget->type == 'tags') {
                $tags = Tag::all();
            }
        }

        return view('front.themes.' . $theme . '.index', [
                "theme" => $theme,
                "variation" => $variation,
                "settings" => $settings,
                "hotMangaList" => $hotMangaList,
                "latestMangaUpdates" => $latestMangaUpdates,
                "latestMangaUpdatesResutlSet" => $latestMangaUpdatesResutlSet,
                "topManga" => $topManga,
                "mangaNews" => $mangaNews,
                "ads" => $ads,
                "widgets" => $widgets,
                "topViewsManga" => $topViewsManga,
                "tags" => $tags
            ]);
    }

    /**
     * Show Manga info page
     * 
     * @param type $slug slug page
     * 
     * @return view
     */
    public function show($slug)
    {
        $mangaInfo = Manga::where('slug', $slug)->first();
        $settings = Cache::get('options');
        $theme = Cache::get('theme');
        $variation = Cache::get('variation');

        $mangaOptions = json_decode($settings['manga.options']);
        $advancedSEO = json_decode($settings['seo.advanced']);
        
        // +1 hit
        Event::dispatch('manga.views', $mangaInfo);
            
        // ad placement
        $info = Placement::where('page', '=', 'MANGAINFO')->first();
        $ads = array();

        foreach ($info->ads()->get() as $key => $ad) {
            $ads[$ad->pivot->placement] = $ad->code;
        }
		
        // posts
        $posts = Post::where('manga_id', $mangaInfo != null ? $mangaInfo->id : 0)
                ->where('posts.status', '1')
                ->orderBy('created_at','desc')
                ->with('user')
                ->get();
        
        // sorted chapters
        $sortedChapters = array();
        $chapters = Chapter::where('manga_id', $mangaInfo != null ? $mangaInfo->id : 0)->with('user')->get();
        
        foreach ($chapters as $chapter) {
            $sortedChapters[$chapter->number] = $chapter;
        }

        array_multisort(array_keys($sortedChapters), SORT_DESC, SORT_NATURAL, $sortedChapters);

        return view('front.themes.' . $theme . '.blocs.manga.show',
            [
                "theme" => $theme,
                "variation" => $variation,
                "settings" => $settings,
                "manga" => $mangaInfo,
                'posts' => $posts,
                'chapters' => $sortedChapters,
                'mangaOptions' => $mangaOptions,
                'ads' => $ads,
                'seo' => $advancedSEO
            ]
        );
    }
    
    /**
     * Show Manga list page
     * 
     * @return view
     */
    public function mangalist($type="", $archive="")
    {
        $settings = Cache::get('options');
        $theme = Cache::get('theme');
        $variation = Cache::get('variation');
        $limit = json_decode($settings['site.pagination'])->mangalist;
        
        $advancedSEO = json_decode($settings['seo.advanced']);
        
        if ($type == "category") {
            $mangaList = Category::where('slug',$archive)->first()
                    ->manga()->orderBy('name', 'asc')->with('categories')->paginate($limit);
        } else if ($type == "author"){
            $mangaList = Manga::where('manga.author', 'like', "%$archive%")
                ->orderBy('name', 'asc')
                ->with('categories')->paginate($limit);
        } else if ($type == "tag"){
            $mangaList = Tag::where('id', $archive)->first()
                    ->manga()->orderBy('name', 'asc')->with('tags')->paginate($limit);
        } else {
            $mangaList = Manga::orderBy('name', 'asc')->with('categories')->paginate($limit);
        }
        
        return view(
            'front.themes.' . $theme . '.blocs.manga.list', 
            [
                "theme" => $theme,
                "variation" => $variation,
                "settings" => $settings,
                "filter" => $mangaList,
                'seo' => $advancedSEO
            ]
        );
    }

    public function changeMangaList() {
        $type = filter_input(INPUT_GET, 'type');
        $settings = Cache::get('options');
        $theme = Cache::get('theme');
        $variation = Cache::get('variation');
        $limit = json_decode($settings['site.pagination'])->mangalist;

        if($type == 'text') {
            $mangaList = Manga::orderBy('name')->get();

            $mangaListGrouped = array();
            foreach ($mangaList as $manga) {
                $firstLetter = substr($manga->name, 0, 1);
                if (strtoupper($firstLetter) >= 'A' && strtoupper($firstLetter) <= 'Z') {
                    if (!array_key_exists(strtoupper($firstLetter), $mangaListGrouped)) {
                        $lettreArray = array();
                        array_push($lettreArray, $manga);
                        $mangaListGrouped[strtoupper($firstLetter)] = $lettreArray;
                    } else {
                        array_push($mangaListGrouped[strtoupper($firstLetter)], $manga);
                    }
                } else {
                    if (!array_key_exists('#', $mangaListGrouped)) {
                        $lettreArray = array();
                        array_push($lettreArray, $manga);
                        $mangaListGrouped['#'] = $lettreArray;
                    } else {
                        array_push($mangaListGrouped['#'], $manga);
                    }
                }
            }

            return view(
                'front.themes.' . $theme . '.blocs.manga.list.text', 
                [
                    "theme" => $theme,
                    "variation" => $variation,
                    "settings" => $settings,
                    "mangaList" => $mangaListGrouped,
                ]
            )->render();
        } else if ($type == 'image') {
            $mangaList = Manga::orderBy('name', 'asc')->with('categories')->paginate($limit);

            return view(
                'front.themes.' . $theme . '.blocs.manga.list.image', [
                    "theme" => $theme,
                    "variation" => $variation,
                    "settings" => $settings,
                    "filter" => $mangaList
                ]
            )->render();
        }
    }
    
    public function filterList()
    {
        $settings = Cache::get('options');
        $theme = Cache::get('theme');
        $variation = Cache::get('variation');
        $limit = json_decode($settings['site.pagination'])->mangalist;
        
        $cat = filter_input(INPUT_GET, 'cat');
        $alpha = filter_input(INPUT_GET, 'alpha');
        $sortBy = filter_input(INPUT_GET, 'sortBy');
        $asc = filter_input(INPUT_GET, 'asc');
        $author = filter_input(INPUT_GET, 'author');
        $author = str_replace('+', ' ', $author);
        $tag = filter_input(INPUT_GET, 'tag');
        
        $direction = 'asc';
        if($asc == 'false') {
            $direction = 'desc';
        }
        
        if ($cat != "") {
            if(is_numeric($cat)) {
                $mangaList = Category::find($cat)->manga()->orderBy($sortBy, $direction)->paginate($limit);
            } else {
                $mangaList = Category::where('slug',$cat)->first()->manga()->orderBy($sortBy, $direction)->paginate($limit);
            }
        } else if ($alpha != "") {
            if($alpha == "Other") {
                $mangaList = Manga::where('name', 'like', "LOWER($alpha%)")
                    ->orWhere('name', 'REGEXP', "^[^a-z,A-Z]")
                    ->orderBy($sortBy, $direction)->paginate($limit);
            } else {
                $mangaList = Manga::where('name', 'like', "LOWER($alpha%)")
                    ->orWhere('name', 'like', "$alpha%")
                    ->orderBy($sortBy, $direction)->paginate($limit);
            }
        } else if ($author != ""){
            $mangaList = Manga::where('manga.author', 'like', "%$author%")
                ->orderBy($sortBy, $direction)->paginate($limit);
        } else if ($tag != "") {
            $mangaList = Tag::where('id', $tag)->first()->manga()
                    ->orderBy($sortBy, $direction)->paginate($limit);
        } else {
            $mangaList = Manga::orderBy($sortBy, $direction)->paginate($limit);
        }

        return view(
            'front.themes.' . $theme . '.blocs.manga.list.filter', [
                "theme" => $theme,
                "variation" => $variation,
                "settings" => $settings,
                "filter" => $mangaList
            ]
        )->render();
    }

    /**
     * Show Random Manga
     * 
     * @return view
     */
    public function randomManga()
    {
    	$mangas = Manga::all();
        
        if (!is_null($mangas) && count($mangas) > 0) {
            $i = rand(0, count($mangas) - 1);

            return Redirect::route(
                'front.manga.show', ['manga' => $mangas[$i]->slug]
            );
        } else {
            return Redirect::route('front.index');
        }
    }

    /**
     * Search Manga
     * 
     * @return view
     */   
    public function search()
    {
    	$mangas = Manga::searchManga($_GET['query']);
		
        $suggestions = array();
        foreach ($mangas as $manga) {
            array_push(
                $suggestions, 
                ['value'=>$manga->name, 'data'=>$manga->slug]
            );
        }

        return Response::json(
            ['suggestions' => $suggestions]
        );
    }
	
    /* *********** social ************ */

    /**
     * Get fb likes
     * 
     * @param type $url fb page
     * 
     * @return int
     */
    public function getLikes($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_URL, 'https://api.facebook.com/method/links.getStats?urls=' . $url . '&format=json');

        $curl_results = curl_exec($curl);
        curl_close($curl);
        $json = json_decode($curl_results, true);

        if (isset($json[0]['total_count'])) {
            return intval($json[0]['total_count']);
        } else {
            return 0;
        }
    }

    /**
     * Get twitter tweets
     * 
     * @param type $url twitter page
     * 
     * @return int
     */
    public function getTweets($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_URL, 'http://urls.api.twitter.com/1/urls/count.json?url=' . $url);

        $curl_results = curl_exec($curl);
        curl_close($curl);
        $json = json_decode($curl_results, true);

        if (isset($json['count'])) {
            return intval($json['count']);
        } else {
            return 0;
        }
    }

    /**
     * Get g+ +1s
     * 
     * @param type $url g+ page
     * 
     * @return type
     */
    public function getPlusones($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "https://clients6.google.com/rpc");
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_POSTFIELDS, '[{"method":"pos.plusones.get","id":"p","params":{"nolog":true,"id":"' . rawurldecode($url) . '","source":"widget","userId":"@viewer","groupId":"@self"},"jsonrpc":"2.0","key":"p","apiVersion":"v1"}]');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        $curl_results = curl_exec($curl);
        curl_close($curl);
        $json = json_decode($curl_results, true);
        return isset($json[0]['result']['metadata']['globalCounts']['count']) ? intval($json[0]['result']['metadata']['globalCounts']['count']) : 0;
    }

    /**
     * Get stumbles
     * 
     * @param type $url stumbleupon page
     * 
     * @return int
     */
    public function getStumble($url)
    {
        $json_string = file_get_contents('http://www.stumbleupon.com/services/1.01/badge.getinfo?url=' . $url);
        $json = json_decode($json_string, true);
        if (isset($json['result']['views'])) {
            return intval($json['result']['views']);
        } else {
            return 0;
        }
    }

    /**
     * Send counters to the Homepage
     * 
     * @return string
     */
    public function socialCounts()
    {
        $data = Cache::remember('social_data', 10, function()
        {
            if (isset($_GET["thisUrl"])) {
                $thisUrl = $_GET["thisUrl"];

                $data = "{";
                $data .= '"facebook": ' . json_encode($this->getLikes($thisUrl)) . ", ";
                $data .= '"twitter": ' . json_encode($this->getTweets($thisUrl)) . ", ";
                $data .= '"gplus": ' . json_encode($this->getPlusones($thisUrl)) . "}";
                //  $data .= '"stumble": ' . json_encode(get_stumble($thisUrl)) . "}";
            } else {
                $data = 'no data yet';
            }
            return $data;
        });

        return $data;
    }

    /**
     * Generate robots.txt
     * 
     * @return type
     */
    public function robots()
    {
        // Set the desired directives
        Robots::disallowPath('/admin');
        Robots::disallowPath('/install');

        $response = Response::make(Robots::getRobotsDirectives(true), 200);
        $response->header('Content-Type', 'text/plain');
        return $response;
    }

    /**
     * Generate sitemap.xml
     * 
     * @return type
     */
    public function sitemap()
    {
        // create new sitemap object
        $sitemap = App::make("sitemap");

        // add items to the sitemap (url, date, priority, freq)
        $sitemap->add(URL::to('/'), date(DATE_W3C), '1.0', 'daily');
        $sitemap->add(URL::to('/manga-list'), date(DATE_W3C), '0.6', 'monthly');

        $mangaList = Manga::orderBy('created_at', 'desc')->get();
        foreach ($mangaList as $manga) {
            $sitemap->add(
                route('front.manga.show', $manga->slug), 
                $manga->created_at, '0.8', 'weekly'
            );

            $chapterList = Chapter::where('manga_id', '=', $manga->id)
                ->orderBy('created_at', 'desc')
                ->get();
            
            foreach ($chapterList as $chapter) {
                $sitemap->add(
                    URL::to('/manga/' . $manga->slug . '/' . $chapter->slug),
                    $chapter->created_at, '0.8', 'weekly'
                );
            }
        }

        // generate your sitemap (format, filename)
        //$sitemap->store('xml', 'sitemap');
        return $sitemap->render('xml');
    }

    /**
     * Generate feed
     * 
     * @return type
     */
    public function feed()
    {
        // create new feed
        $feed = App::make("feed");

        $settings = Cache::get('options');
        $limit = json_decode($settings['site.pagination'])->homepage;
        
        // creating rss feed with our most recent chapters
        $chapters = Chapter::orderBy('created_at', 'desc')->take($limit)->get();
        
        // set your feed's title, description, link, pubdate and language
        $feed->title = $settings['site.name'];
        $feed->description = $settings['site.description'];
        $feed->link = URL::to('feed');
        $feed->setDateFormat('datetime'); // 'datetime', 'timestamp' or 'carbon'
        $feed->lang = 'en';
        $feed->setShortening(true); // true or false
        $feed->setTextLimit(100); // maximum length of description text

        foreach ($chapters as $chapter) {
            // set item's title, author, url, pubdate, description and content
            $feed->add($chapter->manga->name . ' #' . $chapter->number, '', route('front.manga.reader', [$chapter->manga->slug, $chapter->slug]), $chapter->created_at, $chapter->name, '');
        }

        return $feed->render('atom');
    }

    /**
     * download zip file
     */
    public function downloadChapter($mangaSlug, $chapterId) {
        $chapter = Chapter::find($chapterId);
        $chapterSlug = $chapter->slug;

        $sourcePath = public_path() .'/uploads/manga/' . $mangaSlug . '/chapters/' . $chapterSlug;

        if (!File::isDirectory($sourcePath)) {
            return Redirect::back()->with('downloadError', 'no pages');
        }

        $downloadDir = 'uploads/tmp/downloads/';

        if (!File::isDirectory($downloadDir)) {
            File::makeDirectory($downloadDir, 0755, true);
        }
        
        // Choose a name for the archive.
        $zipFileName = $mangaSlug.'-c'.$chapterSlug.'.zip';	
        
        // Create "MyCoolName.zip" file in public directory of project.
        $zip = new ZipArchive;
        if ($zip->open(public_path() .'/' . $downloadDir . $zipFileName, ZipArchive::CREATE) === TRUE) {

            // Copy all the files from the folder and place them in the archive.
            foreach (glob($sourcePath . '/*') as $fileName) {
                $file = basename($fileName);                
                $zip->addFile($fileName, $file);
            }
			                   
            $zip->close();

            $headers = array(
                'Content-Type' => 'application/octet-stream',
            );
	
            // Download .zip file.	    
            return Response::download(public_path() .'/' . $downloadDir . $zipFileName, $zipFileName, $headers);            
        }
    }

    /**
     * Latest release
     */
    public function latestRelease(Request $request)
    {
        $settings = Cache::get('options');
        $theme = Cache::get('theme');
        $variation = Cache::get('variation');
        $limit = json_decode($settings['site.pagination'])->latest_release;
        
        $advancedSEO = json_decode($settings['seo.advanced']);
        
        $page = $request->input('page', 1);        
        $latestMangaUpdates = array();
        $data = Manga::allLatestRelease($page, $limit);
        foreach ($data['items'] as $manga) {
            $key = "";
            if(date("d-n-Y", strtotime($manga->chapter_created_at)) == date("d-n-Y", strtotime('-1 day'))) {
                $key = 'Y';
            } else if (date("d-n-Y", strtotime($manga->chapter_created_at)) == date("d-n-Y", strtotime(date("d-n-Y")))) {
                $key = 'T';
            } else {
                $key = date("d/n/Y", strtotime($manga->chapter_created_at));
            }

            if(!array_key_exists($key, $latestMangaUpdates)) {
                $latestMangaUpdates[$key] = [];
            }

            if(array_key_exists($manga->manga_id, $latestMangaUpdates[$key])) {
                array_push($latestMangaUpdates[$key][$manga->manga_id]['chapters'],  
                    [
                        'chapter_number' => $manga->chapter_number, 
                        'chapter_name' => $manga->chapter_name,
                        'chapter_slug' => $manga->chapter_slug
                    ]); 
            } else {
                $latestMangaUpdates[$key][$manga->manga_id] = [
                    'manga_id' => $manga->manga_id, 
                    'manga_name' => $manga->manga_name, 
                    'manga_slug' => $manga->manga_slug,
					          'manga_status' => $manga->manga_status,
                    'hot' => $manga->hot,
                    'chapters' => [
                    	[
                            'chapter_number' => $manga->chapter_number, 
                            'chapter_name' => $manga->chapter_name,
                            'chapter_slug' => $manga->chapter_slug
                        ]
                    ]
                ];
            }
        }
        //$mangaItems = Paginator::make($latestMangaUpdates, $data['totalItems'], $limit);
        $mangaItems = HelperController::paginateArray($latestMangaUpdates, $limit, $page);

        return view('front.themes.' . $theme . '.blocs.manga.latest_release', [
            "theme" => $theme,
            "variation" => $variation,
            "settings" => $settings,
            "latestMangaUpdates" => $latestMangaUpdates,
            'seo' => $advancedSEO,
            'mangaItems' => $mangaItems,
          ]
        );
    }
    
    public function news($slug)
    {
        $post = Post::where('slug', $slug)->first();
        $settings = Cache::get('options');
        $theme = Cache::get('theme');
        $variation = Cache::get('variation');
        
        $advancedSEO = json_decode($settings['seo.advanced']);
            
        return view(
            'front.themes.' . $theme . '.blocs.news.news', 
            [
                "theme" => $theme,
                "variation" => $variation,
                "settings" => $settings,
                "post" => $post,
                "seo" => $advancedSEO
            ]
        );
    }
  
    /**
     * Latest news
     */
    public function latestNews()
    {
        $settings = Cache::get('options');
        $theme = Cache::get('theme');
        $variation = Cache::get('variation');
        
        $advancedSEO = json_decode($settings['seo.advanced']);
        $limit = json_decode($settings['site.pagination'])->newslist;
        $posts = Post::where('posts.status', '1')
            ->orderBy('created_at', 'desc')
            ->with('user')
            ->with('manga')
            ->paginate($limit);

        return view(
            'front.themes.' . $theme . '.blocs.news.latest_news', 
            [
                "theme" => $theme,
                "variation" => $variation,
                "settings" => $settings,
                "posts" => $posts,
                "seo" => $advancedSEO
            ]
        );
    }
    
    /**
     * Contact us
     */
    public function contactUs()
    {
        $settings = Cache::get('options');


        $theme = Cache::get('theme');
        $variation = Cache::get('variation');

        return view('front.themes.' . $theme . '.contact', [
                "theme" => $theme,
                "variation" => $variation,
                "settings" => $settings,
            ]
        );
    }
    
    public function sendMessage(Request $request)
    {
        if (HelperController::isValidCaptcha($request->all())) {
            $data = array();
            $data['name'] = filter_input(INPUT_POST, 'name');
            $data['email'] = filter_input(INPUT_POST, 'email');
            $data['subject'] = filter_input(INPUT_POST, 'subject');

            $user = User::find(1);

            Mail::send('admin.emails.contact-us', compact('data'), function($message) use ($data,$user)
            {
              $message->to($user->email, $user->username)
                      ->subject('Contact from ' . $data['name']);
            });

            return Redirect::back()->with('sentSuccess', 'Message sent');
        } else {
            return Redirect::back()->with('sentError', 'Invalid Captcha!');
        }
    }
    
    public function advSearch()
    {
        $settings = Cache::get('options');
        $theme = Cache::get('theme');
        $variation = Cache::get('variation');
        
        return view(
                'front.themes.' . $theme . '.blocs.manga.adv_search',
                [
                "theme" => $theme,
                "variation" => $variation,
                "settings" => $settings,
            ]
        );
    }
    
    public function advSearchFilter()
    {
        $settings = Cache::get('options');
        $theme = Cache::get('theme');
        $variation = Cache::get('variation');
        $limit = json_decode($settings['site.pagination'])->mangalist;
        
        parse_str(filter_input(INPUT_POST, 'params'), $params);
        $categories = isset($params['categories']) ? $params['categories'] : null;
        $status = isset($params['status']) ? $params['status'] : null;
        $types = isset($params['types']) ? $params['types'] : null;
        $release = !empty($params['release']) ? $params['release'] : null;
        $author = !empty($params['author']) ? strtolower($params['author']) : null;

        $query = Manga::groupBy('manga.id')->with('categories');

        if (!is_null($categories)) {
            $query = $query->join('category_manga', 'manga.id', '=', 'category_manga.manga_id')
                    ->whereIn('category_manga.category_id', $categories);
        }
        if (!is_null($status)){
            $query = $query->whereIn('manga.status_id', $status);
        }
        if (!is_null($types)){
            $query = $query->whereIn('manga.type_id', $types);
        }
        if (!is_null($release)){
            $query = $query->where('manga.releaseDate', 'like', $release);
        }
        if (!is_null($author)){
            $query = $query->where('manga.author', 'like', "%$author%");
        }

        $mangaList = $query->paginate($limit);

        return view('front.themes.' . $theme . '.blocs.manga.list.filter', [
                "theme" => $theme,
                "variation" => $variation,
                "settings" => $settings,
                "filter" => $mangaList
            ]
        )->render();
    }

    public function storeRatingScore(Request $request) {
        $item_rating = ItemRating::where("ip_address", $request->ip())->first();
        if (!$item_rating) {
            $item_rating = new ItemRating();
        }
        $input = request()->all();
        $item_rating->fill($input);
        $item_rating->save();
    }
}
