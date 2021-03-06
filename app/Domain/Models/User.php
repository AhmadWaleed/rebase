<?php

namespace App\Domain\Models;

use App\Enums\UserRole;
use App\Domain\Models\Workspace;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /** @var bool */
    public $incrementing = false;

    /** @var string */
    protected $connection = 'workspace';

    /** @var array */
    protected $guarded = [ ];

    /** @var array */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /** @var array */
    protected $casts = [
        'id' => 'uuid',
        'workspace_id' => 'uuid',
        'email_verified_at' => 'datetime',
    ];

    public function workspace(): HasOne
    {
        return $this->hasOne(Workspace::class);
    }
}
