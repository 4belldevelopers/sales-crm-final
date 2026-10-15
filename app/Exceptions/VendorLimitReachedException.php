<?php

namespace App\Exceptions;

/**
 * Class VendorLimitReachedException
 *
 * Thrown when an agency attempts to claim an Agency Hiring position where the vendor cap is reached.
 */
class VendorLimitReachedException extends MarketplaceException
{
}
