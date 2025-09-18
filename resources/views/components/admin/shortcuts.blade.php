@props([
  'siteId'  => null,
  'pageId'  => null,

  'adminUrl'     => '/admin',
  'pageEditPath' => '/admin/website-admin/sitios-web/:site/pages/:page/edit',
  'siteEditPath' => '/admin/website-admin/sitios-web/:site/general',

  'pageEditUrl'  => null,
  'siteEditUrl'  => null,

  // ===== Posicionamiento / UI =====
  'position'   => 'right',      // 'left' | 'right'
  'offsetX'    => '14px',
  'offsetY'    => '14px',
  'offsetXSm'  => '14px',
  'offsetYSm'  => '18px',
  'dockAlign'  => 'fab-side',   // 'fab-side' | 'topbar' | 'inline'

  'z'          => 2000,
  'color'      => null,
  'startOpenDesktop' => false,
  'startOpenMobile'  => false,
  'showFabDesktop'   => true,

  'pulse' => true,
  'force' => false,
])

@php
    // CSS vars inline (ligero)
    $isLeft  = ($position === 'left');
    $sideCls = $isLeft ? 'is-left' : 'is-right';

    // Flags de visibilidad (baratos): sólo dependen de IDs/URLs o force
    $showEditPage = $force && ($pageId || $pageEditUrl);
    $showEditSite = $force && ($siteId || $siteEditUrl);
    $showAdmin    = $force; // cuando tengas permisos reales, reemplazas esto
    $showCache    = $force;
@endphp

<div class="kna-admin-dock {{ $sideCls }}"
     role="region" aria-label="Atajos administrativos"
     data-admin-url="{{ $adminUrl }}"
     data-page-edit-path="{{ $pageEditPath }}"
     data-site-edit-path="{{ $siteEditPath }}"
     data-page-edit-url="{{ $pageEditUrl ?? '' }}"
     data-site-edit-url="{{ $siteEditUrl ?? '' }}"
     data-site-id="{{ $siteId ?? '' }}"
     data-page-id="{{ $pageId ?? '' }}"
     data-start-open-desktop="{{ $startOpenDesktop ? '1' : '0' }}"
     data-start-open-mobile="{{ $startOpenMobile ? '1' : '0' }}"
     data-show-fab-desktop="{{ ($showFabDesktop ?? false) ? '1' : '0' }}"
     data-align="{{ $dockAlign }}"
     style="
      --kna-admin-z: {{ $z }};
      --kna-admin-accent: {{ $color ?: 'var(--primary, #c0781a)' }};
      --kna-admin-side: {{ $offsetX }};
      --kna-admin-bottom: {{ $offsetY }};
      --kna-admin-side-sm: {{ $offsetXSm }};
      --kna-admin-bottom-sm: {{ $offsetYSm }};
      --kna-admin-top: 14px;       /* opcional: si quieres tunear topbar */
      --kna-admin-top-sm: 8px;     /* opcional: si quieres tunear topbar en móvil */
    "
>
    <div class="kna-admin-dock__inner" role="toolbar" aria-label="Acciones rápidas" data-open="0">
        @if($showEditPage)
            <a href="#" class="kna-btn js-edit-page" aria-label="Editar página (e)">
                <i class="far fa-edit" aria-hidden="true"></i>
                <span class="kna-btn__label">Editar página</span>
                <kbd class="kna-kbd">e</kbd>
            </a>
        @endif

        @if($showEditSite)
            <a href="#" class="kna-btn js-edit-site" aria-label="Editar sitio (s)">
                <i class="fas fa-sitemap" aria-hidden="true"></i>
                <span class="kna-btn__label">Editar sitio</span>
                <kbd class="kna-kbd">s</kbd>
            </a>
        @endif

        @if($showAdmin)
            <a href="{{ $adminUrl }}" class="kna-btn js-open-admin" aria-label="Abrir panel admin (a)">
                <i class="fas fa-tools" aria-hidden="true"></i>
                <span class="kna-btn__label">Admin</span>
                <kbd class="kna-kbd">a</kbd>
            </a>
        @endif

        @if($showCache)
            <button type="button" class="kna-btn" data-action="purge-cache" aria-label="Purgar caché (c)">
                <i class="fas fa-broom" aria-hidden="true"></i>
                <span class="kna-btn__label">Cache</span>
                <kbd class="kna-kbd">c</kbd>
            </button>
        @endif

        <button type="button" class="kna-btn" data-action="help" aria-label="Ver atajos (?)">
            <i class="far fa-question-circle" aria-hidden="true"></i>
            <span class="kna-btn__label">Ayuda</span>
            <kbd class="kna-kbd">?</kbd>
        </button>
    </div>

    <button class="kna-admin-fab {{ $pulse ? 'kna-pulse' : '' }}" aria-label="Abrir atajos admin">
        <i class="fas fa-bolt"></i>
    </button>
</div>

@push('page-script')
  <script>
    (() => {
      const dock  = document.querySelector('.kna-admin-dock');
      if(!dock) return;

      const inner = dock.querySelector('.kna-admin-dock__inner');
      const fab   = dock.querySelector('.kna-admin-fab');

      const base = (document.documentElement.dataset.baseUrl || '/');
      const bool = (v) => v === true || v === 'true' || v === '1' || v === 1 || v === 'on' || v === 'yes';

      const opts = {
        adminUrl: dock.dataset.adminUrl || '/admin',
        pageEditPath: dock.dataset.pageEditPath || '',
        siteEditPath: dock.dataset.siteEditPath || '',
        pageEditUrl: dock.dataset.pageEditUrl || '',
        siteEditUrl: dock.dataset.siteEditUrl || '',
        siteId: dock.dataset.siteId || '',
        pageId: dock.dataset.pageId || '',
        startOpenDesktop: bool(dock.dataset.startOpenDesktop),
        startOpenMobile:  bool(dock.dataset.startOpenMobile),
        align: (dock.dataset.align || 'fab-side'),
      };

      const isMobile = () => window.matchMedia('(max-width: 991.98px)').matches;

      const buildUrl = (tpl, siteId, pageId) => {
        if (!tpl) return null;
        let path = tpl.replace(':site', siteId || '').replace(':page', pageId || '');
        if (path.includes(':site') || path.includes(':page')) return null;
        try { return new URL(path, base).toString(); } catch (e){ return null; }
      };

      const computed = {
        page: opts.pageEditUrl || buildUrl(opts.pageEditPath, opts.siteId, opts.pageId),
        site: opts.siteEditUrl || buildUrl(opts.siteEditPath, opts.siteId, null),
        admin: new URL(opts.adminUrl, base).toString()
      };

      // Inyecta hrefs válidos
      const ep = dock.querySelector('.kna-btn.js-edit-page');
      if (ep){ if (computed.page) ep.setAttribute('href', computed.page); else ep.remove(); }
      const es = dock.querySelector('.kna-btn.js-edit-site');
      if (es){ if (computed.site) es.setAttribute('href', computed.site); else es.remove(); }

      // ===== Estado / helpers
      let pinned = false;
      let hideTimer = null;

      const show = () => {
        // inline/fab-side/topbar render
        if (opts.align === 'inline') {
          inner.style.display = 'inline-flex';
        } else {
          inner.style.display = (isMobile() || opts.align==='fab-side') ? 'block' : 'inline-flex';
        }
        inner.dataset.open = '1';
      };
      const hide = () => {
        if (opts.align === 'inline') return; // inline siempre visible
        inner.style.display = 'none';
        inner.dataset.open = '0';
      };
      const scheduleHide = () => {
        if (pinned || opts.align !== 'fab-side') return;
        clearTimeout(hideTimer);
        hideTimer = setTimeout(() => { if (!pinned) hide(); }, 150);
      };
      const cancelHide = () => { clearTimeout(hideTimer); };

      // ===== Arranque
      const applyState = () => {
        if (opts.align === 'inline') { show(); return; }
        const open = isMobile() ? opts.startOpenMobile : opts.startOpenDesktop;
        if (pinned) { show(); return; }
        open ? show() : hide();
      };
      applyState();
      window.addEventListener('resize', applyState);

      // ===== Click FAB: toggle real
      fab?.addEventListener('click', () => {
        if (opts.align === 'inline') return; // inline no togglea
        const visible = window.getComputedStyle(inner).display !== 'none';
        if (visible){
          pinned = false; hide();
        } else {
          pinned = true; show();
          inner.querySelector('.kna-btn')?.focus({preventScroll:true});
        }
      });

      // ===== Peek por hover SOLO en fab-side
      const allowPeek = (opts.align === 'fab-side');
      if (allowPeek && fab && inner) {
        fab.addEventListener('mouseenter', () => { if (!isMobile() && !pinned) { cancelHide(); show(); } });
        fab.addEventListener('mouseleave', () => { if (!isMobile() && !pinned) scheduleHide(); });
        inner.addEventListener('mouseenter', () => { if (!isMobile() && !pinned) cancelHide(); });
        inner.addEventListener('mouseleave', () => { if (!isMobile() && !pinned) scheduleHide(); });
      }

      // Acciones
      dock.addEventListener('click', (e) => {
        const el = e.target.closest('[data-action]');
        if(!el) return;
        const action = el.getAttribute('data-action');
        switch(action){
          case 'purge-cache':
            window.dispatchEvent(new CustomEvent('kna:admin', { detail: { action: 'purge-cache' }}));
            break;
          case 'help':
            alert('Atajos: e (editar página), s (editar sitio), a (admin), c (cache), ? (ayuda)');
            break;
        }
      });

      // Atajos
      document.addEventListener('keydown', (e) => {
        if (e.ctrlKey || e.metaKey || e.altKey) return;
        const tag = (e.target.tagName || '').toLowerCase();
        if (tag === 'input' || tag === 'textarea' || e.target.isContentEditable) return;

        switch(e.key){
          case 'e': if (computed.page)  window.location.href = computed.page;  e.preventDefault(); break;
          case 's': if (computed.site)  window.location.href = computed.site;  e.preventDefault(); break;
          case 'a': if (computed.admin) window.location.href = computed.admin; e.preventDefault(); break;
          case 'c': window.dispatchEvent(new CustomEvent('kna:admin', { detail: { action: 'purge-cache' }})); e.preventDefault(); break;
          case '?': alert('Atajos: e, s, a, c, ?'); e.preventDefault(); break;
        }
      });
    })();
  </script>
@endpush
