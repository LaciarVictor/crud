<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends Model
{
    use HasFactory;


    //  /**
    //  * La relación muchos a muchos con los usuarios.
    //  *
    //  * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
    //  */
    // public function users()
    // {
    //     return $this->belongsToMany(User::class, 'model_has_roles', 'role_id', 'model_id');
    // }

}
