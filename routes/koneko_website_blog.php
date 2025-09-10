<?php

use Illuminate\Support\Facades\Route;
use Koneko\KonekoWebsiteAdmin\Application\Http\Controllers\{
    BlogCategoryController,
    BlogTagController,
    BlogArticleController,
    BlogCommentController
};
use Koneko\KonekoVuexyAdmin\Support\Routing\RouteScope;

RouteScope::auto(__FILE__, 'blog', 'blog.', function (RouteScope $r) {
    // Categorías del Blog
    $r->route('categorias', 'categories.', BlogCategoryController::class, function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::get('edit/{id}', 'edit')->name('edit');
        Route::get('delete/{id}', 'delete')->name('delete');
    });

    // Etiquetas
    $r->route('etiquetas', 'tags.', BlogTagController::class, function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::get('edit/{id}', 'edit')->name('edit');
        Route::get('delete/{id}', 'delete')->name('delete');
    });

    // Artículos
    $r->route('articulos', 'articles.', BlogArticleController::class, function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::get('edit/{id}', 'edit')->name('edit');
        Route::get('delete/{id}', 'delete')->name('delete');
    });

    // Comentarios
    $r->route('comentarios', 'comments.', BlogCommentController::class, function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::get('edit/{id}', 'edit')->name('edit');
        Route::get('delete/{id}', 'delete')->name('delete');
    });
});
