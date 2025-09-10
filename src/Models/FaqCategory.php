<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Koneko\KonekoVuexyAdmin\Support\Traits\Model\HasVuexyModelMetadata;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class FaqCategory extends Model implements AuditableContract
{
    use HasVuexyModelMetadata;
    use Auditable;

    protected $fillable = [
        'name',
        'icon',
        'order',
        'is_active',
    ];

    protected $casts = [
        'order' => 'integer',
        'is_active' => 'boolean',
    ];

    protected $auditInclude = [
        'name',
        'icon',
        'order',
        'is_active',
    ];

    /**
     * FAQs asociadas a esta categoría.
     */
    public function faqs(): HasMany
    {
        return $this->hasMany(Faq::class, 'category_id');
    }
}
