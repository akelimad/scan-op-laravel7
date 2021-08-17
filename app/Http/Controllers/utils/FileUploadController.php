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
class FileUploadController extends BaseController
{

    /**
     * Upload cover
     * 
     * @return type
     */
    public function uploadMangaCover()
    {
        $destinationPath = 'uploads/tmp/mangacover/';

        if (!File::isDirectory($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }

        $file = Input::file('file');
        if ($file) {
            $cover_250x350 = 'cover_250x350_' . time() . '.jpg';


            // GD API
            $image = HelperController::openImage($file);

            if ($image === false) {
                return Response::json(
                    ['error' =>
                        [
                            'type' => 'Unable to open image',
                            'message' => 'extension not supported'
                        ]
                    ]
                    , 400
                );
            }

            HelperController::makeThumb($image, $destinationPath . $cover_250x350, 250, 350);

            
            // Intervention API
            // $img = Image::make($file);
            // $img->resize(250, 350)->save($destinationPath . $cover_250x350);

            return Response::json(
                ['result' => asset($destinationPath . $cover_250x350)]
            );
        } else {
            return Response::json('error', 400);
        }
    }

    /**
     * Upload pages
     * 
     * @return type
     */
    public function uploadMangaChapterPage()
    {
        $mangaId = $_GET['manga'];
        $destinationPath = 'uploads/tmp/mangachapter/' . $mangaId . '/';

        $file = Input::file('file');
        if ($file) {
            $filename = $file->getClientOriginalName();

            $upload_success = $file->move($destinationPath, $filename);
            if ($upload_success) {
                return Response::json(
                    ['result' => asset($destinationPath . $filename)]
                );
            } else {
                return Response::json('error', 400);
            }
        } else {
            return Response::json('error', 400);
        }
    }

    /**
     * Delete image
     * 
     * @return type
     */
    public function deleteImage()
    {
        $mangaId = $_POST['manga'];
        $filename = $_POST['filename'];

        $destinationPath = 'uploads/tmp/mangachapter/' . $mangaId . '/';

        if (File::isDirectory($destinationPath)) {
            $stat = File::delete($destinationPath . $filename);
            return Response::json(['result' => $stat]);
        } else {
            return Response::json('error', 400);
        }
    }

    /**
     * Delete cover
     * 
     * @return type
     */
    public function deleteCover()
    {
        $filename = $_POST['filename'];

        $destinationPath = 'uploads/tmp/mangacover/';

        if (File::isDirectory($destinationPath)) {
            $stat = File::delete($destinationPath . $filename);
            return Response::json(['result' => $stat]);
        } else {
            return Response::json('error', 400);
        }
    }

    /**
     * Download Image by url
     * 
     * @return type
     */
    public function downloadImageFromUrl()
    {
        $imageUrl = $_POST['scanURL'];
        $index = $_POST['index'];

        $fileExtension = strrchr($imageUrl, '.');
        $destinationPath = 'uploads/tmp/mangacover/' . $index . $fileExtension;

        try {
            $ch = curl_init($imageUrl);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
            $rawdata = curl_exec($ch);
            curl_close($ch);

            if ($rawdata) {
                $fp = fopen($destinationPath, 'w');
                fwrite($fp, $rawdata);
                fclose($fp);
                return Response::json(
                    ['index' => $index, 'path' => URL::asset($destinationPath)]
                );
            } else {
                return Response::json(
                    [
                        'error' => curl_error($ch), 
                        'index' => $index, 
                        'url' => $imageUrl
                    ]
                );
            }
        } catch (Exception $e) {
            return Response::json(
                [
                    'error' => $e->getMessage(), 
                    'index' => $index, 
                    'url' => $imageUrl
                ], 
                400
            );
        }
    }

    /**
     * Upload avatar
     * 
     * @return type
     */
    public function uploadAvatar()
    {
        $destinationPath = 'uploads/tmp/avatar/';

        if (!File::isDirectory($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }

        $file = Input::file('file');
        if ($file) {
            $avatar = 'avatar' . time() . '.jpg';

            // GD API
            $image = HelperController::openImage($file);

            if ($image === false) {
                return Response::json(
                    ['error' =>
                        [
                            'type' => 'Unable to open image',
                            'message' => 'extension not supported'
                        ]
                    ]
                    , 400
                );
            }

            HelperController::makeThumb($image, $destinationPath . $avatar, 200, 200);

            return Response::json(
                ['result' => asset($destinationPath . $avatar)]
            );
        } else {
            return Response::json('error', 400);
        }
    }
    
    /**
     * Delete avatar
     * 
     * @return type
     */
    public function deleteAvatar()
    {
        $filename = $_POST['filename'];

        $destinationPath = 'uploads/tmp/avatar/';

        if (File::isDirectory($destinationPath)) {
            $stat = File::delete($destinationPath . $filename);
            return Response::json(['result' => $stat]);
        } else {
            return Response::json('error', 400);
        }
    }
}
