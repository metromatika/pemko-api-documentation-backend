<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Role extends Model
{
    use HasFactory, HasUuids;

    public const ROLE_ADMINISTRATOR = 'Administrator';
    public const ROLE_PROGRAMMER = 'Programmer';
    public const ROLE_REGULAR_USER = 'Regular User';

    public const ROLE_ALIAS_ADMINISTRATOR = 'administrator';
    public const ROLE_ALIAS_PROGRAMMER = 'programmer';
    public const ROLE_ALIAS_REGULAR_USER = 'regular_user';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'roles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = ['name', 'alias'];

}
