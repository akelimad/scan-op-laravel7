@if(count($bookmarks) > 0)
<div class="panel panel-default" style="margin-top: 15px;">
  <div class="panel-heading">
      <input type="checkbox" name="check-all" class="all" value="all"/>
      <input class="btn btn-danger btn-xs pull-right delete" type="button" value="{{Lang::get('messages.admin.users.delete')}}"/>
      <div class="pull-right" style="border-right: 1px solid #ccc; padding-right: 30px; margin-right: 30px;">
      <select style="width: 200px; vertical-align: middle;">
          <option value="currently-reading">{{Lang::get('messages.front.bookmarks.currently-reading')}}</option>
          <option value="completed">{{Lang::get('messages.front.bookmarks.completed')}}</option>
          <option value="on-hold">{{Lang::get('messages.front.bookmarks.on-hold')}}</option>
          <option value="plan-to-read">{{Lang::get('messages.front.bookmarks.plan-to-read')}}</option>
      </select>
      <input class="btn btn-default btn-xs move" type="button" value="{{Lang::get('messages.front.bookmarks.move')}}"/>
      </div>
  </div>
</div>

<table class="table table-hover">
    <thead>
        <th></th>
        <th></th>
        <th>{{Lang::get('messages.front.bookmarks.bookmark-at')}}</th>
        <th></th>
    </thead>
    @foreach($bookmarks as $bookmark)
    <tr>
        <td colspan="2">
            <input type="checkbox" value="{{$bookmark['manga_id']}}"/>
            <a href="{{route('front.manga.show', $bookmark['manga_slug'])}}" style="margin-right: 20px;"><strong>{{$bookmark['manga_name']}}</strong></a>
            <i class="fa fa-star"></i>
            {{ link_to("/manga/".$bookmark['manga_slug']."/".$bookmark['last_chapter']->slug, "#".$bookmark['last_chapter']->number." ".$bookmark['last_chapter']->name) }}
        </td>
        <td>{{$bookmark['created_at']}}</td>
        <td style="text-align: right;">
            <div style="display: inline-block">
                {{ Form::open(array('route' => array('bookmark.destroy', $bookmark['manga_id']), 'method' => 'delete')) }}
                {{ Form::hidden('rootBookmark', 'true')}}
                {{ Form::submit(Lang::get('messages.admin.users.delete'), array('class' => 'btn btn-danger btn-xs',  'onclick' => 'if (!confirm("'.Lang::get('messages.front.bookmarks.confirm-delete').'")) {return false;}')) }}
                {{ Form::close() }}
            </div>
        </td>
    </tr>
    @foreach($bookmark['chapters'] as $chapter)
    @if(!is_null($chapter['chapter']))
    <tr>
        <td style="width: 50px;"></td>
        <td>
            <i class="fa fa-book"></i>
            {{ link_to("/manga/".$bookmark['manga_slug']."/".$chapter['chapter']->slug, "#".$chapter['chapter']->number." ".$chapter['chapter']->name) }}
            @if($chapter['page_id'] != '1') - {{ link_to("/manga/".$bookmark['manga_slug']."/".$chapter['chapter']->slug."/".$chapter['page_id'], "Page ".$chapter['page_id'])}}@endif
        </td>
        <td>{{$chapter['created_at']}}</td>
        <td style="text-align: right;">
            <div style="display: inline-block">
                {{ Form::open(array('route' => array('bookmark.destroy', $chapter['id']), 'method' => 'delete')) }}
                {{ Form::submit(Lang::get('messages.admin.users.delete'), array('class' => 'btn btn-danger btn-xs',  'onclick' => 'if (!confirm("'.Lang::get('messages.front.bookmarks.confirm-delete').'")) {return false;}')) }}
                {{ Form::close() }}
            </div>
        </td>
    </tr>
    @endif
    @endforeach
    @endforeach
</table>
@else
<div class="text-center" style="padding-top: 25px;">
    {{Lang::get('messages.front.bookmarks.no-bookmark')}}
</div>
@endif
