<?php

namespace App\Observers;

use App\Models\FrontCmsHeader;
use App\Models\FrontImageFeature;
use App\Helper\Files;

class FrontImageFeatureObserver
{
    public function updating(FrontImageFeature $image)
    {
        $original = $image->getOriginal();

        if ($image->isDirty('image')) {
            if (strpos($original['image'], 'feature') !== false) {
                return true;
            }
            Files::deleteFile($original['image'], 'front-features');
        }
    }
}
