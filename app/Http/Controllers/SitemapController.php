<?php

namespace App\Http\Controllers;

use App\Chapter;
use App\Manga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
  public function index() {
    $theme = Cache::get('theme', "default");
    $settings = Cache::get('options');
    $variation = Cache::get('variation');

    $mangaList = Manga::orderBy('created_at', 'desc')->paginate(50);

    return view('front.themes.' . $theme . '.sitemap.mangas', [
      'theme' => $theme,
      'settings' => $settings,
      'variation' => $variation,
      'mangaList' => $mangaList
    ]);
  }

  public function mangaChapters($mangaSlug) {
    $theme = Cache::get('theme', "default");
    $settings = Cache::get('options');
    $variation = Cache::get('variation');
    $manga = Manga::where('slug', $mangaSlug)->firstOrFail();
    $mangaChapterList = Chapter::where('manga_id', '=', $manga->id)->orderBy('created_at', 'desc')->paginate(50);

    return view('front.themes.' . $theme . '.sitemap.chapters', [
      'theme' => $theme,
      'settings' => $settings,
      'variation' => $variation,
      'manga' => $manga,
      'mangaChapterList' => $mangaChapterList
    ]);
  }
}
