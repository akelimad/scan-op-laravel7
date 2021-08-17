<?php

/**
 * Manga Model Class
 * 
 * PHP version 5.4
 *
 * @category PHP
 * @package  Controller
 * @author   cyberziko <cyberziko@gmail.com>
 * @license  commercial http://getcyberworks.com/
 * @link     http://getcyberworks.com/
 */
class Manga extends Eloquent
{

    public $fillable = [
        'name',
        'slug',
        'author',
        'artist',
        'releaseDate', 
        'otherNames', 
        'status_id', 
        'summary', 
        'cover',
        'caution',
        'views',
        'bulkStatus',
        'type_id'
    ];
    public static $rules = [
        'name' => 'required', 
        'slug' => 'required|unique:manga,slug,:id'
    ];
    public $errors;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'manga';

    /**
     * Status of manga
     * 
     * @return type
     */
    public function status()
    {
        return $this->belongsTo('Status');
    }

    /**
     * Manga chapters
     * 
     * @return type
     */
    public function chapters()
    {
        return $this->hasMany('Chapter')->orderBy('created_at', 'desc');
    }

    public function sortedChapters() {
        $sorted=array();
        $chapters = $this->chapters()->get();
        foreach ($chapters as $chapter) {
            $sorted[$chapter->number] = $chapter;
        }

        array_multisort(array_keys($sorted), SORT_DESC, SORT_NATURAL, $sorted);
        
        return $sorted;
    }
    
    /**
     * Last chapter
     * 
     * @return type
     */
    public function lastChapter()
    {
        return Chapter::latest()->where('manga_id', $this->id)->first();
    }

    public function posts()
    {
        return $this->hasMany('Post')
            ->where('posts.status', '1')
            ->orderBy('created_at', 'desc');
    }
    
    public function type()
    {
        return $this->belongsTo('ComicType');
    }
    
    /**
     * Delete Manga
     * 
     * @return type
     */
    public function deleteMe()
    {
        // delete all related chapters 
        if (count($this->chapters())) {
            foreach ($this->chapters()->getResults() as $chapter) {
                $chapter->deleteMe();
            }
        }

        // delete the manga
        return parent::delete();
    }

    /**
     * Manga categories
     * 
     * @return type
     */
    public function categories()
    {
        return $this->belongsToMany('Category');
    }

    /**
     * Manga tags
     * 
     * @return type
     */
    public function tags()
    {
        return $this->belongsToMany('Tag');
    }
    
    /**
     * Manga owner
     * 
     * @return type
     */
    public function user()
    {
        return $this->belongsTo('User');
    }
	
    /**
     * Lastest release
     * 
     * @return type
     */
    public function scopeLatestRelease($query, $limit)
    {
        return DB::table('manga')
            ->join('chapter', 'manga.id', '=', 'chapter.manga_id')
            ->select(
                'manga.id as manga_id',
                'manga.name as manga_name',
                'manga.slug as manga_slug', 
                'manga.cover as manga_cover', 
                'manga.hot as hot', 
                'chapter.number as chapter_number', 
                'chapter.name as chapter_name', 
                'chapter.slug as chapter_slug', 
                'chapter.created_at as chapter_created_at',
				'manga.status_id as manga_status'
            )
            ->orderBy("chapter.created_at", "desc")
            ->limit($limit)->get();
    }

    /**
     * Hot Manga
     * 
     * @return type
     */
    public function scopeHotManga()
    {
        $sql = "SELECT m.id as manga_id,
                m.slug as manga_slug,
                m.name as manga_name,
                m.cover as manga_cover,
                c1.slug as chapter_slug,
                c1.name as chapter_name,
                c1.number as chapter_number
            FROM `chapter` c1,
                `manga` m,
                (SELECT  `manga_id` , MAX(cast(number AS decimal(10, 2))) AS `number` FROM  `chapter` 
                JOIN  `manga` ON  `manga`.`id` =  `chapter`.`manga_id` 
                WHERE  `hot` IS NOT NULL 
                GROUP BY  `manga_id`) c2
            WHERE c2.manga_id = c1.manga_id 
                AND c2.number = c1.number 
                AND c2.manga_id = m.id 
            ORDER BY c1.created_at DESC";

        return DB::select($sql);
    }

    /**
     * Top 10 Manga
	 
     * @return type
     */
    public function scopeTopManga($query, $limit = 10, $direction = 'desc')
    {
        $sql = "SELECT 
                manga.id AS manga_id, 
                manga.name AS manga_name, 
                manga.slug AS manga_slug, 
                manga.cover AS manga_cover, 
                c1.slug AS chapter_slug, 
                c1.name AS chapter_name, 
                c1.number AS chapter_number, 
                ((avg_num_votes * avg_rating) + ( this_num_votes * this_rating )) / ( avg_num_votes + this_num_votes ) AS real_rating
            FROM 
                (select `item_ratings`.`item_id` AS `item_id`,
                    ((select count(`item_ratings`.`item_id`) from `item_ratings`) / (select count(distinct `item_ratings`.`item_id`) from `item_ratings`)) AS `avg_num_votes`,
                    (select avg(`item_ratings`.`score`) from `item_ratings`) AS `avg_rating`,
                    count(`item_ratings`.`item_id`) AS `this_num_votes`,
                    avg(`item_ratings`.`score`) AS `this_rating` 
                from `item_ratings` group by `item_ratings`.`item_id`) top_manga, 
                manga, 
                chapter c1, 
                (SELECT  `manga_id` , MAX(cast(number AS decimal(10, 2))) AS `number` FROM  `chapter` 
                    JOIN  `manga` ON  `manga`.`id` =  `chapter`.`manga_id` 
                    GROUP BY  `manga_id`) c2
            WHERE 
                manga.id = top_manga.item_id
                AND c1.manga_id = manga.id
                AND c1.number = c2.number and c2.manga_id = c1.manga_id
            ORDER BY `real_rating` $direction 
            LIMIT $limit";

        return DB::select($sql);
    }

    public function scopeTopViewsManga($query, $limit = 10, $direction = 'desc')
    {
    	return Manga::orderBy('views', $direction)
                ->limit($limit)
                ->get();
    }
    
    /**
    * Search Manga
    * 
    * @return manga list
    */
    public function scopeSearchManga($query, $key)
    {
    	return DB::table('manga')
            ->select('name', 'slug')
            ->where('name', 'LIKE', "%$key%")
            ->get();
    }
	
    public function scopeAllLatestRelease($query, $page = 1, $limit = 40)
    {
        $results = [];
        $results['page'] = $page;
        $results['limit'] = $limit;
        $results['totalItems'] = 0;
        $results['items'] = array();

        $mangas = DB::table('manga')
        ->join('chapter', 'manga.id', '=', 'chapter.manga_id')
        ->select(
            'manga.id as manga_id',
            'manga.name as manga_name',
            'manga.slug as manga_slug', 
            'manga.cover as manga_cover', 
            'manga.hot as hot', 
            'chapter.number as chapter_number', 
            'chapter.name as chapter_name', 
            'chapter.slug as chapter_slug', 
            'chapter.created_at as chapter_created_at',
			'manga.status_id as manga_status'
        )
        ->orderByRaw('chapter.created_at desc')
        ->groupBy('manga_id')->groupBy('chapter_number')
        ->skip($limit * ($page - 1))->take($limit)
        ->get();

        $results['totalItems'] = count(Chapter::groupBy('manga_id')->groupBy('number')->get());
        $results['items'] = $mangas;

        return $results;
    }
    
    /**
     * Validate Manga
     * 
     * @return boolean
     */
    public function isValid()
    {
        static::$rules = str_replace(':id', $this->id, static::$rules);

        $validation = Validator::make($this->attributes, static::$rules);

        if ($validation->passes()) {
            return true;
        }

        $this->errors = $validation->messages();
        return false;
    }

}
