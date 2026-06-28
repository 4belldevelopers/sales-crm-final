<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LanguageSetting extends Model
{
    public function frontCmsHeader()
    {
        return $this->hasOne(FrontCmsHeader::class, 'language_settings_id');
    }
}
