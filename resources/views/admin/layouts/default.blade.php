<!DOCTYPE html>
<html lang="{{ App::getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">

        <title> 
            @if (Session::has('sitename'))
            {{ Lang::get('messages.admin.layout.site-name', array('sitename' => Session::get('sitename'))) }}
            @endif
        </title>

        {{ HTML::style('css/bootstrap.min.css') }}
        {{ HTML::style('css/font-awesome.min.css') }}
        {{ HTML::style('css/admin/metisMenu.min.css') }}
        {{ HTML::style('css/admin/main.css') }}

        {{ HTML::script('js/vendor/modernizr-2.6.2-respond-1.1.0.min.js') }}
        {{ HTML::script('js/vendor/jquery-1.11.0.min.js') }}
        {{ HTML::script('js/vendor/bootstrap.min.js') }}
        {{ HTML::script('js/vendor/metisMenu.min.js') }}
        {{ HTML::script('js/admin/main.js') }}

        @yield('head')

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
            <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->

    </head>

    <body>

        <div id="wrapper">

            <!-- Navigation -->
            <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="{{ route('admin.index') }}">
                        @if (Session::has('sitename'))
                        {{ Lang::get('messages.admin.layout.site-name', array('sitename' => Session::get('sitename'))) }}
                        @endif 
                    </a>
                </div>
                <!-- /.navbar-header -->

                <ul class="nav navbar-top-links navbar-right">
                    <li>Hello {{ Auth::user()->username }}</li>
                    <li>
                        <a href="{{route('front.index')}}"><i class="fa fa-home fa-fw"></i></a>
                    </li>
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-user fa-fw"></i>  <i class="fa fa-caret-down"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-user">
                            @if(Entrust::can('edit_profile'))
                            <li>
                                <a href="{{route('admin.settings.profile')}}"><i class="fa fa-user fa-fw"></i> {{ Lang::get('messages.admin.layout.user-profile') }}</a>
                            </li>
                            @endif
                            @if(Entrust::can('edit_general'))
                            <li>
                                <a href="{{route('admin.settings.general')}}"><i class="fa fa-gear fa-fw"></i> {{ Lang::get('messages.admin.layout.settings') }}</a>
                            </li>
                            @endif
                            @if(Entrust::can('edit_profile') || Entrust::can('edit_general'))
                            <li class="divider"></li>
                            @endif
                            <li>
                                <a href="{{route('admin.logout')}}"><i class="fa fa-sign-out fa-fw"></i> {{ Lang::get('messages.admin.layout.logout') }}</a>
                            </li>
                        </ul>
                        <!-- /.dropdown-user -->
                    </li>
                    @if(Entrust::can('manage_users'))
                    <li>
                        <a href="#" title="changelog" data-toggle="modal" data-target="#changelog"><i class="fa fa-info-circle fa-fw"></i></a>
                    </li>
                    @endif
                    <!-- /.dropdown -->
                </ul>
                <!-- /.navbar-top-links -->

                <div class="navbar-default sidebar" role="navigation">
                    <div class="sidebar-nav navbar-collapse">
                        <ul class="nav" id="side-menu">
                            <li>
                                <a href="{{ route('admin.index') }}">
                                    <i class="fa fa-dashboard fa-fw"></i> {{ Lang::get('messages.admin.layout.dashboard') }}
                                </a>
                            </li>
                            @if(Entrust::can('view_manga') || Entrust::can('manage_hotmanga') || Entrust::can('manage_categories'))
                            <li>
                                <a href="#">
                                    <i class="fa fa-wrench fa-fw"></i> {{ Lang::get('messages.admin.layout.manage-manga') }}<span class="fa arrow"></span>
                                </a>
                                <ul class="nav nav-second-level">
                                    @if(Entrust::can('view_manga'))
                                    <li>
                                        {{ link_to_route('admin.manga.index', Lang::get('messages.admin.layout.manga-list')) }}
                                    </li>
                                    @endif
                                    @if(Entrust::can('manage_hotmanga'))
                                    <li>
                                        {{ link_to_route('admin.manga.hot', Lang::get('messages.admin.layout.hotmanga')) }}
                                    </li>
                                    @endif
                                    @if(Entrust::can('manage_categories'))
                                    <li>
                                        {{ link_to_route('admin.category.index', Lang::get('messages.admin.layout.categories')) }}
                                    </li>
                                    @endif
                                    @if(Entrust::can('manage_categories'))
                                    <li>
                                        {{ link_to_route('admin.comictype.index', Lang::get('messages.admin.layout.comic-types')) }}
                                    </li>
                                    @endif
                                    @if(Entrust::can('edit_general'))
                                    <li>
                                        {{ link_to_route('admin.manga.options', Lang::get('messages.admin.layout.options')) }}
                                    </li>
                                    @endif
                                </ul>
                                <!-- /.nav-second-level -->
                            </li>
                            @endif
                            @if(Entrust::can('edit_general') || Entrust::can('edit_seo') || Entrust::can('edit_themes') || Entrust::can('edit_profile'))
                            <li>
                                <a href="#">
                                    <i class="fa fa-gear fa-fw"></i> {{ Lang::get('messages.admin.layout.settings') }}<span class="fa arrow"></span>
                                </a>
                                <ul class="nav nav-second-level">
                                    @if(Entrust::can('edit_general'))
                                    <li>
                                        {{ link_to_route('admin.settings.general', Lang::get('messages.admin.layout.general')) }}
                                    </li>
                                    @endif
                                    @if(Entrust::can('edit_seo'))
                                    <li>
                                        {{ link_to_route('admin.settings.seo', Lang::get('messages.admin.layout.seo')) }}
                                    </li>
                                    @endif
                                    @if(Entrust::can('edit_profile'))
                                    <li>
                                        {{ link_to_route('admin.settings.profile', Lang::get('messages.admin.layout.user-profile')) }}
                                    </li>
                                    @endif
                                    @if(Entrust::can('edit_themes'))
                                    <li>
                                        {{ link_to_route('admin.settings.theme', Lang::get('messages.admin.layout.themes')) }}
                                    </li>
                                    @endif
                                    @if(Entrust::can('edit_general'))
                                    <li>
                                        {{ link_to_route('admin.ads.index', Lang::get('messages.admin.settings.ads.manage-ads')) }}
                                    </li>
                                    @endif
                                    @if(Entrust::can('edit_general'))
                                    <li>
                                        {{ link_to_route('admin.settings.widgets', Lang::get('messages.admin.settings.widgets')) }}
                                    </li>
                                    @endif
                                    @if(Entrust::can('edit_general'))
                                    <li>
                                        {{ link_to_route('admin.settings.cache', Lang::get('messages.admin.settings.cache')) }}
                                    </li>
                                    @endif
                                    @if(Entrust::can('edit_general'))
                                    <li>
                                        {{ link_to_route('admin.settings.gdrive', Lang::get('messages.admin.settings.gdrive')) }}
                                    </li>
                                    @endif
                                </ul>
                                <!-- /.nav-second-level -->
                            </li>
                            @endif
                            @if(Entrust::can('manage_users'))
                            <li>
                                <a href="#">
                                    <i class="fa fa-users fa-fw"></i> Manage Users<span class="fa arrow"></span>
                                </a>
                                <ul class="nav nav-second-level">
                                    <li>
                                        {{ link_to_route('admin.users.permissions', 'Permissions') }}
                                    </li>
                                    <li>
                                        {{ link_to_route('admin.role.index', 'Roles') }}
                                    </li>
                                    <li>
                                        {{ link_to_route('admin.user.index', 'Users') }}
                                    </li>
                                </ul>
                                <!-- /.nav-second-level -->
                            </li>
                            @endif
                            @if(Entrust::can('manage_posts'))
                            <li>
                                <a href="#">
                                    <i class="fa fa-newspaper-o fa-fw"></i> {{Lang::get('messages.admin.posts.manage')}}<span class="fa arrow"></span>
                                </a>
                                <ul class="nav nav-second-level">
                                    <li>
                                        {{ link_to_route('admin.posts.index', Lang::get('messages.admin.posts.posts')) }}
                                    </li>
                                </ul>
                                <!-- /.nav-second-level -->
                            </li>
                            @endif
                            @if(Entrust::can('edit_general'))
                            <li style="margin: 50px 0">
                                {{ Form::open(array('route' => 'admin.settings.cache.clear', 'role' => 'form')) }}
                                <div class="form-group" style="display: table; margin: 0 auto 15px;">
                                    {{ Form::submit(Lang::get('messages.admin.settings.cache.clear'), ['class' => 'btn btn-danger submit']) }}
                                </div>
                                {{ Form::close() }}
                                {{ Form::open(array('route' => 'admin.settings.downloads.clear', 'role' => 'form')) }}
                                <div class="form-group" style="display: table; margin: 0 auto 15px;">
                                    {{ Form::submit(Lang::get('messages.admin.settings.downloads.clear'), ['class' => 'btn btn-danger submit']) }}
                                </div>
                                {{ Form::close() }}
                            </li>
                            @endif
                        </ul>
                    </div>
                    <!-- /.sidebar-collapse -->
                </div>
                <!-- /.navbar-static-side -->
            </nav>

            <!-- Page Content -->
            <div id="page-wrapper">
                <div class="container-fluid" >
                    <div class="row">
                        <div class="col-lg-12">
                            @yield('breadcrumbs')
                        </div>
                        <!-- /.col-lg-12 -->
                    </div>
                    <!-- /.row -->

                    @yield('content')

                    <div class="footer-admin">
                        <div class="powered">Powered by MangaReader CMS v1.9</div>
                    </div>
                </div>
                <!-- /.container-fluid -->
            </div>
            <!-- /#page-wrapper -->
        </div>
        <!-- /#wrapper -->
        
        <!-- Modal -->
        <div class="modal fade" id="changelog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Change Log</h4>
                    </div>
                    <div class="modal-body" style="height: 400px; overflow-x: hidden">
                        <b>Version 1.9 - 03/09/2016:</b>
                        <ul>
                            <li>Choose your reader mode: reload pages/or not</li>
                            <li>Choose your storage mode: Local server, Copy URLs, Google Drive</li>
                            <li>Built-in comment system</li>
                            <li>Adding tags to Manga + Widget</li>
                            <li>Bulk chapters/pages deletion</li>
                            <li>Adding Captcha to all public forms</li>
                            <li>Next/Previous chapter links in reader page</li>
                            <li>Show by volume in colorful theme</li>
                            <li>Fix bugs</li>
                        </ul>
                        
                        <b>Version 1.8 - 04/05/2016:</b>
                        <ul>
                            <li>Public user profil page + add avatar</li>
                            <li>Advanced SEO</li>
                            <li>Advanced Search</li>
                            <li>Filter by Author & Categories from Manga Info page</li>
                            <li>Add Widgets to FrontPage: Top by views, custom codes, ...</li>
                            <li>Replace broken Spanish scraper</li>
                            <li>Script optimization: Cache chapters (configured in settings), ...</li>
                            <li>Fix bugs</li>
                        </ul>
                        
                        <b>Version 1.7 - 04/02/2016:</b>
                        <ul>
                            <li>Notification of updated bookmarked Manga</li>
                            <li>Adding Articles</li>
                            <li>Adding comic types & artist in Manga creation</li>
                            <li>Comment system: Facebook & Disqus</li>
                            <li>French & Indonisian scraper</li>
                            <li>Italian Language (tnx to Araragi for the trad)</li>
                            <li>Auto get info & bulk scraper for other languages</li>
                            <li>Move/Re-order pages of a chapter</li>
                            <li>More options (General & Manga Option Menu): show by volume, edit menu, pagination, Reader default mode</li>
                            <li>latest release & news page</li>
                            <li>Feed at domain.com/feed</li>
                            <li>Report broken page</li>
                            <li>Contact us page</li>
                            <li>Script optimization: Caching & Reader (no load of all the site)</li>
                            <li>Fix bugs</li>
                        </ul>
                        
                        <b>Version 1.6 - 14/11/2015:</b>
                        <ul>
                            <li>Download all chapters at once click from mangapanda.com & mangareader.net (stop/resume options)</li>
                            <li>Manage Ads / Ads placement</li>
                            <li>Add mangareader.net scraper</li>
                            <li>Add comicvn.net (Vietnam) scraper</li>
                            <li>Download chapters as ZIP from Manga Info Page</li>
                        </ul>
                      
                        <b>Version 1.5 - 12/10/2015:</b>
                        <ul>
                            <li>Bookmarking system.</li>
                            <li>Auto get Info from Mangapanda on creation process.</li>
                            <li>Improve the Reader (easily navigate between chapters).</li>
                            <li>Fix Bugs.
                        </ul>

                        <b>Version 1.4 - 09/14/2015:</b>
                        <ul>
                            <li>Changing Manga List (see list by image or text, filtring and sorting).</li>
                            <li>Manga View Counter.</li>
                        </ul>

                        <b>Version 1.3 - 09/11/2015:</b> 
                        <ul>
                            <li>Fix Bugs.</li>
                        </ul>

                        <b>Version 1.2 - 09/09/2015:</b>
                        <ul>
                            <li>User Management System (Manage users, roles, permissions and subscription).</li>
                            <li>Bulk chapters adder.</li>
                            <li>Use external images source.</li>
                            <li>RTL language support.</li>
                            <li>Change reading mode of chapter (Page by Page / All in one Page).</li>
                            <li>Flag violent content.</li>
                            <li>Adding Arabic language.</li>
                        </ul>
                        
                        <b>Version 1.1 - 08/29/2015:</b>
                        <ul>
                            <li>Fix Bugs.
                            <li>Supporting more languages: French and Spanish in addition to English.</li>
                            <li>Add your own language.</li>
                            <li>Random Manga button.</li>
                            <li>Search Manga.</li>
                            <li>Automatic creation of a chapter (name and images).</li>
                            <li>New source for Automatic creation for Spanish people.</li>
                        </ul>
                        
                        <b>Version 1.0 - 08/20/2015:</b>
                        <ul>
                            <li>Initial release.</li>
                        </ul>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
