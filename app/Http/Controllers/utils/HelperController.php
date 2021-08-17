<?php

/**
 * File upload Controller Class
 * 
 * PHP version 5.4
 *
 * @category PHP
 * @package  Controller
 * @author   cyberziko <cyberziko@gmail.com>
 * @license  commercial http://getcyberworks.com/
 * @link     http://getcyberworks.com/
 */
class HelperController extends BaseController
{

    /**
     * Format date
     * 
     * @param type $created_at date
     * 
     * @return type
     */
    public function formateCreationDate($created_at) 
    {
        $date = new DateTime($created_at);
        return $date->format('d M. Y');
    }

    /**
     * List to String
     * 
     * @param type $list      list
     * @param type $separator separator
     * 
     * @return string
     */
    public static function listAsString($list, $separator)
    {
        $prefix = '';
        $str = '';
        foreach ($list as $item) {
            $str .= $prefix . $item->name;
            $prefix = $separator;
        }

        return $str;
    }

    /**
     * Create a thumbnail
     * 
     * @param type $source_image   image source
     * @param type $dest           image destination
     * @param type $desired_width  desired width
     * @param type $desired_height desired height
     */
    public static function makeThumb($source_image, $dest, $desired_width, $desired_height)
    {
        /* read the source image */
        $width = imagesx($source_image);
        $height = imagesy($source_image);

        /* create a new, "virtual" image */
        $virtual_image = imagecreatetruecolor($desired_width, $desired_height);

        /* copy source image at a resized size */
        imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);

        /* create the physical thumbnail image to its destination */
        imagejpeg($virtual_image, $dest);
    }

    /**
     * Open image file
     * 
     * @param type $file file
     * 
     * @return boolean
     */
    public static function openImage($file)
    {
        //detect type and process accordinally
        $size = getimagesize($file);
        switch ($size["mime"]) {
            case "image/jpeg":
                $im = imagecreatefromjpeg($file); //jpeg file
                break;
            case "image/gif":
                $im = imagecreatefromgif($file); //gif file
                break;
            case "image/png":
                $im = imagecreatefrompng($file); //png file
                break;
            default:
                $im = false;
                break;
        }
        
        return $im;
    }
    
    /**
     * truncat text by limit
     * 
     * @param string $text
     * @param type $limit
     * @return string
     */
    public static function limitTextByWord($text, $limit) {
        if (str_word_count($text, 0) > $limit) {
            $words = str_word_count($text, 2);
            $pos = array_keys($words);
            $text = substr($text, 0, $pos[$limit]) . '...';
        }
        
        return $text;
    }

    /**
     * truncat text by limit
     * 
     * @param string $text
     * @param type $limit
     * @return string
     */
    public static function limitTextByChar($text, $limit) {
        if (strlen($text) > $limit) {
            $text = substr($text, 0, $limit) . '...';
        }
        
        return $text;
    }
    
    CONST MANGA_NAME = '%manga_name%';
    CONST MANGA_AUTHOR = '%manga_author%';
    CONST MANGA_ARTIST = '%manga_artist%';
    CONST MANGA_CAT = '%manga_categories%';
    CONST MANGA_DESC = '%manga_description%';
    
    CONST CHAPTER_TITLE = '%chapter_title%';
    CONST CHAPTER_NUMBER = '%chapter_number%';
    CONST CHAPTER_VOLUME = '%chapter_volume%';
    CONST PAGE_NUMBER = '%page_number%';
    
    CONST POST_TITLE = '%post_title%';
    CONST POST_CONTENT = '%post_content%';
    CONST POST_KEYWORDS = '%post_keywords%';
    
    /**
     * SEO ADV: Info meta & title
     * 
     * @return string
     */
    public static function advSeoInfoPage($value, $manga) {
        $str = str_replace(self::MANGA_NAME, $manga->name, $value);
        $str = str_replace(self::MANGA_AUTHOR, $manga->author, $str);
        $str = str_replace(self::MANGA_ARTIST, $manga->artist, $str);
        $str = str_replace(self::MANGA_CAT, static::listAsString($manga->categories, ', '), $str);
        $str = str_replace(self::MANGA_DESC, static::limitTextByChar($manga->summary, 250), $str);

        return trim($str);
    }
    
    /**
     * SEO ADV: Reader meta & title
     * 
     * @return string
     */
    public static function advSeoReaderPage($value, $current, $page) {
        if (str_contains($value, [self::MANGA_AUTHOR, self::MANGA_ARTIST, self::MANGA_CAT, self::MANGA_DESC])) {
            $manga = Cache::remember('manga-reader-'.$current->manga_id, 30, function() use($current) {
              return  Manga::where('id',$current->manga_id)->with('categories')->first();
            });
            $str = static::advSeoInfoPage($value, $manga);
        } else {
            $str = $value;
        }
        
        $str = str_replace(self::MANGA_NAME, $current->manga_name, $str);
        $str = str_replace(self::CHAPTER_TITLE, $current->chapter_name, $str);
        $str = str_replace(self::CHAPTER_NUMBER, $current->chapter_number, $str);
        $str = str_replace(self::CHAPTER_VOLUME, $current->chapter_volume, $str);
        $str = str_replace(self::PAGE_NUMBER, is_numeric($page)?0:$page->page_slug, $str);

        return trim($str);
    }

    /**
     * SEO ADV: News meta & title
     * 
     * @return string
     */
    public static function advSeoNewsPage($value, $post) {
        $str = str_replace(self::POST_TITLE, $post->title, $value);
        $str = str_replace(self::POST_CONTENT, 
                static::limitTextByChar(preg_replace('/\s+/', ' ', strip_tags($post->content)), 250), $str);
        $str = str_replace(self::POST_KEYWORDS, $post->keywords, $str);

        return trim($str);
    }
    
    public static function isValidCaptcha($input)
    {
        $rules =  ['captcha' => 'required|captcha'];
        $validation = Validator::make($input, $rules);

        if ($validation->passes()) {
            return true;
        }

        return false;
    }
}
