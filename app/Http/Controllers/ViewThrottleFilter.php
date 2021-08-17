<?php

use Illuminate\Session\Store;

class ViewThrottleFilter
{
    private $session;

    public function __construct(Store $session)
    {
        // Let Laravel inject the session Store instance,
        // and assign it to our $session variable.
        $this->session = $session;
    }

    public function filter()
    {
        $manga = $this->getViewedManga();

        if ( ! is_null($manga))
        {
            $manga = $this->cleanExpiredViews($manga);

            $this->storeManga($manga);
        }
    }

    private function getViewedManga()
    {
        // Get all the viewed posts from the session. If no
        // entry in the session exists, default to null.
        return $this->session->get('viewed_manga', null);
    }

    private function cleanExpiredViews($manga)
    {
        $time = time();

        // Let the views expire after one hour.
        $throttleTime = 3600;

        // Filter through the post array. The argument passed to the
        // function will be the value from the array, which is the
        // timestamp in our case.
        return array_filter($manga, function ($timestamp) use ($time, $throttleTime)
        {
            // If the view timestamp + the throttle time is 
            // still after the current timestamp the view  
            // has not expired yet, so we want to keep it.
            return ($timestamp + $throttleTime) > $time;
        });
    }

    private function storeManga($manga)
    {
        $this->session->put('viewed_manga', $manga);
    }
}