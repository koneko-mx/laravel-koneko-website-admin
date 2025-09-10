<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Application\UI\Livewire\Sites\General;

use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule as VRule;
use Koneko\KonekoWebsiteAdmin\Application\Enums\WebsiteContents\WebsiteContentStatus;
use Koneko\KonekoWebsiteAdmin\Application\Enums\Websites\WebsiteSiteStatus;
use Koneko\KonekoWebsiteAdmin\Models\WebsiteContent;
use Koneko\KonekoWebsiteAdmin\Models\WebsiteSite;
use Livewire\Attributes\Rule;
use Livewire\Component;

final class VisibilitySecurityCard extends Component
{
    public string $targetNotify = '#website-visibility-security-card .notification-container';

    public WebsiteSite $site;

    /** Campos del form */
    #[Rule('boolean')]
    public bool $www_redirect = false;

    #[Rule('boolean')]
    public bool $force_https = false;

    #[Rule('integer|nullable')]
    public ?int $coming_soon_content_id = null;

    #[Rule('integer|nullable')]
    public ?int $maintenance_content_id = null;

    public string $status = '';

    public $status_options = [],
        $pages_options = [];

    public function mount(WebsiteSite $site): void
    {
        $this->site = $site;

        $this->status_options = WebsiteSiteStatus::options();
        $this->pages_options = WebsiteContent::query()
            ->select([
                'id',
                DB::raw("CONCAT(CONCAT_WS('    - /', title, slug), IF(status='draft',  '   [Borrador]', '')) as title"),
            ])
            ->where('site_id', $site->id)
            ->whereIn('status', [
                WebsiteContentStatus::Published->value,
                WebsiteContentStatus::Draft->value,
            ])
            ->orderBy('title')
            ->pluck('title','id')
            ->toArray();

        $this->loadForm();
    }

    public function loadForm() : void
    {
        $this->www_redirect    = (bool) $this->site->www_redirect;
        $this->force_https     = (bool) $this->site->force_https;
        $this->coming_soon_content_id = $this->site->coming_soon_content_id;
        $this->maintenance_content_id = $this->site->maintenance_content_id;
        $this->status          = $this->site->status instanceof WebsiteSiteStatus
            ? $this->site->status->value
            : (string) $this->site->status;
    }

    public function save() : void
    {
        // Valida booleans por atributos; status con in: dinámico
        $this->validate();
        $this->validate([
            'status' => [ 'required', 'string', VRule::in($this->statusValues()) ],
        ]);

        // Carga fresca
        $this->site = WebsiteSite::query()->findOrFail($this->site->id);
        $this->site->update([
            'www_redirect'    => $this->www_redirect,
            'force_https'     => $this->force_https,
            'coming_soon_content_id' => $this->coming_soon_content_id ?? null,
            'maintenance_content_id' => $this->maintenance_content_id ?? null,
            'status'          => WebsiteSiteStatus::from($this->status),
        ]);

        // Limpiamos Cache


        // Notificación
        $this->dispatch(
            'notification',
            target: $this->targetNotify,
            type: 'success',
            message: 'Se han guardado los cambios en las configuraciones.'
        );
    }

    /** Valores válidos del enum para la regla in: */
    private function statusValues(): array
    {
        return array_map(fn ($case) => $case->value, WebsiteSiteStatus::cases());
    }

    public function resetForm(): void
    {
        $this->site = WebsiteSite::query()->findOrFail($this->site->id);
        $this->loadForm();
        $this->resetValidation();
    }

    public function render() : ViewContract
    {
        return view('koneko-website-admin::livewire.sites.general.visibility-security-card');
    }
}
