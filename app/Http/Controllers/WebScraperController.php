<?php

use Goutte\Client;

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
class WebScraperController extends BaseController
{

    private $client;
    private $website;
    private $websites;
    public $chapterUrl;
    public $crawler;
    public $filters;
    public $content = array();

    /**
     * Defining our Dependency Injection Here.
     * or Instantiate new Classes here.
     *
     * @param Client $client current client
     * 
     * @return void
     */
    public function __construct(Client $client)
    {
        $client->setHeader('User-Agent', "Google Mozilla/5.0 (compatible; Googlebot/2.1;)");
        $client->setHeader('Referer', "http://www.google.com/bot.html");
    
        $this->client = $client;
		
        $this->websites = [
            'mangapanda'    => [
                'name'	=> 'MangaPanda (English)',
                'url'	=> 'http://www.mangapanda.com',
                'filter'=> [
                    'page_list'     => '#pageMenu option',
                    'image_src'     => '#imgholder img',
                ]
            ],
            'mangareader'    => [
                'name'	=> 'MangaReader (English)',
                'url'	=> 'http://www.mangareader.net',
                'filter'=> [
                    'page_list'     => '#pageMenu option',
                    'image_src'     => '#imgholder img',
                ]
            ],
            /*'tumanga'       => [
                'name'	=> 'TuManga (Spanish)',
                'url'	=> 'http://www.tumangaonline.com',
                'filter'=> [
                    'page_list'     => 'input[hidden="true"]',
                    'image_src'     => '#imgNextImagen img',
                    'mainPage'      => '.dropdown-menu a[target="_blank"]',
                ]
            ],*/
            '9manga_es'       => [
                'name'	=> '9Manga (Spanish)',
                'url'	=> 'http://es.ninemanga.com',
                'filter'=> [
                    'page_list'     => '(//select[@id="page"])[1]/option',
                    'image_src'     => 'img.manga_pic',
                ]
            ],
            'comicvn'    => [
                'name'	=> 'Comicvn (Vietnam)',
                'url'	=> 'http://comicvn.net',
                'filter'=> [
                    'page_list'     => '#txtarea img',
                ]
            ],
            'l-e-l'    => [
                'name'	=> 'Lecture-en-ligne (French)',
                'url'	=> 'http://lecture-en-ligne.com',
                'filter'=> [
                    'page_list'     => '#head .pages option',
                    'image_src'     => '#image',
                ]
            ],
            'pecinta'    => [
                'name'	=> 'Pecinta Komik (Indonesian )',
                'url'	=> 'http://www.pecintakomik.com',
                'filter'=> [
                    'page_list'     => '.pager select[name="page"] option',
                    'image_src'     => '.picture',
                ]
            ],
            '3asq'    => [
                'name'	=> '3asq (Arabic)',
                'url'	=> 'http://www.3asq.info',
                'filter'=> [
                    'page_list'     => '(//select[@class="cbo_wpm_pag"])[1]/option',
                    'chap_title'    => "(//select[@class='cbo_wpm_chp'])[1]/option[@selected='selected']",
                    'image_src'     => '.prw img',
                ]
            ]
        ];
    }

    /**
     * Start scraping
     * 
     * @param type $mangaId   manga id
     * @param type $chapterId chapter id
     * 
     * @return type
     */
    public function scraper($mangaId)
    {
        $manga = Manga::find($mangaId);
        $settings = Cache::get('options');
        
        return View::make(
            'admin.manga.chapter.scraper', 
            ['manga' => $manga, 'websites' => $this->websites, 'settings' => $settings]
        );
    }

    /**
     * This will be used for Outputing our Data
     * and Rendering to browser.
     *
     * @return void
     */
    public function startScraper() 
    {
        $selectedWebsite = filter_input(INPUT_POST, 'selectedWebsite');
        $mangaId = filter_input(INPUT_POST, 'mangaId');
        $chapterUrl = filter_input(INPUT_POST, 'chapterUrl');
        
        $this->website = $this->websites[$selectedWebsite]['url'];
        $this->chapterUrl = $chapterUrl;
        $this->setScrapeUrl($this->chapterUrl);

        $this->filters = $this->websites[$selectedWebsite]['filter'];

        switch ($selectedWebsite) {
            case 'mangapanda':
                return $this->_mangapanda($mangaId, $chapterUrl);
            case 'mangareader':
                return $this->_mangapanda($mangaId, $chapterUrl);
            case 'tumanga':
                return $this->_tumanga($mangaId);
            case 'comicvn':
                return $this->_comicvn($mangaId, $chapterUrl);
            case 'l-e-l':
                return $this->_lel($mangaId, $chapterUrl);
            case 'pecinta':
                return $this->_pecinta($mangaId, $chapterUrl);
            case '3asq':
                return $this->_3asq($mangaId, $chapterUrl);
            case '9manga_es':
                return $this->_ninemanga_es($mangaId, $chapterUrl);
        }
    }

    /**
     * Setup our scraper data. Which includes the url that
     * we want to scrape
     *
     * @param String $url    = default is NULL 
     * @param String $method = Method Types its either POST || GET
     * 
     * @return void
     */
    public function setScrapeUrl($url = null, $method = 'GET')
    {
        $this->crawler = $this->client->request($method, $url);
    }

    /**
     * This will get all the return Result from our Web Scraper
     *
     * @return array
     */
    public function getContents() 
    {        
        $countContent = $this->crawler
            ->filter($this->filters['page_list'])->count();
        if ($countContent) {
            $this->content = $this->crawler
                ->filter($this->filters['page_list'])
                ->each(
                    function ($node, $i) {
                        return [
                            'index' => $node->text(),
                            'url' => $this->_getImageSrc(
                                $this->website . $node->attr('value')
                            )
                        ];
                    }
                );
        }

        return $this->content;
    }

    /**
     * Get image src
     * 
     * @param type $nextPageUrl url
     * @param type $method      GET
     * 
     * @return type
     */
    private function _getImageSrc($nextPageUrl, $method = 'GET') 
    {
        $this->client->setHeader('timeout', '60');
        $imgCrawler = $this->client->request($method, $nextPageUrl);
        return $imgCrawler->filter($this->filters['image_src'])->attr('src');
    }

    /**
     * Create the new chapter
     * 
     * @param type $mangaId    manga id
     * @param type $chapterUrl chapter url
     * 
     * @return chapter id
     */
    private function _mangapanda($mangaId, $chapterUrl)
    {
        $chapterFragUrl = substr($chapterUrl, strlen($this->website)+1);
        $tab = explode("/", $chapterFragUrl);
        $mainPage = $tab[0];
        $chapterNumber = $tab[1];

        $mainPageCrawler = $this->client
            ->request('GET', $this->website . '/'. $mainPage);
        
        $tmpTitle = $mainPageCrawler
            ->filter('#listing td a[href*="'.$chapterFragUrl.'"]')
            ->parents()
            ->first()
            ->text();
        
        $title = trim(substr(strstr($tmpTitle, ":"), 1));
        
        if(strlen($title) == 0) {
            $title = $chapterNumber;
        }
        
        $chapterId = $this->_createChapter($mangaId, $chapterNumber, $title);
        
        if($chapterId == 'exist') {
            return Response::json(
                ['chapterId' => $chapterId, 'chapterNumber' => $chapterNumber]
            );
        }
        
        return Response::json(
            [
                'contents' => $this->getContents(),
                'chapterId' => $chapterId,
                'chapterNumber' => $chapterNumber
            ]
        );
    }
    
    private function _tumanga($mangaId)
    {
        $pagesData = $this->crawler
            ->filter($this->filters['page_list'])->first()->attr('value');

        // img src
        $imgSrcURL = 'http://img1.tumangaonline.com/subidas/';
        
        // images, number
        $tabInfo = explode(';', $pagesData);
        $tabImage = explode('%', $tabInfo[3]);
        $chapterNumber = $tabInfo[1];

        $contents = array();
        foreach ($tabImage as $index=>$img) {
            array_push(
                $contents, 
                [
                    'index' => $index + 1, 
                    'url' => $imgSrcURL . $tabInfo[0] .'/'. $tabInfo[1] .'/'. $tabInfo[2] . '/'. $img
                ]
            );
        }
        
        // title
        $mainPage = $this->crawler->filter($this->filters['mainPage'])
                ->first()
                ->attr('href');
        $mainPageCrawler = $this->client->request('GET', $mainPage);

        $tmpTitle = "";
        try {
            $tmpTitle = $mainPageCrawler
                ->filterXPath("//table[@id='accordion']//h5/a[contains(text(),'Cáspitulo " . $chapterNumber . " ')]")
                ->text();
        } catch (Exception $e) {
            try {
                $tmpTitle = $mainPageCrawler
                    ->filterXPath("//table[@id='accordion']//h5/a[contains(text(),'Capítulo " . $chapterNumber . " ')]")
                    ->text();
            } catch (Exception $e) {}
        }
        
        $title = trim(substr(trim($tmpTitle), strlen('Capítulo ' . $chapterNumber . ' ')));
        
        if(strlen($title) == 0) {
            $title = $chapterNumber;
        }
        
        $chapterId = $this->_createChapter($mangaId, $chapterNumber, $title);
        
        if($chapterId == 'exist') {
            return Response::json(
                ['chapterId' => $chapterId, 'chapterNumber' => $chapterNumber]
            );
        }
        
        return Response::json(
            [
                'contents' => $contents, 
                'chapterId' => $chapterId, 
                'chapterNumber' => $chapterNumber
            ]
        );
    }

    /**
     * http://comicvn.net (Vietnam source)
     */
    private function _comicvn($mangaId, $chapterUrl)
    {
        $contents = array();
        
        $countContent = $this->crawler
            ->filter($this->filters['page_list'])->count();
		
        // image src
        if ($countContent) {
            $contents = $this->crawler
                ->filter($this->filters['page_list'])
                ->each(
                    function ($node, $i) {
                        return [
                            'index' => $i + 1,
                            'url' => strpos($node->attr('src'), "?imgmax")?substr($node->attr('src'), 0, strpos($node->attr('src'), "?imgmax")):$node->attr('src')
                        ];
                    }
                );
		
            // number
            $pattern = 'chapter-';
            if(strpos($chapterUrl, 'chuong-')) {
                $pattern = 'chuong-';
            }
            $chapterFragUrl = substr($chapterUrl, strpos($chapterUrl, $pattern) + strlen($pattern));
            $chapterNumber = substr($chapterFragUrl, 0, strpos($chapterFragUrl, '/'));

            // title (no title)
            $title = '';

            if(strlen($title) == 0) {
                $title = $chapterNumber;
            }

            $chapterId = $this->_createChapter($mangaId, $chapterNumber, $title);

            if($chapterId == 'exist') {
                return Response::json(
                    ['chapterId' => $chapterId, 'chapterNumber' => $chapterNumber]
                );
            }

            return Response::json(
                [
                    'contents' => $contents, 
                    'chapterId' => $chapterId, 
                    'chapterNumber' => $chapterNumber
                ]
            );
        }
        
        return Response::json(
            [
                'contents' => $contents, 
            ]
        );
    }

    /**
     * http://lecture-en-ligne.com
     * @param type $mangaId
     * @param type $chapterUrl
     * @return type
     */
    private function _lel($mangaId, $chapterUrl)
    {
        $chapterNumber = trim($this->crawler->filter('#chapter_num')->text());

        $title = 'chapitre '. $chapterNumber;
        
        $chapterId = $this->_createChapter($mangaId, $chapterNumber, $title);
        
        if($chapterId == 'exist') {
            return Response::json(
                ['chapterId' => $chapterId, 'chapterNumber' => $chapterNumber]
            );
        }
        $countContent = $this->crawler
            ->filter($this->filters['page_list'])->count();
        if ($countContent) {
            $this->content = $this->crawler
                ->filter($this->filters['page_list'])
                ->each(
                    function ($node, $i) {
                        return [
                            'index' => $node->text(),
                            'url' => $this->_getImageSrc(
                                substr($this->chapterUrl, 0, strrpos($this->chapterUrl, "/")+1) . $node->attr('value') . '.html'
                            )
                        ];
                    }
                );
        }
        
        return Response::json(
            [
                'contents' => $this->content,
                'chapterId' => $chapterId,
                'chapterNumber' => $chapterNumber
            ]
        );
    }
    
    /**
     * http://www.pecintakomik.com
     * @param type $mangaId
     * @param type $chapterUrl
     * @return type
     */
    private function _pecinta($mangaId, $chapterUrl)
    {
        $chapterNumber = trim($this->crawler->filter('.pager select[name="chapter"] option:selected')->text());

        $title = 'chapter '. $chapterNumber;
        
        $chapterId = $this->_createChapter($mangaId, $chapterNumber, $title);
        
        if($chapterId == 'exist') {
            return Response::json(
                ['chapterId' => $chapterId, 'chapterNumber' => $chapterNumber]
            );
        }
        $countContent = $this->crawler
            ->filter($this->filters['page_list'])->count();
        if ($countContent) {
            $this->content = $this->crawler
                ->filter($this->filters['page_list'])
                ->each(
                    function ($node, $i) {
                        return [
                            'index' => $node->text(),
                            'url' => str_replace( ' ', '%20', 'http://www.pecintakomik.com/manga/'.$this->_getImageSrc(
                                $this->chapterUrl . '/'. $node->attr('value'))
                            )
                        ];
                    }
                );
        }
        
        return Response::json(
            [
                'contents' => $this->content,
                'chapterId' => $chapterId,
                'chapterNumber' => $chapterNumber
            ]
        );
    }
    
    /**
     * Create the new chapter
     * 
     * @param type $mangaId    manga id
     * @param type $chapterUrl chapter url
     * 
     * @return chapter id
     */
    private function _3asq($mangaId, $chapterUrl)
    {
        if(substr($this->chapterUrl, -1) != '/') {
            $this->chapterUrl = $this->chapterUrl . '/';
        }

        $tmpTitle = $this->crawler
            ->filterXPath($this->filters['chap_title'])->text();
        $title = trim(substr($tmpTitle, strpos($tmpTitle, "-")+1));
        $chapterNumber = trim(substr($tmpTitle, 0, strpos($tmpTitle, "-")));

        $contents = array();
        
        $countContent = $this->crawler
            ->filterXPath($this->filters['page_list']);
        
        if ($countContent) {
            $contents = $this->crawler
                ->filterXPath($this->filters['page_list'])
                ->each(
                    function ($node, $i) {
                        return [
                            'index' => $node->text(),
                            'url' => $this->_getImageSrc(
                                $this->chapterUrl . $node->text()
                            )
                        ];
                    }
                );
        }

        if(strlen($title) == 0) {
            $title = $chapterNumber;
        }
        
        $chapterId = $this->_createChapter($mangaId, $chapterNumber, $title, false);
        
        if($chapterId == 'exist') {
            return Response::json(
                ['chapterId' => $chapterId, 'chapterNumber' => $chapterNumber]
            );
        }
        
        return Response::json(
            [
                'contents' => $contents,
                'chapterId' => $chapterId,
                'chapterNumber' => $chapterNumber
            ]
        );
    }
    
    /**
     * http://es.ninemanga.com (Spanish source)
     */
    private function _ninemanga_es($mangaId, $chapterUrl)
    {
        $contents = array();
        $chapterUrl = str_replace('%20', ' ', $chapterUrl);
        $countContent = $this->crawler
           ->filterXPath($this->filters['page_list'])->count();
		
        // image src
        if ($countContent) {
            $contents = $this->crawler
                ->filterXPath($this->filters['page_list'])
                ->each(
                    function ($node, $i) {
                        return [
                            'index' => $i + 1,
                            'url' => $this->_getImageSrc($this->website . $node->attr('value'))
                        ];
                    }
                );
		
            // number
            $chapterFragUrl = substr($chapterUrl, strlen($this->website)+1);
            $tab = explode("/", $chapterFragUrl);
            $mangaName = $tab[1];
            
            $selectedChapter = $this->crawler->filter('#chapter :selected')->text();
            $chapterNumber = substr($selectedChapter, strlen($mangaName)+1);
            
            if(strrpos($chapterNumber, 'Capítulo ')!==false) {
               $chapterNumber = substr($chapterNumber, strlen('Capítulo ')); 
            }
        
                //http://es.ninemanga.com/chapter/Yamada-kun%20to%20Nananin%20no%20Majo/449941.html
            // title (no title)
            $title = '';

            if(strlen($title) == 0) {
                $title = $chapterNumber;
            }

            $chapterId = $this->_createChapter($mangaId, $chapterNumber, $title);

            if($chapterId == 'exist') {
                return Response::json(
                    ['chapterId' => $chapterId, 'chapterNumber' => $chapterNumber]
                );
            }

            return Response::json(
                [
                    'contents' => $contents, 
                    'chapterId' => $chapterId, 
                    'chapterNumber' => $chapterNumber
                ]
            );
        }
        
        return Response::json(
            [
                'contents' => $contents, 
            ]
        );
    }
    
    /***/
    private function _createChapter($mangaId, $chapterNumber, $title, $checkChapterExist = true)
    {
    	$chapterExist = 0;
        
        if($checkChapterExist) {
            // allow duplicate?
            $mangaOptions = json_decode(Option::where('key', '=' , 'manga.options')->first()->value);

            if (isset($mangaOptions->allow_duplicate_chapter) 
                && $mangaOptions->allow_duplicate_chapter == '1') {
                $checkChapterExist = false;
            }
        }
        
    	if($checkChapterExist) {
            $chapterExist = Chapter::join("manga", "manga.id", "=", "manga_id")
                ->where(function ($query) use($chapterNumber) {
                    $query->where('number', '=', $chapterNumber)
                    ->orWhere('chapter.slug', '=', $chapterNumber);
                })
                ->where('manga_id', '=', $mangaId)
                ->count();
    	}

        if ($chapterExist > 0) {
            return 'exist';
        } else {
            $chapter = new Chapter();
            
            $chapter->name = $title;
            $chapter->slug = $chapterNumber;
            $chapter->number = $chapterNumber;
            $chapter->user_id = Auth::user()->id;
            
            $manga = Manga::find($mangaId);
            $savedChapter = $manga->chapters()->save($chapter);

            return $savedChapter->id;
       }
    }

    // ---------- Bulk Scraping ---------- \\ 
    public function getTotalChapters()
    {
        $mangaPageUrl = filter_input(INPUT_POST, 'mangaPageUrl');
        $this->setScrapeUrl($mangaPageUrl);
        $content = array();

        if(strpos($mangaPageUrl, '3asq.info')) {
            $pages = array();
            
            if($this->crawler->filterXPath('(//ul[@class="pgg"])[1]//a')->count()>1){
                $pages = $this->crawler->filterXPath('(//ul[@class="pgg"])[1]//a')->each(
                    function ($node) {
                        if(is_numeric($node->text())) {
                            return $node->attr('href');
                        }
                    }
                );
                
                foreach ($pages as $page) {
                    if(!is_null($page)) {
                        $pageCrawler = $this->client->request('GET', $page);
                        $table = $pageCrawler->filter('ul.lst a.lst')->each(
                            function ($node) {
                                return ['url' => $node->attr('href')];
                            }
                        );
                        
                        foreach ($table as $value) {
                            array_push($content, $value);
                        }
                    }
                }
            } else {
                $content = $this->crawler->filter('ul.lst a.lst')->each(
                    function ($node) {
                        return ['url' => $node->attr('href')];
                    }
                ); 
            }
            
            $total = count($content);
        } else if(strpos($mangaPageUrl, 'comicvn.net')) {
            $tableListing = $this->crawler->filter('.list-chapter');
            
            $total = $tableListing->filter('.list-chapter td a')->count();
            
            if ($total > 0) {
                $content = $tableListing->filter('.list-chapter td a')
                    ->each(
                        function ($node, $i) {
                            return [
                                'url' => $node->attr('href'),
                                'title' =>  $node->text()
                            ];
                        }
                    );
            }
        } else if(strpos($mangaPageUrl, 'lecture-en-ligne.com')) {
            $tableListing = $this->crawler->filter('.accueil');
            
            $total = $tableListing->filter('.accueil td a')->count();
            
            if ($total > 0) {
                $content = $tableListing->filter('.accueil td a')
                    ->each(
                        function ($node, $i) {
                            return [
                                'url' => $node->attr('href'),
                                'title' =>  ''
                            ];
                        }
                    );
            }
        } else if(strpos($mangaPageUrl, 'pecintakomik.com')) {
            $tableListing = $this->crawler->filter('.post-cnt');
            
            $total = $tableListing->filter('.post-cnt li a')->count();
            
            if ($total > 0) {
                $content = $tableListing->filter('.post-cnt li a')
                    ->each(
                        function ($node, $i) {
                            return [
                                'url' => $node->attr('href'),
                                'title' =>  ''
                            ];
                        }
                    );
            }
        } else if(strpos($mangaPageUrl, 'es.ninemanga.com')) {
            $tableListing = $this->crawler->filter('.silde');
            
            $total = $tableListing->filter('.silde .chapter_list_a')->count();
            
            if ($total > 0) {
                $content = $tableListing->filter('.silde .chapter_list_a')
                    ->each(
                        function ($node, $i) {
                            return [
                                'url' => $node->attr('href'),
                                'title' =>  ''
                            ];
                        }
                    );
            }
        } else {
            $tableListing = $this->crawler->filter('#listing');

            $total = $tableListing->filter('#listing td a')->count();

            if ($total > 0) {
                $content = $tableListing->filter('#listing td a')
                    ->each(
                        function ($node, $i) {
                            return [
                                'url' => $node->attr('href'),
                                'title' =>  trim(substr(strstr($node->parents()->first()->text(), ":"), 1))
                            ];
                        }
                    );
            }
        }
        
        return Response::json(
            [
                'total' => $total,
                'content' => $content
            ]
        );
    }
	
    public function getChapter() 
    {
        $selectedWebsite = filter_input(INPUT_POST, 'selectedWebsite');
        $mangaId = filter_input(INPUT_POST, 'mangaId');
        $chapterUrl = filter_input(INPUT_POST, 'chapterUrl');
        $title = filter_input(INPUT_POST, 'chapterTitle');
		
        $this->website = $this->websites[$selectedWebsite]['url'];
        $this->chapterUrl = $selectedWebsite == '3asq'?$chapterUrl:$this->website . $chapterUrl;
        $this->setScrapeUrl($this->chapterUrl);

        $this->filters = $this->websites[$selectedWebsite]['filter'];
	
        if($selectedWebsite == '3asq') {
            $cnt =  $this->_3asq($mangaId, $chapterUrl);
            return $cnt;
        } else if ($selectedWebsite == 'comicvn') {
            $cnt =  $this->_comicvn($mangaId, $this->chapterUrl);
            return $cnt;
        } else if ($selectedWebsite == 'l-e-l') {
            $cnt =  $this->_lel($mangaId, $this->chapterUrl);
            return $cnt;
        } else if ($selectedWebsite == 'pecinta') {
            $cnt =  $this->_pecinta($mangaId, $this->chapterUrl);
            return $cnt;
        } else if ($selectedWebsite == '9manga_es') {
            $cnt =  $this->_ninemanga_es($mangaId, $this->chapterUrl);
            return $cnt;
        }
        
        $chapterNumber = substr($chapterUrl, strrpos($chapterUrl, '/')+1);
        $chapterId = $this->_createChapter($mangaId, $chapterNumber, $title, false);
        
        return Response::json(
            [
                'contents' => $this->getContents(),
                'chapterId' => $chapterId,
                'chapterNumber' => $chapterNumber
            ]
        );
    }

    public function abort() 
    {
    	$mangaId = filter_input(INPUT_POST, 'mangaId');
        $bulkStatus = filter_input(INPUT_POST, 'bulkStatus');
		
        $manga = Manga::find($mangaId);
        $manga->bulkStatus =$bulkStatus;
        $manga->save();
    }
	
    public function resume() 
    {
        $mangaId = filter_input(INPUT_POST, 'mangaId');
        $manga = Manga::find($mangaId);

        return Response::json(
            [
                'bulkStatus' => $manga->bulkStatus
            ]
        );
    }
}
