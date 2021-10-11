@extends('front.layouts.colorful')

@section('title')
  {{$settings['seo.title']}} | {{ Lang::get('messages.front.home.contact-us') }}
@stop

@section('description')
  {{ Lang::get('messages.front.home.contact-us') }}
@stop

@section('keywords')
  {{ $settings['seo.keywords'] }}
@stop

@section("header")
  {!! htmlScriptTagJsApi() !!}
  <style>
    a {
      text-decoration: none !important;
    }
    a:hover {
      text-decoration: underline !important;
    }
    a.goback-link:hover {
      color: white !important;
      text-decoration: none !important;
    }
  </style>
@endsection

@include('front.themes.'.$theme.'.blocs.menu')

@section('allpage')
  <h2 class="listmanga-header">
    <i class="fa fa-sitemap"></i> {{ __("XML Sitemap | :manga", ['manga' => $manga->name]) }}
  </h2>
  <hr/>

  <div class="col-md-12 p-0">
    <p><a href="{{ route('sitemap.mangas') }}" class="btn btn-primary p-10 ml-0 goback-link"><i class="fa fa-long-arrow-left"></i> {{ __("Sitemap index") }}</a></p>
    <table class="table bg-info table-striped table-hover">
      <thead>
        <tr class="bg-primary">
          <th>{{ __("URL") }}</th>
          <th>{{ __("Last Modified") }}</th>
        </tr>
      </thead>
      <tbody>
        @forelse($mangaChapterList as $chapter)
          <tr>
            <td>{{ link_to("/manga/$manga->slug/$chapter->slug", route('front.manga.reader', ['manga'=>$manga->slug, 'chapter'=>$chapter->slug])) }}</td>
            <td>{{ date(DATE_W3C) }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="2" class="text-center">{{ __("Aucun résultat trouvé !") }}</td>
          </tr>
        @endforelse
      </tbody>
    </table>
    <div class="navigation">
      {{ $mangaChapterList->links() }}
    </div>
  </div>
@stop
