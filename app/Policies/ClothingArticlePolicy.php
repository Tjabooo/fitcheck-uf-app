<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ClothingArticle;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClothingArticlePolicy
{
    use HandlesAuthorization;

    public function delete(User $user, ClothingArticle $clothingArticle)
    {
        return $user->id === $clothingArticle->user_id;
    }
}
