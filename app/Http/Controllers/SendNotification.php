<?php

class SendNotification {

    public function fire($job, $data) {
        $users = Bookmark::join('users', 'users.id', '=', 'bookmarks.user_id')
                ->join('manga', 'manga.id', '=', 'bookmarks.manga_id')
                ->select('users.email as email',
                    'users.username as username',
                    'manga.name as manga_name',
                    'manga.slug as manga_slug')
                ->where('bookmarks.manga_id', $data['mangaId'])
                ->where('users.notify', 1)
                ->get();

        foreach ($users as $user) {
            $user['chapter_number'] = $data['chapter_number'];
            $user['chapter_url'] = $data['chapter_url'];
            
            Mail::send('admin.emails.notification', compact('user'), function ($message) use ($user) {
                    $subject = $user->manga_name . ' Chapter ' . $user['chapter_number'] . ' is available';
                    if(is_null($user['chapter_number'])) {
                        $subject = $user->manga_name . ' new chapter(s) added';
                    }
                    
                    $message
                    ->to($user->email, $user->username)
                    ->subject($subject);
                }
            );
        }

        $job->delete();
    }

}
