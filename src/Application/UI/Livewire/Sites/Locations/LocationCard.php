<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Application\UI\Livewire\Sites\Locations;

use Livewire\Component;
use Livewire\Attributes\Rule;
use Koneko\KonekoVuexyAdmin\Application\Settings\Manager\KonekoSettingManager;
use Koneko\KonekoWebsiteAdmin\Application\LocalModule as WebsiteModule;
use Koneko\KonekoWebsiteAdmin\Models\WebsiteSite;

final class LocationCard extends Component
{
    public WebsiteSite $site;

    public string $targetNotify = '#website-location-card .notification-container';

    private const GROUP    = 'layout';
    private const SECTION  = 'contact';
    private const SUBGROUP = 'location';

    #[Rule('nullable|string|max:120')]
    public string $address_line = '';

    #[Rule('nullable|string|max:80')]
    public string $city = '';

    #[Rule('nullable|string|max:80')]
    public string $state = '';

    #[Rule('nullable|string|max:20')]
    public string $postal_code = '';

    #[Rule('nullable|string|max:80')]
    public string $country = '';

    #[Rule('nullable|numeric')]
    public $location_lat = null;

    #[Rule('nullable|numeric')]
    public $location_lng = null;

    // URL de mapa (calculada)
    public string $maps_url = '';

    public function mount(WebsiteSite $site): void
    {
        $this->site = $site;
        $this->loadForm();
    }

    private function settings(): KonekoSettingManager
    {
        return settings('website-admin')
            ->context(self::GROUP, self::SECTION, self::SUBGROUP)
            ->scope($this->site);
    }

    public function loadForm(): void
    {
        $d = $this->settings()->asArray()->all();
        $this->address_line = (string)($d['address_line'] ?? '');
        $this->city         = (string)($d['city'] ?? '');
        $this->state        = (string)($d['state'] ?? '');
        $this->postal_code  = (string)($d['postal_code'] ?? '');
        $this->country      = (string)($d['country'] ?? '');
        $this->location_lat = $d['location_lat'] ?? null;
        $this->location_lng = $d['location_lng'] ?? null;
        $this->maps_url     = (string)($d['maps_url'] ?? '');
    }

    public function save(): void
    {
        $this->address_line = trim($this->address_line);
        $this->city         = trim($this->city);
        $this->state        = trim($this->state);
        $this->postal_code  = trim($this->postal_code);
        $this->country      = trim($this->country);
        $lat = is_numeric($this->location_lat) ? (float)$this->location_lat : null;
        $lng = is_numeric($this->location_lng) ? (float)$this->location_lng : null;

        $this->validate([
            'address_line' => ['nullable','string','max:120'],
            'city'         => ['nullable','string','max:80'],
            'state'        => ['nullable','string','max:80'],
            'postal_code'  => ['nullable','string','max:20'],
            'country'      => ['nullable','string','max:80'],
            'location_lat' => ['nullable','numeric','between:-90,90'],
            'location_lng' => ['nullable','numeric','between:-180,180'],
        ]);

        $this->location_lat = $lat;
        $this->location_lng = $lng;
        $this->maps_url = $this->buildMapsUrl();

        $s = $this->settings();
        $s->set('address_line', $this->address_line);
        $s->set('city', $this->city);
        $s->set('state', $this->state);
        $s->set('postal_code', $this->postal_code);
        $s->set('country', $this->country);
        $s->set('location_lat', $this->location_lat);
        $s->set('location_lng', $this->location_lng);
        $s->set('maps_url', $this->maps_url);

        $this->dispatch('notification', target: $this->targetNotify, type: 'success', message: 'Se han guardado los cambios.');
        $this->dispatch('site-location-updated', id: $this->site->id);
    }

    public function resetForm(): void
    {
        $this->resetValidation();
        $this->loadForm();
        $this->dispatch('notification', target: $this->targetNotify, type: 'info', message: 'Cambios descartados.');
    }

    public function render()
    {
        return view('koneko-website-admin::livewire.sites.locations.location-card');
    }

    private function buildMapsUrl(): string
    {
        if ($this->location_lat !== null && $this->location_lng !== null) {
            return sprintf('https://www.google.com/maps/search/?api=1&query=%s,%s', $this->location_lat, $this->location_lng);
        }
        $parts = array_filter([$this->address_line, $this->city, $this->state, $this->postal_code, $this->country]);
        if (!$parts) return '';
        return 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode(implode(', ', $parts));
    }
}
