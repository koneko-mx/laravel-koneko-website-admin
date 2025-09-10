<?php

  declare(strict_types=1);

  namespace Koneko\KonekoWebsiteAdmin\Application\UI\Livewire\Sites\Locations;

  use Livewire\Component;
  use Livewire\Attributes\Rule;
  use Koneko\KonekoVuexyAdmin\Application\Settings\Manager\KonekoSettingManager;
  use Koneko\KonekoWebsiteAdmin\Application\LocalModule as WebsiteModule;
  use Koneko\KonekoWebsiteAdmin\Models\WebsiteSite;

  final class BranchesCard extends Component
  {
      public WebsiteSite $site;

      public string $targetNotify = '#website-branches-card .notification-container';

      private const GROUP    = 'layout';
      private const SECTION  = 'contact';
      private const SUBGROUP = 'branches';

      /** @var array<int, array<string, mixed>> */
      #[Rule('array')]
      public array $items = [];

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
          $this->items = array_values($d['items'] ?? []);
      }

      public function addBranch(): void
      {
          $this->items[] = [
              'name' => '',
              'phone' => '',
              'email' => '',
              'address' => '',
              'lat' => null,
              'lng' => null,
              'hours' => '',
          ];
      }

      public function removeBranch(int $index): void
      {
          unset($this->items[$index]);
          $this->items = array_values($this->items);
      }

      public function save(): void
      {
          // Normaliza
          foreach ($this->items as &$b) {
              $b['name']    = trim((string)($b['name'] ?? ''));
              $b['phone']   = $this->normalizePhone((string)($b['phone'] ?? ''));
              $b['email']   = strtolower(trim((string)($b['email'] ?? '')));
              $b['address'] = trim((string)($b['address'] ?? ''));
              $b['lat']     = isset($b['lat']) && is_numeric($b['lat']) ? (float)$b['lat'] : null;
              $b['lng']     = isset($b['lng']) && is_numeric($b['lng']) ? (float)$b['lng'] : null;
              $b['hours']   = trim((string)($b['hours'] ?? ''));
          }
          unset($b);

          // Validación (ligera) por item
          foreach ($this->items as $i => $b) {
              $this->validate([
                  "items.$i.name"  => ['required','string','min:2','max:80'],
                  "items.$i.phone" => ['nullable','regex:/^[+]?[1-9][0-9]{4,19}$/'],
                  "items.$i.email" => ['nullable','email:rfc','max:254'],
                  "items.$i.address" => ['nullable','string','max:160'],
                  "items.$i.lat"   => ['nullable','numeric','between:-90,90'],
                  "items.$i.lng"   => ['nullable','numeric','between:-180,180'],
                  "items.$i.hours" => ['nullable','string','max:160'],
              ]);
          }

          $this->settings()->set('items', $this->items);

          $this->dispatch('notification', target: $this->targetNotify, type: 'success', message: 'Sucursales guardadas.');
          $this->dispatch('site-branches-updated', id: $this->site->id);
      }

      public function resetForm(): void
      {
          $this->resetValidation();
          $this->loadForm();
          $this->dispatch('notification', target: $this->targetNotify, type: 'info', message: 'Cambios descartados.');
      }

      public function render()
      {
          return view('koneko-website-admin::livewire.sites.locations.branches-card');
      }

      private function normalizePhone(string $raw): string
      {
          $v = preg_replace('/[^0-9+]/', '', trim($raw));
          if (str_starts_with($v, '00')) $v = '+' . substr($v, 2);
          return $v;
      }
}
