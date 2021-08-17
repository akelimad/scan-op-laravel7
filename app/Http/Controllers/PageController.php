<?php

use Cyberziko\Gdrive\Controllers\GoogleController;

/**
 * Page Controller Class
 * 
 * PHP version 5.4
 *
 * @category PHP
 * @package  Controller
 * @author   cyberziko <cyberziko@gmail.com>
 * @license  commercial http://getcyberworks.com/
 * @link     http://getcyberworks.com/
 */
class PageController extends BaseController
{

    /**
     * The create page
     * 
     * @param type $mangaId   manga id
     * @param type $chapterId chapter id
     * 
     * @return view
     */
    public function create($mangaId, $chapterId)
    {
        $manga = Manga::find($mangaId);
        $chapter = Chapter::find($chapterId);

        return View::make(
            'admin.manga.chapter.page.create',
            ['manga' => $manga, 'chapter' => $chapter]
        );
    }

    /**
     * Create page
     * 
     * @param type $mangaId   manga id
     * @param type $chapterId chapter id
     * 
     * @return view
     */
    public function store($mangaId, $chapterId)
    {
        $manga = Manga::find($mangaId);
        $chapter = Chapter::find($chapterId);
        $lastPage = $chapter->lastPage();
        $settings = Cache::get('options');

        $counter = 0;
        if (!is_null($lastPage)) {
            $counter = $lastPage->slug;
        }

        $destinationPath = 'uploads/manga/'
                . $manga->slug . '/chapters/' . $chapter->slug . '/';

        $file = Input::file('file');

        if ($file) {
            $counter++;
            $fileExtension = strrchr($file->getClientOriginalName(), '.');

            if ($counter < 10) {
                $newName = '0' . $counter . $fileExtension;
            } else {
                $newName = '' . $counter . $fileExtension;
            }

            // gdrive
            if ($settings['storage.type'] == 'gdrive') {                
                if(class_exists('\Cyberziko\Gdrive\Controllers\GoogleController')) {
                    GoogleController::createGDriveFile($manga->slug, $chapter, $file, $counter);
                    
                    return Response::json(
                        ['result' => asset($destinationPath . $newName)]
                    );
                } else {
                    return Response::json('error', 400);
                }
            } else {
                $upload_success = $file->move($destinationPath, $newName);
                if ($upload_success) {
                    $page = new Page();
                    $page->image = $newName;
                    $page->slug = $counter;
                    $page->save();
                    $chapter->pages()->save($page);
                    return Response::json(
                                    ['result' => asset($destinationPath . $newName)]
                    );
                } else {
                    return Response::json('error', 400);
                }
            }
        } else {
            return Response::json('error', 400);
        }
    }

    /**
     * Delete page
     * 
     * @param type $mangaId   manga id
     * @param type $chapterId chapter id
     * @param type $pageId    page id
     * 
     * @return view
     */
    public function destroy($mangaId, $chapterId, $pageId)
    {
        $page = Page::find($pageId);
        $manga = Manga::find($mangaId);
        $chapter = Chapter::find($chapterId);

	if($page->external === 0) {
            $destinationPath = 'uploads/manga/' 
                . $manga->slug . '/chapters/' . $chapter->slug . '/';

            if (File::isDirectory($destinationPath)) {
                if (File::exists($destinationPath . $page->image)) {
                    File::delete($destinationPath . $page->image);
                }
                $page->delete();
            }
        } else {
            $page->delete();
        }
        
        foreach($chapter->pages as $index => $pg) {
            $index = $index + 1;
            
            if($page->external === 0) {
                $fileExtension = substr($pg->image, strpos($pg->image, '.'));
                $pageName="";

                if ($index < 10) {
                    $pageName = '0' . $index . $fileExtension;
                } else {
                    $pageName = '' . $index . $fileExtension;
                }
            
                if (File::isDirectory($destinationPath)) {
                    $newPath = $destinationPath . $pageName;
                    if (File::exists($destinationPath . $pg->image)) {
                        File::move($destinationPath . $pg->image, $newPath);
                    }
                }

                $pg->image = $pageName;
            }
            
            $pg->slug = $index;
            $pg->save();
        }
        
        return Redirect::route(
            'admin.manga.chapter.show', 
            ['mangaId' => $mangaId, 'chapterId' => $chapter->id]
        );
    }
    
    public function destroyPages($mangaId, $chapterId)
    {
        $ids = Input::get("pages-ids");
        $manga = Manga::find($mangaId);
        $chapter = Chapter::find($chapterId);

        if (strlen(trim($ids)) > 0) {
            $tab_ids = explode(',', $ids);

            foreach ($tab_ids as $id) {
                if ($id != 'all') {
                    $page = Page::find($id);
                     
                    if ($page->external === 0) {
                        $destinationPath = 'uploads/manga/'
                                . $manga->slug . '/chapters/' . $chapter->slug . '/';

                        if (File::isDirectory($destinationPath)) {
                            if (File::exists($destinationPath . $page->image)) {
                                File::delete($destinationPath . $page->image);
                            }
                            $page->delete();
                        }
                    } else {
                        $page->delete();
                    }
                }
            }
        }

        // reorder indexs
        foreach($chapter->pages as $index => $pg) {
            $index = $index + 1;
            
            if($page->external === 0) {
                $fileExtension = substr($pg->image, strpos($pg->image, '.'));
                $pageName="";

                if ($index < 10) {
                    $pageName = '0' . $index . $fileExtension;
                } else {
                    $pageName = '' . $index . $fileExtension;
                }
            
                if (File::isDirectory($destinationPath)) {
                    $newPath = $destinationPath . $pageName;
                    if (File::exists($destinationPath . $pg->image)) {
                        File::move($destinationPath . $pg->image, $newPath);
                    }
                }

                $pg->image = $pageName;
            }
            
            $pg->slug = $index;
            $pg->save();
        }
        
        return Redirect::route(
            'admin.manga.chapter.show', 
            ['mangaId' => $mangaId, 'chapterId' => $chapter->id]
        );
    }
    
    /**
     * Move page
     * @return type
     */
    public function movePage()
    {
        $index = (int) filter_input(INPUT_POST, 'index');
        $mangaSlug = filter_input(INPUT_POST, 'mangaSlug');
        $chapterId = filter_input(INPUT_POST, 'chapterId');
        $position = filter_input(INPUT_POST, 'position');
        
        $chapter = Chapter::find($chapterId);
        $pages = $chapter->pages;
        
        $page = $pages[$index];
        if($position == 'up') {
            $page->slug = $page->slug - 1;
        } else if($position == 'down') {
            $page->slug = $page->slug + 1;
        }
        $fileExtension = substr($page->image, strpos($page->image, '.'));
        $pageName="";

        if ($page->slug < 10) {
            $pageName = '0' . $page->slug . $fileExtension;
        } else {
            $pageName = '' . $page->slug . $fileExtension;
        }
        
        if($position == 'up') {
            $page2 = $pages[$index-1];
            $page2->slug = $page2->slug + 1;
        } else if($position == 'down') {
            $page2 = $pages[$index+1];
            $page2->slug = $page2->slug - 1;
        }
        
        $fileExtension2 = substr($page2->image, strpos($page2->image, '.'));
        $pageName2="";

        if ($page2->slug < 10) {
            $pageName2 = '0' . $page2->slug . $fileExtension2;
        } else {
            $pageName2 = '' . $page2->slug . $fileExtension2;
        }
            
        $destinationPath = 'uploads/manga/' 
            . $mangaSlug . '/chapters/' . $chapter->slug . '/';

        if (File::isDirectory($destinationPath)) {
            $newPath = $destinationPath . $pageName;
            $newPath2 = $destinationPath . $pageName2;
            $newPathT = $destinationPath . 'Transient';
            if (File::exists($destinationPath . $page2->image)) {
                File::move($destinationPath . $page2->image, $newPathT);
            }
            if (File::exists($destinationPath . $page->image)) {
                File::move($destinationPath . $page->image, $newPath);
            }
            if (File::exists($newPathT)) {
                File::move($newPathT, $newPath2);
            }
        }

        $page->image = $pageName;
        $page->save();
        $page2->image = $pageName2;
        $page2->save();
        
        return Response::json(['p1' => $page, 'p2' => $page2]);
    }
 
    /**
     * Download Image by URL
     * 
     * @return type
     */
    public function downloadImageFromUrl()
    {
        $settings = Cache::get('options');
        $imageUrl = $_POST['scanURL'];
        $index = $_POST['index'];
        $mangaSlug = $_POST['mangaSlug'];
        $chapterId = $_POST['chapterId'];

        $chapter = Chapter::find($chapterId);

        if ($settings['storage.type'] == 'gdrive') {
            if(class_exists('\Cyberziko\Gdrive\Controllers\GoogleController')) {
                $fileId = GoogleController::createGDriveFile($mangaSlug, $chapter, $imageUrl, $index, "scraper");
                $name = substr($imageUrl, strrpos($imageUrl, "/") + 1);
                    
                return Response::json(
                            ['index' => $index,
                                'path' => GoogleController::$webLinkPatern . $fileId,
                                'filename' => $name,
                                'url' => $imageUrl,]
                );
            } else {
                return Response::json('error', 400);
            }
        } else if ($settings['storage.type'] == 'mirror') {
            $page = Page::where('slug', '=', $index)
                            ->where('chapter_id', '=', $chapter->id)->first();
            if (is_null($page)) {
                $page = new Page();
                $page->image = $imageUrl;
                $page->slug = $index;
                $page->external = 1;
                $page->save();
                $chapter->pages()->save($page);
            } else {
                $page->image = $imageUrl;
                $page->slug = $index;
                $page->external = 1;
                $page->save();
            }

            return Response::json(
                            ['index' => $index,
                                'path' => $imageUrl,
                                'filename' => "",
                                'url' => $imageUrl,]
            );
        } else {
            $fileExtension = strtolower(pathinfo($imageUrl, PATHINFO_EXTENSION));
            if ($fileExtension == '') {
                $fileExtension = "jpg";
            }

            if ($index < 10) {
                $pageName = '0' . $index . '.' . $fileExtension;
            } else {
                $pageName = '' . $index . '.' . $fileExtension;
            }

            $destinationPath = 'uploads/manga/'
                    . $mangaSlug . '/chapters/' . $chapter->slug . '/';

            try {
                $ch = curl_init($imageUrl);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                //curl_setopt($ch, CURLOPT_USERAGENT, "Google Mozilla/5.0 (compatible; Googlebot/2.1;)");
                //curl_setopt($ch, CURLOPT_REFERER, "http://www.google.com/bot.html");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
                $rawdata = curl_exec($ch);
                curl_close($ch);

                if ($rawdata) {
                    if (!File::isDirectory($destinationPath)) {
                        File::makeDirectory($destinationPath, 0755, true);
                    }

                    $fp = fopen($destinationPath . $pageName, 'w');
                    $stat = fwrite($fp, $rawdata);
                    fclose($fp);
                    if ($stat) {
                        $page = Page::where('slug', '=', $index)
                                        ->where('chapter_id', '=', $chapter->id)->first();
                        if (is_null($page)) {
                            $page = new Page();
                            $page->image = $pageName;
                            $page->slug = $index;
                            $page->save();
                            $chapter->pages()->save($page);
                        } else {
                            $page->image = $pageName;
                            $page->slug = $index;
                            $page->save();
                        }

                        return Response::json(
                                        ['index' => $index,
                                            'path' => URL::asset($destinationPath . $pageName),
                                            'filename' => $pageName,
                                            'url' => '/' . $mangaSlug . '/' . $chapter->slug . '/' . $index,]
                        );
                    } else {
                        return Response::json(
                                        ['error' => 'error saving the file.',
                                    'index' => $index,
                                    'url' => $imageUrl], 400
                        );
                    }
                } else {
                    return Response::json(
                                    ['error' => curl_error($ch),
                                'index' => $index,
                                'url' => $imageUrl], 400
                    );
                }
            } catch (Exception $e) {
                return Response::json(
                                ['error' => $e->getMessage(),
                            'index' => $index,
                            'url' => $imageUrl], 400
                );
            }
        }
    }

    /**
     * Upload Zip file
     * 
     * @return type
     */
    public function uploadZIPFile()
    {
        try {
            $mangaSlug = Input::get('mangaSlug');
            $chapter = Chapter::find(Input::get('chapterId'));
            $destinationPath = 'uploads/manga/' 
                . $mangaSlug . '/chapters/' . $chapter->slug . '/';

            $file = Input::file('zipfile');
            if ($file) {
                if ("zip" == $file->getClientOriginalExtension()) {
                    $filename = $file->getClientOriginalName();

                    $newFile = $file->move($destinationPath, $filename);

                    $filesname = $this->zipExtract(
                        $newFile->getPathname(),
                        $destinationPath
                    );
                    File::delete($newFile);

                    if (count($filesname) > 0) {
                        $settings = Cache::get('options');
                        if ($settings['storage.type'] == 'gdrive') {
                            if(class_exists('\Cyberziko\Gdrive\Controllers\GoogleController')) {
                                GoogleController::createGDriveFile($mangaSlug, $chapter, $filesname, $destinationPath, "zip");
                            } else {
                                return Redirect::back()
                                    ->with('uploadError', 'GDrive not configured!');
                            }            
                        } else {
                            foreach ($filesname as $key => $name) {
                                $page = new Page();
                                $page->image = $name;
                                $page->slug = $key + 1;
                                $page->save();
                                $chapter->pages()->save($page);
                            }
                        }
                    }

                    return Redirect::back()
                        ->with('uploadSuccess', Lang::get('messages.admin.chapter.page.create-success'));
                } else {
                    return Redirect::back()
                        ->with('uploadError', Lang::get('messages.admin.chapter.page.select-zip-error'));
                }
            } else {
                return Redirect::back()
                    ->with('uploadError', Lang::get('messages.admin.chapter.page.select-file-error'));
            }
        } catch (Exception $e) {
            return Redirect::back()->with('uploadError', $e->getMessage());
        }
    }

    /**
     * Extract Zip file
     * 
     * @param type $file        zip file
     * @param type $extractPath path
     * 
     * @return array
     */
    public function zipExtract($file, $extractPath)
    {
        $zip = new ZipArchive;
        $files = array();

        $res = $zip->open($file);
        if ($res === true) {
            if ($zip->extractTo($extractPath)) {
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $stat = $zip->statIndex($i);
                    array_push($files, basename($stat['name']));
                }
            }

            $zip->close();
        }

        sort($files);
        return $files;
    }

    /**
     * Create page with external url
     *  
     * @return view
     */
    public function createExternalPages()
    {
    	//$mangaId = filter_input(INPUT_POST, 'mangaId');
        $chapterId = filter_input(INPUT_POST, 'chapterId');
        $urls = filter_input(INPUT_POST, 'urls');

        if(strlen($urls)>0) {
            //$manga = Manga::find($mangaId);
            $chapter = Chapter::find($chapterId);
            $images = explode(';', $urls);

            $lastPage = $chapter->lastPage();

            $counter = 0;
            if (!is_null($lastPage)) {
                $counter = $lastPage->slug;
            }

            for ($i=0; $i < count($images); $i++) { 
                $counter++;

                $page = new Page();
                $page->image = $images[$i];
                $page->external = 1;
                $page->slug = $counter;
                $page->save();
                $chapter->pages()->save($page);
            }

            return Response::json(
                ['result' => 'success']
            );
        }
    }
}

