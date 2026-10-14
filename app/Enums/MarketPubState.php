<?php

namespace App\Enums;

/**
 * MarketPubState Enum
 *
 * Defines the recruiter publication intent for the Agency Job Marketplace.
 * Decoupled from moderation and job lifecycle.
 */
enum MarketPubState: string
{
    case PRIVATE = 'private';
    case PENDING_REVIEW = 'pending_review';
    case PUBLISHED = 'published';
    case UNPUBLISHED = 'unpublished';
    case WITHDRAWN = 'withdrawn';
}
