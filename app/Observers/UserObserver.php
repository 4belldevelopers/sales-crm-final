<?php

namespace App\Observers;

use App\Models\Company;
use App\Helper\Files;
use App\Models\User;

class UserObserver
{
    public function saving(User $user)
    {
        if (company()) {
            $user->company_id = company()->id;
        }
    }

    public function updating(User $user){
        $original = $user->getOriginal();
        if ($user->isDirty('image')){
            Files::deleteFile($original['image'], 'profile');
        }
    }
}
