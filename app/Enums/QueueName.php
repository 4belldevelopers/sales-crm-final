<?php

namespace App\Enums;

/**
 * QueueName Enum
 *
 * Defines canonical, strongly-typed queue names for the JERO application.
 * Prevents queue mis-spelling and ensures proper routing to dedicated
 * Horizon queue supervisors for 10K-concurrent scalability.
 */
enum QueueName: string
{
    case DEFAULT = 'default';
    case INDEXING = 'indexing';
    case MAIL = 'mail';
    case UPLOADS = 'uploads';
    case WEBHOOKS = 'webhooks';
    case COUNTERS = 'counters';
}
