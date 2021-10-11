@extends('front.layouts.default')

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
  </style>
@endsection

@include('front.themes.'.$theme.'.blocs.menu')

@section('allpage')
  <h2 class="listmanga-header">
    <i class="fa fa-sitemap"></i> {{ __("XML Sitemap") }}
  </h2>
  <hr/>

  <div class="col-md-12 p-0">
    <table class="table table-danger table-striped table-hover">
      <thead>
        <tr class="bg-primary">
          <th>{{ __("URL") }}</th>
          <th>{{ __("Last Modified") }}</th>
        </tr>
      </thead>
      <tbody>
        @forelse($mangaList as $manga)
          <tr>
            <td><a href="{{ route('sitemap.chapters', $manga->slug) }}">{{ route('sitemap.chapters', $manga->slug) }}</a></td>
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
      {{ $mangaList->links() }}
    </div>
  </div>
@stop
