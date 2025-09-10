<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Application\UI\Livewire\Blog\Comment;

use Koneko\KonekoVuexyAdmin\Support\Livewire\Components\Table\AbstractTableComponent;
use Koneko\KonekoWebsiteAdmin\Application\ConfigBuilders\Blog\CommentsTableConfigBuilder;

class BlogCommentsTable extends AbstractTableComponent
{
    /**
     * Define la clase del builder de configuración.
     */
    protected function configBuilderClass(): ?string
    {
        return CommentsTableConfigBuilder::class;
    }

    /**
     * Vista Blade que debe renderizar este componente.
     */
     protected function viewPath(): string
     {
         return 'koneko-website-admin::livewire.content.faq.index';
     }
}
