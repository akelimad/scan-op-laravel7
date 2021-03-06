<?php

namespace App\Listeners;

use App\Events\MangaViewed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Session\Store;

class IncrementMangaViews
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    private $session;

    public function __construct(Store $session)
    {
        // Let Laravel inject the session Store instance,
        // and assign it to our $session variable.
        $this->session = $session;
    }

    /**
     * Handle the event.
     *
     * @param  MangaViewed  $event
     * @return void
     */
    public function handle(MangaViewed $event)
    {
        if ( ! $this->isMangaViewed($event->manga)) {
            $event->manga->increment('views');
            //$manga->views += 1;
            $this->storeManga($event->manga);
        }
    }

    private function isMangaViewed($manga)
    {
        $viewed = $this->session->get('viewed_manga', []);

        // Check if the post id exists as a key in the array.
        return array_key_exists($manga->id, $viewed);
    }

    private function storeManga($manga)
    {
        // First make a key that we can use to store the timestamp
        // in the session. Laravel allows us to use a nested key
        // so that we can set the post id key on the viewed_posts
        // array.
        $key = 'viewed_manga.' . $manga->id;

        // Then set that key on the session and set its value
        // to the current timestamp.
        $this->session->put($key, time());
    }
}
