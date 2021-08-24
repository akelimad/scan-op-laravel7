<?php

use DaveJamesMiller\Breadcrumbs\Facades\Breadcrumbs;
use Illuminate\Support\Facades\Lang;

Breadcrumbs::register(
    'admin.index',
    function ($breadcrumbs) {
        $breadcrumbs->push(
            Lang::get('messages.admin.layout.dashboard'), 
            route('admin.index')
        );
    }
);

Breadcrumbs::register(
    'admin.manga.index',
    function ($breadcrumbs) {
        $breadcrumbs->push(
            Lang::get('messages.admin.layout.manage-manga'), 
            route('manga.index')
        );
    }
);

Breadcrumbs::register(
    'admin.manga.show',
    function ($breadcrumbs, $manga) {
        $breadcrumbs->parent('admin.manga.index');
        $breadcrumbs->push($manga->name, route('manga.show', $manga->id));
    }
);

Breadcrumbs::register(
    'admin.manga.edit', 
    function ($breadcrumbs, $manga) {
        $breadcrumbs->parent('admin.manga.show', $manga);
        $breadcrumbs->push(
            Lang::get('messages.admin.manga.edit'), 
            route('manga.edit', $manga->id)
        );
    }
);

Breadcrumbs::register(
    'manga.create', 
    function ($breadcrumbs) {
        $breadcrumbs->parent('admin.manga.index');
        $breadcrumbs->push(
            Lang::get('messages.admin.manga.create'), 
            route('manga.create')
        );
    }
);

Breadcrumbs::register(
    'admin.manga.chapter.create', 
    function ($breadcrumbs, $manga) {
        $breadcrumbs->parent('admin.manga.show', $manga);
        $breadcrumbs->push(
            Lang::get('messages.admin.chapter.create'), 
            route('manga.chapter.create', [$manga])
        );
    }
);

Breadcrumbs::register(
    'admin.manga.chapter.show', 
    function ($breadcrumbs, $manga, $chapter) {
        $breadcrumbs->parent('admin.manga.show', $manga);
        $breadcrumbs->push(
            Lang::get('messages.admin.chapter.edit', array('number' => $chapter->number)),
            route(
                'manga.chapter.show', 
                array($manga->id, $chapter->id)
            )
        );
    }
);

Breadcrumbs::register(
    'admin.manga.chapter.page.create', 
    function ($breadcrumbs, $manga, $chapter) {
        $breadcrumbs->parent('admin.manga.chapter.show', $manga, $chapter);
        $breadcrumbs->push(
            Lang::get('messages.admin.chapter.edit.add-pages'),
            route('manga.chapter.page.create', [$manga->id, $chapter->id])
        );
    }
);

Breadcrumbs::register(
    'admin.manga.hot',
    function ($breadcrumbs) {
        $breadcrumbs->parent('admin.manga.index');
        $breadcrumbs->push(Lang::get('messages.admin.layout.hotmanga'));
    }
);

Breadcrumbs::register(
    'admin.category.index',
    function ($breadcrumbs) {
        $breadcrumbs->parent('admin.manga.index');
        $breadcrumbs->push(Lang::get('messages.admin.layout.categories'));
    }
);

Breadcrumbs::register(
    'admin.category.edit',
    function ($breadcrumbs) {
        $breadcrumbs->parent('admin.manga.index');
        $breadcrumbs->push(Lang::get('messages.admin.layout.categories'));
    }
);

Breadcrumbs::register(
    'admin.comictype.index',
    function ($breadcrumbs) {
        $breadcrumbs->parent('admin.manga.index');
        $breadcrumbs->push(Lang::get('messages.admin.layout.comic-types'));
    }
);

Breadcrumbs::register(
    'admin.comictype.edit',
    function ($breadcrumbs) {
        $breadcrumbs->parent('admin.manga.index');
        $breadcrumbs->push(Lang::get('messages.admin.layout.comic-types'));
    }
);

Breadcrumbs::register(
    'admin.manga.options',
    function ($breadcrumbs) {
        $breadcrumbs->parent('admin.manga.index');
        $breadcrumbs->push(Lang::get('messages.admin.layout.options'));
    }
);

Breadcrumbs::register(
    'admin.settings.general', 
    function ($breadcrumbs) {
        $breadcrumbs->push(Lang::get('messages.admin.settings'));
	$breadcrumbs->push(Lang::get('messages.admin.settings.general'));
    }
);

Breadcrumbs::register(
    'admin.settings.seo', 
    function ($breadcrumbs) {
        $breadcrumbs->push(Lang::get('messages.admin.settings'));
	$breadcrumbs->push(Lang::get('messages.admin.settings.seo'));
    }
);

Breadcrumbs::register(
    'admin.settings.profile', 
    function ($breadcrumbs) {
        $breadcrumbs->push(Lang::get('messages.admin.settings'));
		$breadcrumbs->push(Lang::get('messages.admin.settings.profile'));
    }
);

Breadcrumbs::register(
    'admin.settings.theme', 
    function ($breadcrumbs) {
        $breadcrumbs->push(Lang::get('messages.admin.settings'));
        $breadcrumbs->push(Lang::get('messages.admin.settings.theme'));
    }
);

Breadcrumbs::register(
    'admin.settings.ads', 
    function ($breadcrumbs) {
        $breadcrumbs->push(Lang::get('messages.admin.settings'));
        $breadcrumbs->push(Lang::get('messages.admin.settings.ads.manage-ads'));
    }
);

Breadcrumbs::register(
    'admin.settings.widgets', 
    function ($breadcrumbs) {
        $breadcrumbs->push(Lang::get('messages.admin.settings'));
        $breadcrumbs->push(Lang::get('messages.admin.settings.widgets'));
    }
);

Breadcrumbs::register(
    'admin.settings.cache', 
    function ($breadcrumbs) {
        $breadcrumbs->push(Lang::get('messages.admin.settings'));
        $breadcrumbs->push(Lang::get('messages.admin.settings.cache'));
    }
);

Breadcrumbs::register(
    'admin.settings.gdrive', 
    function ($breadcrumbs) {
        $breadcrumbs->push(Lang::get('messages.admin.settings'));
        $breadcrumbs->push(Lang::get('messages.admin.settings.gdrive'));
    }
);

Breadcrumbs::register(
    'user.index',
    function ($breadcrumbs) {
        $breadcrumbs->push(Lang::get('messages.admin.users.manage-users'));
        $breadcrumbs->push(Lang::get('messages.admin.users.users'), route('user.index'));
    }
);

Breadcrumbs::register(
    'user.create',
    function ($breadcrumbs) {
        $breadcrumbs->parent('user.index');
        $breadcrumbs->push(Lang::get('messages.admin.users.create'));
    }
);

Breadcrumbs::register(
    'user.edit',
    function ($breadcrumbs) {
        $breadcrumbs->parent('user.index');
        $breadcrumbs->push(Lang::get('messages.admin.users.edit-user'));
    }
);

Breadcrumbs::register(
    'role.index',
    function ($breadcrumbs) {
        $breadcrumbs->push(Lang::get('messages.admin.users.manage-users'));
        $breadcrumbs->push(Lang::get('messages.admin.users.roles'), route('role.index'));
    }
);

Breadcrumbs::register(
    'role.create',
    function ($breadcrumbs) {
        $breadcrumbs->parent('role.index');
        $breadcrumbs->push(Lang::get('messages.admin.users.roles.create'));
    }
);

Breadcrumbs::register(
    'role.edit',
    function ($breadcrumbs) {
        $breadcrumbs->parent('role.index');
        $breadcrumbs->push(Lang::get('messages.admin.users.roles.edit'));
    }
);

Breadcrumbs::register(
    'admin.users.permissions', 
    function ($breadcrumbs) {
        $breadcrumbs->push(Lang::get('messages.admin.users.manage-users'));
        $breadcrumbs->push(Lang::get('messages.admin.users.permissions'));
    }
);

Breadcrumbs::register(
    'posts.index',
    function ($breadcrumbs) {
        $breadcrumbs->push('Manage posts', route('posts.index'));
    }
);

Breadcrumbs::register(
    'posts.create',
    function ($breadcrumbs) {
        $breadcrumbs->parent('posts.index');
        $breadcrumbs->push('Create Post');
    }
);

Breadcrumbs::register(
    'posts.edit',
    function ($breadcrumbs) {
        $breadcrumbs->parent('posts.index');
        $breadcrumbs->push('Edit Post');
    }
);
