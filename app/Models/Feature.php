<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Feature
 *
 * Model for Super Admin Platform Feature Policies & Expiration Rules.
 * Maps to MySQL `features` table and denormalized Elasticsearch index `local_features_list`.
 *
 * @property int $id
 * @property string $title
 * @property int $days
 * @property int $status 0 = Inactive/OFF, 1 = Active/ON
 */
class Feature extends Model
{
    protected $table = 'features';

    public $timestamps = false;

    protected $guarded = ['id'];

    protected $casts = [
        'id' => 'integer',
        'days' => 'integer',
        'status' => 'integer',
    ];

    /**
     * Check if policy is active.
     */
    public function isActive(): bool
    {
        return (int) $this->status === 1;
    }

    /**
     * Check if policy duration is immediate (0 days).
     */
    public function isImmediate(): bool
    {
        return (int) $this->days === 0;
    }
}
