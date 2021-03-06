<?php

namespace App\Http\Controllers;

use App\Category;
use App\ComicType;
use App\Http\Controllers\Utils\HelperController;
use App\Manga;
use App\Option;
use App\Status;
use App\Tag;
use Goutte\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

/**
 * Manga Controller Class
 * 
 * PHP version 5.4
 *
 * @category PHP
 * @package  Controller
 * @author   cyberziko <cyberziko@gmail.com>
 * @license  commercial http://getcyberworks.com/
 * @link     http://getcyberworks.com/
 */
class MangaController extends BaseController
{

    protected $manga;
    protected $mangaPerPage = 20;
    /**
     * Constructor
     * 
     * @param Manga $manga current manga
     */
    public function __construct(Manga $manga)
    {
        $this->middleware('auth');
        $this->manga = $manga;
    }

    /**
     * Mangas page
     * 
     * @return view
     */
    public function index()
    {
        $mangas = Manga::orderBy('name')->paginate($this->mangaPerPage);

        return view('admin.manga.index', ["mangas" => $mangas]);
    }

    public function filterList()
    {
        $alpha = filter_input(INPUT_GET, 'alpha');
        $query = filter_input(INPUT_GET, 'query');
        $sortBy = 'name';
        if ($alpha != "" || $query != "") {
            if($alpha == "Other") {
                $mangaList = Manga::where('name', 'like', "LOWER($alpha%)")
                    ->orWhere('name', 'REGEXP', "^[^a-z,A-Z]")
                    ->orderBy($sortBy)->paginate($this->mangaPerPage);
            } else {
                if ($alpha != "") {
                    $mangaList = Manga::where('name', 'like', "LOWER($alpha%)")->orWhere('name', 'like', "$alpha%");
                }
                if ($query != "") {
                    $mangaList = Manga::where('name', 'like', "%$query%");
                }

                $mangaList = $mangaList->orderBy($sortBy)->paginate($this->mangaPerPage);
            }
        } else {
            $mangaList = Manga::orderBy($sortBy)->paginate($this->mangaPerPage);
        }

        return view('admin.manga.filter', [
            "mangas" => $mangaList
        ])->render();
    }
    
    /**
     * Create manga page
     * 
     * @return view
     */
    public function create()
    {
        $status = array('' => Lang::get('messages.admin.manga.create.choose-status')) + Status::pluck('label', 'id')->toArray();
        $comicTypes = array('' => 'Choose the comic type') + ComicType::pluck('label', 'id')->toArray();
        $categories = Category::pluck('name', 'id')->toArray();
        $tags = Tag::pluck('name', 'id')->toArray();
        $str = implode(',', $tags);
        return view(
            'admin.manga.create', 
            [
                'status' => $status, 
                'comicTypes' => $comicTypes,
                'categories' => $categories,
                'tags' => $str
            ]
        );
    }

    /**
     * Create the page
     * 
     * @return view
     */
    public function store()
    {
        $input = request()->all();

        if (!$this->manga->fill($input)->isValid()) {
            return Redirect::back()
                ->withInput()
                ->withErrors($this->manga->errors);
        }

        $this->createOrUpdate($input, $this->manga);

        return Redirect::route('manga.index');
    }

    /**
     * Show manga info
     * 
     * @param type $id manga identifier
     * 
     * @return view
     */
    public function show($id)
    {
        $mangaInfo = Manga::find($id);

        return view('admin.manga.show', ['manga' => $mangaInfo]);
    }

    /**
     * Edit page
     * 
     * @param type $id manga identifier
     * 
     * @return view
     */
    public function edit($id)
    {
        $mangaInfo = Manga::find($id);
		
        $status = array('' => 'Choose Status') + Status::pluck('label', 'id')->toArray();
        $comicTypes = array('' => 'Choose the comic type') + ComicType::pluck('label', 'id')->toArray();
        $categories = Category::pluck('name', 'id')->toArray();
        $tags = Tag::pluck('name', 'id')->toArray();
        $str = implode(',', $tags);
        
        $categories_id = array();
        if (!is_null($mangaInfo->categories)) {
            foreach ($mangaInfo->categories as $category) {
                array_push($categories_id, $category->id);
            }
        }
        
        $tags_id = array();
        if (!is_null($mangaInfo->tags)) {
            foreach ($mangaInfo->tags as $tag) {
                array_push($tags_id, $tag->name);
            }
        }

        return view(
            'admin.manga.edit',
            [
                'manga' => $mangaInfo,
                'status' => $status,
                'comicTypes' => $comicTypes,
                'categories' => $categories,
                'categories_id' => $categories_id,
                'tags' => $str,
                'tags_id' => implode(',', $tags_id),
            ]
        );
    }

    /**
     * Edit the manga
     * 
     * @param type $id manga identifier
     * 
     * @return view
     */
    public function update($id)
    {
        $input = request()->all();
        $this->manga = Manga::find($id);

        $slugDiff = false;
        $newSlug = $input['slug'];
        $oldSlug = $this->manga->slug;
        if ($newSlug !== $oldSlug) {
            $slugDiff = true;
        }
        
        if (!$this->manga->fill($input)->isValid()) {
            return Redirect::back()
                ->withInput()
                ->withErrors($this->manga->errors);
        }

        $this->createOrUpdate($input, $this->manga, $slugDiff, $oldSlug, $newSlug);

        return Redirect::route('manga.show', $id);
    }

    /**
     * Delete manga
     * 
     * @param type $mangaId manga identifier
     * 
     * @return view
     */
    public function destroy($mangaId)
    {
        $manga = Manga::find($mangaId);
        $manga->deleteMe();

        $destinationPath = 'uploads/manga/' . $manga->slug;

        if (File::isDirectory($destinationPath)) {
            File::deleteDirectory($destinationPath);
        }

        return $this->index();
    }

    /**
     * Hot manga list
     * 
     * @return view
     */
    public function hotManga()
    {
        $mangas = Manga::whereNull('hot')->get();
        $hotest = Manga::whereNotNull('hot')->get();

        return view(
            'admin.manga.hot',
            ["mangas" => $mangas, "hotest" => $hotest]
        );
    }

    /**
     * Save the hot list
     * 
     * @return view
     */
    public function updateHotManga()
    {
        $input = request()->get("hotlist");
        $hotlist = explode(",", $input);

        if (count($hotlist) > 0) {
            Manga::where('hot', '<>', 'null')->update(array('hot' => null));

            foreach ($hotlist as $id) {
                Manga::where('id', $id)->update(array('hot' => true));
            }
        }

        return Redirect::back()
            ->with('updateSuccess', Lang::get('messages.admin.manga.hot.update-success'));
    }

    /**
     * Create/update manga
     * 
     * @param type $input inputs
     * @param type $manga manga id
     * 
     * @return void
     */
    public function createOrUpdate($input, $manga, $slugDiff=false, $oldSlug='', $newSlug='')
    {
        $cover = $input['cover'];
        $manga->cover = "1";
        
        if (str_contains($cover, 'uploads/tmp/mangacover/')) {
            $coverCreated = $this->createCover($cover, $manga->slug);
            if (!$coverCreated) {
                $manga->cover = null;
            }
        } else if (is_null($cover) || $cover == "") {
            $manga->cover = null;
            $coverPath = 'uploads/manga/' . $manga->slug . '/cover/';

            // clear cover directory
            File::cleanDirectory($coverPath);
        }

        $manga->user_id = Auth::user()->id;
        $manga->save();
        $cats = request()->get('categories');
        if (is_array($cats) && count($cats) > 0) {
            $manga->categories()->detach();
            $manga->categories()->attach(array_values(request()->get('categories')));
        } else {
            $manga->categories()->detach();
        }
        
        // tags

        if (count(explode(",", request()->get('tags'))) > 0) {
            $manga->tags()->detach();
            $tags = explode(",", request()->get('tags'));
            $tags_tosave = array();
            
            foreach ($tags as $index=>$entry) {
                if(strlen(trim($entry))>0) {
                $tag = Tag::firstOrNew(array('id'=>Str::slug($entry)));
                $tag->id = Str::slug($entry);
                $tag->name = $entry;
                $tag->save();
                $tags_tosave[$index] = Str::slug($entry);
                }
            }
            if(count($tags_tosave)>0) {
                $manga->tags()->attach($tags_tosave);
            }
        } else {
            $manga->tags()->detach();
        }
        
        // rename directory
        if ($slugDiff) {
            $oldPath = 'uploads/manga/' . $oldSlug;
            $newPath = 'uploads/manga/' . $newSlug;
            if (File::isDirectory($oldPath)) {
                File::move($oldPath, $newPath);
            }
        }
    }

    /**
     * Create cover
     * 
     * @param type $cover the image
     * @param type $slug  manga slug
     * 
     * @return type
     */
    public function createCover($cover, $slug)
    {
        $coverTmpPath = 'uploads/tmp/mangacover/';
        $coverNewPath = 'uploads/manga/' . $slug . '/cover/';
        
        if (!File::isDirectory($coverNewPath)) {
            File::makeDirectory($coverNewPath, 0755, true);
        }
        $cover_name = substr(strrchr($cover, "/"), count([$cover]));
        $coverCreated = File::move(
            $coverTmpPath . $cover_name, $coverNewPath . 'cover_250x350.jpg'
        );

        // GD API
        $image = HelperController::openImage($coverNewPath . 'cover_250x350.jpg');
        HelperController::makeThumb($image, $coverNewPath . 'cover_thumb.jpg', 100, 100);

        return $coverCreated;
    }
    
    public function autoMangaInfo() 
    {
        $url = filter_input(INPUT_POST, 'url-data');
        
        $client = new Client();
        $client->setHeader('timeout', '60');
        $crawler = $client->request('GET', $url);
        if(strpos($url, 'mangapanda.com')) {
            $contents = $crawler->filter('#mangaproperties table tr')
                ->each(
                    function ($node, $i) {
                        return $node->text();
                    }
                );
            
            array_push($contents, $crawler->filter('#readmangasum p')->text());
        } else if(strpos($url, 'pecintakomik.com')) {
            $contents = $crawler->filterXPath('(//div[@class="post-cnt"])[1]//li')
                ->each(
                    function ($node, $i) {
                        return $node->text();
                    }
                );
            
            array_push($contents, $crawler->filter('h2')->text());
        } else if(strpos($url, 'tumangaonline.com')) {
            $contents = $crawler->filterXPath('//table[@class="tbl table-hover"]//td')
                ->each(
                    function ($node, $i) {
                        return $node->text();
                    }
                );
            
            array_push($contents, $crawler->filter('h1')->text());
            array_push($contents, $crawler->filter('#descripcion')->text());
        } else if(strpos($url, 'lecture-en-ligne.com')) {
            $contents = $crawler->filter('#page .infos td')
                ->each(
                    function ($node, $i) {
                        return $node->text();
                    }
                );
            
            array_push($contents, $crawler->filter('h2')->text());
            array_push($contents, $crawler->filterXPath('(//div[@id="resume"]//p)[2]')->text());
        } else if(strpos($url, 'comicvn.net')) {
            array_push($contents, $crawler->filter('h1')->text());
            array_push($contents, $crawler->filter('li.author a')->text());
            array_push($contents, $crawler->filter('.detail-content p')->text());
        }

        return Response::json(
            [
                'contents' => $contents
            ]
        );
    }
	
    /**
     * manga options
     * 
     * @return view
     */
    public function mangaOptions()
    {
        $options = Option::where('key', '=' , 'manga.options')->first();
        $mangaOptions = json_decode($options->value);
        
        return view('admin.manga.options', [
            'mangaOptions' => $mangaOptions,
        ]);
    }
	
    /**
     * save manga options
     * 
     * @return view
     */
    public function saveMangaOptions()
    {
    	  $input = request()->all();
        unset($input['_token']);

        Option::where("key", "manga.options")->update(['value' => json_encode($input)]);

        // clean cache
        Cache::forget('options');
        
        return Redirect::back()
            ->with('updateSuccess', Lang::get('messages.admin.settings.update.success')
        );
    }
}
