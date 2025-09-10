<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Application\UI\Livewire\Sites\Seo;

use Koneko\KonekoWebsiteAdmin\Models\{WebsiteSeoProfile, WebsiteSite, WebsiteContent};
use Livewire\Attributes\Rule;
use Livewire\Component;

final class SchemaOrgCard extends Component
{
    public string $scope; // 'site' | 'content'
    public int    $scopeId;
    //public bool   $isSite = false;

    public ?WebsiteSeoProfile $profile = null;
    public ?WebsiteSite $website = null;
    public ?WebsiteContent $content = null;

    // Modo
    #[Rule('nullable|string|in:site,content,disable')]
    public ?string $schema_mode = null;

    /** JSON-LD (cast array en el modelo) */
    public ?array $schema_org = null;
    public ?string $schema_org_text = ''; // ESPEJO para el textarea

    /** Presets */
    public ?string $preset = null;
    public array $presetOptions = [
        'organization_basic'   => 'Organization (básico)',
        'localbusiness_geo'    => 'LocalBusiness (con geo)',
        'website_searchaction' => 'WebSite + SearchAction',
    ];

    /** UI */
    public string $targetNotify = '#website-schemaorg-card .notification-container';

    public string $richResultsUrl = 'https://search.google.com/test/rich-results';

    private function buildPublicUrl(): ?string
    {
        if ($this->scope == 'site') {
            $site = WebsiteSite::find($this->scopeId);
            return $site?->getFullDomainUrl(); // https://dominio
        }

        // Content: https://dominio/slug
        $content = WebsiteContent::with('site')->find($this->scopeId);
        if (!$content || !$content->site) return null;

        return rtrim($content->site->getFullDomainUrl(), '/') . '/' . ltrim($content->slug, '/');
    }

    public function mount(string $scope, int $scopeId): void
    {
        $this->scope   = $scope;
        $this->scopeId = $scopeId;

        $owner = $this->scope == 'site'
            ? WebsiteSite::query()->findOrFail($scopeId)
            : WebsiteContent::query()->findOrFail($scopeId);

        $this->profile = $owner->seoProfile()->firstOrCreate([], [ 'scope' => $this->scope ]);
        //$this->content = $this->isSite ? null : $owner;

        if ($url = $this->buildPublicUrl()) {
            $this->richResultsUrl = 'https://search.google.com/test/rich-results?url=' . urlencode($url);
        }

        $this->loadForm();
    }

    public function loadForm(): void
    {
        $p = $this->profile;

        $this->schema_mode     = $p->schema_mode->value;
        $this->schema_org      = $p->schema_org;
        $this->schema_org_text = $this->schema_org
            ? json_encode($this->schema_org, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)
            : '';
    }

    /** Inserta un preset sobre el editor (se puede editar antes de guardar) */
    public function applyPreset(): void
    {
        if (!$this->preset) return;

        $baseUrl = null;
        if ($this->scope) {
            $site = WebsiteSite::find($this->scopeId);
            $baseUrl = $site?->getFullDomainUrl();
        }

        $this->schema_org = match ($this->preset) {
            'organization_basic'   => $this->presetOrganizationBasic($baseUrl),
            'localbusiness_geo'    => $this->presetLocalBusinessGeo($baseUrl),
            'website_searchaction' => $this->presetWebsiteSearchAction($baseUrl),
            default                => $this->schema_org,
        };

        // Refleja en el textarea
        $this->schema_org_text = json_encode($this->schema_org, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }

    public function save(): void
    {
        $data = null;
        if ($this->schema_org_text) {
            try {
                $data = json_decode($this->schema_org_text, true, 512, JSON_THROW_ON_ERROR);
            } catch (\Throwable $e) {
                $this->addError('schema_org_text', 'JSON inválido: '.$e->getMessage());
                return;
            }
            if (!isset($data['@type']) && !isset($data['@graph'])) {
                $this->addError('schema_org_text', 'El JSON-LD debe incluir @type o @graph.');
                return;
            }
        }

        $this->profile->fill([
            'schema_mode' => $this->schema_mode,
            'schema_org'  => $data,
        ])->save();

        if ($this->content) {
            $this->content->fill([
            ])->save();
        }

        $this->dispatch('notification', target: $this->targetNotify, type: 'success', message: 'Schema.org guardado correctamente.');
    }

    public function resetForm(): void
    {
        $this->resetValidation();
        $this->profile->refresh();
        if($this->content) $this->content->refresh();
        $this->loadForm();
        $this->dispatch('notification', target: $this->targetNotify, type: 'info', message: 'Cambios descartados.');
    }

    public function render()
    {
        return view('koneko-website-admin::livewire.sites.seo.schema-org-card');
    }

    // ---------------------- PRESETS ----------------------

    private function presetOrganizationBasic(?string $baseUrl): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type'    => 'Organization',
            'name'     => 'Nombre de la organización',
            'url'      => $baseUrl ?? 'https://ejemplo.com/',
            'logo'     => ($baseUrl ? rtrim($baseUrl, '/') : 'https://ejemplo.com') . '/logo.png',
            'sameAs' => ['https://www.facebook.com/tu-pagina','https://www.instagram.com/tu-cuenta']
        ];
    }

    private function presetLocalBusinessGeo(?string $baseUrl): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type'    => 'LocalBusiness',
            'name'     => 'Nombre del negocio',
            'url'      => $baseUrl ?? 'https://ejemplo.com/',
            'address'  => [
                '@type'            => 'PostalAddress',
                'streetAddress'    => 'Calle 123',
                'addressLocality'  => 'Ciudad',
                'addressRegion'    => 'Estado/Provincia',
                'postalCode'       => '00000',
                'addressCountry'   => 'MX',
            ],
            'geo' => [
                '@type'     => 'GeoCoordinates',
                'latitude'  => 19.432608,
                'longitude' => -99.133209,
            ],
            'telephone' => '+52 55 0000 0000',
            'openingHours' => ['Mo-Fr 09:00-18:00']
        ];
    }

    private function presetWebsiteSearchAction(?string $baseUrl): array
    {
        $searchUrl = ($baseUrl ? rtrim($baseUrl, '/') : 'https://ejemplo.com') . '/buscar?q={search_term_string}';

        return [
            '@context' => 'https://schema.org',
            '@type'    => 'WebSite',
            'name'     => 'Nombre del sitio',
            'url'      => $baseUrl ?? 'https://ejemplo.com/',
            'potentialAction' => [
                '@type'       => 'SearchAction',
                'target'      => $searchUrl,
                'query-input' => 'required name=search_term_string'
            ]
        ];
    }
}
