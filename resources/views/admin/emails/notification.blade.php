<p>{{ Lang::get('messages.email.notif.greetings', array('name' => (isset($user['username'])) ? $user['username'] : $user['email'])) }},</p>

<p>
    @if(is_null($user['chapter_number']))
    {{ Lang::get('messages.email.notif.body.bulk-update', ['manga' => $user['manga_name']]) }}
    <a href="{{$user['chapter_url']}}">
        {{ $user['manga_name'] }} Page
    </a>
    @else
    {{ Lang::get('messages.email.notif.body', ['manga' => $user['manga_name'], 'chapter' => $user['chapter_number']]) }}
    <a href="{{$user['chapter_url']}}">
        {{ $user['manga_name'].' #'.$user['chapter_number'] }}
    </a>
    @endif
</p>

<p>{{ Lang::get('messages.email.notif.best-regards') }}</p>
