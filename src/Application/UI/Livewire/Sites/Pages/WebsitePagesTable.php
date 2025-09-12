<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Application\UI\Livewire\Sites\Pages;

use Illuminate\Contracts\View\View;
use Koneko\KonekoVuexyAdmin\Support\Livewire\Components\Table\AbstractTableComponent;
use Koneko\KonekoWebsiteAdmin\Application\Enums\WebsiteContents\WebsiteContentStatus;
use Koneko\KonekoWebsiteAdmin\Application\UX\ConfigBuilders\Pages\PagesTableConfigBuilder;
use Koneko\KonekoWebsiteAdmin\Models\WebsiteSite;

class WebsitePagesTable extends AbstractTableComponent
{
    public WebsiteSite $site;

    public $statusOptions;

    protected function configBuilderClass(): ?string
    {
        return PagesTableConfigBuilder::class;
    }

    public function mount(): void
    {
        parent::mount();

        $this->statusOptions = WebsiteContentStatus::optionsForForm();
    }

    public function render(): View
    {
        return view('koneko-website-admin::livewire.sites.pages.table-index');
    }
}
