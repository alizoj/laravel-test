<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuItem extends Model
{
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->with('children');
    }
}
