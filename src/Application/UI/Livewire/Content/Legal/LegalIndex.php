<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Application\UI\Livewire\Content\Legal;

use Koneko\KonekoVuexyAdmin\Application\Settings\SettingsService;
use Koneko\KonekoWebsiteAdmin\Application\Services\WebsiteTemplateService;
use Livewire\Component;

class LegalIndex extends Component
{
    private $targetNotify = "#website-legal-settings-card .notification-container";

    public $legalVars = [];
    public $currentSection = null;

    protected $listeners = [
        'saveLegalNotices' => 'save',
    ];

    public function mount()
    {
        $this->loadSettings();

        // Seleccionar la primera sección por defecto
        $this->currentSection = array_key_first($this->legalVars);
    }

    function loadSettings()
    {
        $websiteTemplateService = app(WebsiteTemplateService::class);

        switch ($this->currentSection) {
            case 'legal_terminos_y_condiciones':
                $this->legalVars['legal_terminos_y_condiciones'] = $websiteTemplateService->getLegalVars('legal_terminos_y_condiciones');
                break;

            case 'legal_aviso_de_privacidad':
                $this->legalVars['legal_aviso_de_privacidad'] = $websiteTemplateService->getLegalVars('legal_aviso_de_privacidad');
                break;

            case 'legal_politica_de_devoluciones':
                $this->legalVars['legal_politica_de_devoluciones'] = $websiteTemplateService->getLegalVars('legal_politica_de_devoluciones');
                break;

            case 'legal_politica_de_envios':
                $this->legalVars['legal_politica_de_envios'] = $websiteTemplateService->getLegalVars('legal_politica_de_envios');
                break;

            case 'legal_politica_de_cookies':
                $this->legalVars['legal_politica_de_cookies'] = $websiteTemplateService->getLegalVars('legal_politica_de_cookies');
                break;

            case 'legal_autorizaciones_y_licencias':
                $this->legalVars['legal_autorizaciones_y_licencias'] = $websiteTemplateService->getLegalVars('legal_autorizaciones_y_licencias');
                break;

            case 'legal_informacion_comercial':
                $this->legalVars['legal_informacion_comercial'] = $websiteTemplateService->getLegalVars('legal_informacion_comercial');
                break;

            case 'legal_consentimiento_para_el_login_de_terceros':
                $this->legalVars['legal_consentimiento_para_el_login_de_terceros'] = $websiteTemplateService->getLegalVars('legal_consentimiento_para_el_login_de_terceros');
                break;

            case 'legal_leyendas_de_responsabilidad':
                $this->legalVars['legal_leyendas_de_responsabilidad'] = $websiteTemplateService->getLegalVars('legal_leyendas_de_responsabilidad');
                break;

            default:
                $this->legalVars = $websiteTemplateService->getLegalVars();
        }
    }

    public function rules()
    {
        $rules = [];

        if ($this->legalVars[$this->currentSection]['enabled']) {
            $rules["legalVars.{$this->currentSection}.content"] = ['required', 'string', new NotEmptyHtml];
        }

        $rules["legalVars.{$this->currentSection}.enabled"] = 'boolean';

        return $rules;
    }

    public function save()
    {
        $this->validate($this->rules());

        $SettingsService = app(SettingsService::class);

        $SettingsService->set($this->currentSection . '_enabled', $this->legalVars[$this->currentSection]['enabled'], null, 'koneko-website-admin');
        $SettingsService->set($this->currentSection . '_content', $this->legalVars[$this->currentSection]['content'], null, 'koneko-website-admin');

        $this->dispatch(
            'notification',
            target: $this->targetNotify,
            type: 'success',
            message: 'Se han guardado los cambios en las configuraciones.'
        );
    }

    public function render()
    {
        return view('koneko-website-admin::livewire.content.legal-notices.index');
    }
}
