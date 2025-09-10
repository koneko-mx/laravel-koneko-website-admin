<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Application\UI\Livewire\Blog\Article;

use Koneko\KonekoVuexyAdmin\Support\Livewire\Components\Table\AbstractTableComponent;
use Koneko\KonekoWebsiteAdmin\Application\ConfigBuilders\Blog\ArticlesTableConfigBuilder;

class BlogArticlesTable extends AbstractTableComponent
{
    /**
     * Define la clase del builder de configuración.
     */
    protected function configBuilderClass(): ?string
    {
        return ArticlesTableConfigBuilder::class;
    }

    /**
     * Vista Blade que debe renderizar este componente.
     */
     protected function viewPath(): string
     {
         return 'koneko-website-admin::livewire.content.faq.index';
     }
}
