<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Application\UI\Livewire\Sites\Seo;

use Koneko\KonekoVuexyAdmin\Support\Media\Image\ImageCore;
use Koneko\KonekoWebsiteAdmin\Application\Enums\WebsiteSeoProfile\MetaMode;
use Koneko\KonekoWebsiteAdmin\Models\{WebsiteSeoProfile, WebsiteSite, WebsiteContent};
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

final class OgCard extends Component
{
    use WithFileUploads;

    public string $scope; // 'site' | 'content'
    public int    $scopeId;
    public bool   $isSite = false;

    public ?WebsiteSeoProfile $profile = null;
    public ?WebsiteContent $content = null;

    public ?float $source_aspect = null;
    public ?int   $source_w = null;
    public ?int   $source_h = null;

    #[Rule('nullable|string|in:site,content,disable')]
    public ?string $og_mode = null;

    // Campos OG
    public ?string $og_type  = null;
    public ?string $og_image = null;
    public ?string $og_url   = null;

    // ===== Procesamiento de imagen =====
    #[Rule('in:cover,keep')] // cover|keep (sin contain/stretch)
    public string $image_fit = 'cover';

    #[Rule('numeric|min:0.1|max:10')]
    public float $target_aspect = 1.91; // 1.91 recomendado, 1.0 cuadrada

    #[Rule('numeric|min:100000|max:5000000')]
    public int $pixel_area = 756000;    // presupuesto de píxeles

    #[Rule('in:auto,jpg,webp,png')]
    public string $image_format = 'auto'; // por defecto conservar

    #[Rule('integer|min:60|max:95')]
    public int $image_quality = 82;      // default 82

    #[Rule(['nullable','image','mimes:jpeg,png,webp','max:20480'])]
    public $upload_og_image = null;

    // UI
    public string $targetNotify = '#website-og-card .notification-container';
    public string $shareDebuggerUrl = 'https://developers.facebook.com/tools/debug/';

    // Previews (solo lectura en UI)
    public ?string $computed_site_url = null;
    public ?string $computed_page_url = null;

    public function mount(string $scope, int $scopeId): void
    {
        $this->scope = $scope;
        $this->scopeId   = $scopeId;
        $this->isSite      = $scope === 'site';

        $owner = $this->isSite
            ? WebsiteSite::query()->findOrFail($scopeId)
            : WebsiteContent::query()->findOrFail($scopeId);

        $scope = $this->isSite ? 'Site' : 'Content';
        $this->profile = $owner->seoProfile()->firstOrCreate([], ['scope' => $scope]);
        $this->content = $this->isSite ? null : $owner;

        $this->loadForm();
    }

    public function loadForm(): void
    {
        $p = $this->profile;
        $c = $this->content;

        $this->og_mode = $c ? $c->og_mode->value : null;

        $this->og_type   = $p->og_type ?: $this->og_type;
        $this->og_image  = $p->og_image ?: $this->og_image;
        $this->og_url    = $p->og_url ?: $this->og_url;

        // Defaults de procesado
        $this->image_fit     = 'cover';
        $this->target_aspect = 1.91;
        $this->pixel_area    = 756000;
        $this->image_format  = 'auto';
        $this->image_quality = 82;

        $this->upload_og_image = null;

        $this->source_aspect = null;
    }

    /** Auto-cambiar a override cuando se suba una imagen */
    public function updatedUploadOgImage(): void
    {
        if (!$this->upload_og_image) return;

        if ($this->og_mode !== MetaMode::Override->value) {
            $this->og_mode = MetaMode::Override->value;
        }

        try {
            [$w, $h] = getimagesize($this->upload_og_image->getRealPath());
            if ($w && $h) {
                $this->source_w = $w;
                $this->source_h = $h;
                $this->source_aspect = $w / max(1, $h);
            }
        } catch (\Throwable $e) {
            $this->source_aspect = null;
        }
    }

    public function save(): void
    {
        if ($this->upload_og_image && $this->og_mode === MetaMode::Override->value) {
            $disk = config('koneko.media.default_disk', 'public');

            $core = app(ImageCore::class)
                ->withDisk($disk)
                ->forContext([
                    'module' => 'website',
                    'scope'  => $this->isSite ? 'site' : 'content',
                    'owner'  => $this->scopeId,
                ]);

            // Si keep ⇒ intenta usar aspecto de origen (si lo conocemos), si no, cae a target_aspect
            $effectiveAspect = $this->image_fit === 'keep'
                ? ($this->source_aspect ?: (float) $this->target_aspect)
                : (float) $this->target_aspect;

            $path = $core->makeShare($this->upload_og_image, [
                'area'    => $this->pixel_area,
                'aspect'  => $effectiveAspect,
                'fit'     => $this->image_fit,
                'format'  => $this->image_format,  // 'auto'|jpg|png|webp
                'quality' => $this->image_quality, // 60..95
                'dirKey'  => 'share',
                'prefix'  => 'og',
                'upscale' => false
            ]);

            try {
                $this->og_image = \Storage::disk($disk)->url($path);
            } catch (\Exception) {
                $this->og_image = asset('storage/' . ltrim($path, '/'));
            }
        }

        // Validación de URLs (suave)
        $abs = fn (?string $u) => !$u || preg_match('#^https?://#i', $u);
        if (!$abs($this->og_url))   { $this->addError('og_url', 'URL debe ser absoluta (http/https).'); return; }
        if (!$abs($this->og_image)) { $this->addError('og_image', 'La imagen debe ser URL absoluta.'); return; }

        // Reglas mínimas cuando override
        if ($this->og_mode === MetaMode::Override->value) {
            if (!filled($this->og_image)) {
                $this->addError('og_image', 'Proporciona una imagen o súbela.');
                return;
            }
        }

        $this->profile->fill([
            'og_type'        => $this->og_type ?: null,
            'og_image'       => $this->og_image ?: null,
            'og_url'         => $this->og_url ?: null,
        ])->save();

        if ($this->content) {
            $this->content->fill([
                'og_mode' => $this->og_mode,
            ])->save();
        }

        $this->dispatch('notification', target: $this->targetNotify, type: 'success', message: 'Open Graph guardado.');
        $this->upload_og_image = null;
    }

    public function resetForm(): void
    {
        $this->resetValidation();
        $this->profile->refresh();
        if($this->content) $this->content->refresh();
        $this->loadForm();
        $this->dispatch('notification', target: $this->targetNotify, type: 'info', message: 'Cambios descartados.');
    }

    private function absolutizeStorage(?string $relative): ?string
    {
        if (!$relative) return null;
        if (preg_match('#^https?://#i', $relative)) { return $relative; }
        return asset('storage/' . ltrim($relative, '/'));
    }

    public function render()
    {
        return view('koneko-website-admin::livewire.sites.seo.og-card');
    }
}
