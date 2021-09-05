<?php

namespace FiftySq\Commerce;

use FiftySq\Commerce\Data\Concerns\HandlesSchemaNames;
use FiftySq\Commerce\Data\Concerns\HasEvents;
use FiftySq\Commerce\Data\Concerns\HasSlug;

/**
 * @method static \Illuminate\Database\Eloquent\Builder isEnabled()
 * @method static \Illuminate\Database\Eloquent\Relations\BelongsTo category()
 * @method static \Illuminate\Database\Eloquent\Relations\HasMany variants()
 *
 * @see \FiftySq\Commerce\HasProductRelationships
 * @see \FiftySq\Commerce\HasProductScopes
 */
trait Sellable
{
    use HandlesSchemaNames;
    use HasEvents;
    use HasProductAttributes;
    use HasProductRelationships;
    use HasProductScopes;
    use HasSlug;
}
