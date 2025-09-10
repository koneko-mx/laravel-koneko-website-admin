import { routes } from '@vuexy-admin/assets/js/bootstrap-table/globalConfig';

export const websiteContentActionFormatter = (value, row, index) => {
    if (!row.id) return '';

    const showUrl = row.slug;
    const editUrl = routes['websites-admin.pages.edit']
                      .replace(':site_id', row.site_id)
                      .replace(':id', row.id);

    return `
        <div class="flex justify-center space-x-2">
            <a href="${editUrl}" title="Editar" class="icon-button hover:text-slate-700">
                <i class="ti ti-edit"></i>
            </a>
            <a href="${showUrl}" title="Ver" class="icon-button hover:text-slate-700">
                <i class="ti ti-eye"></i>
            </a>
        </div>
    `.trim();
};

export const websiteContentTitleFormatter = (value, row, index) => {
  if (!row.id) return '';

  const showUrl = row.slug;


  return `
      <div class="">
          <span>${row.title}</span>
          <a href="/${showUrl}" title="Ver" class="icon-button hover:text-slate-700 block">
            ${row.slug}
          </a>
      </div>
  `.trim();
};

