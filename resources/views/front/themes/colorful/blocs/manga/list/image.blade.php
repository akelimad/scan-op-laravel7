<div class="type-content">
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="filter-text pull-left"></div>
            <div class="pull-right">
                {{ Lang::get('messages.front.directory.sort-by') }}
                <div id="sort-types" class="btn-group" role="group" data-toggle="buttons">
                    <div class="btn-group" role="group">
                        <label class="btn btn-primary active">
                            <input type="radio" name="sort-type" id="name" /> {{ Lang::get('messages.front.directory.az') }}
                        </label>
                    </div>
                    <div class="btn-group" role="group">
                        <label class="btn btn-primary">
                            <input type="radio" name="sort-type" id="views" /> {{ Lang::get('messages.front.directory.views') }}
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="filter-content">
            @include('front.themes.'.$theme.'.blocs.manga.list.filter')
        </div>
    </div>
</div>