<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Collection extends Model
{
    use HasFactory, HasUuids;

    public const COLLECTION_ACCESS_TYPE_PUBLIC = 'public';
    public const COLLECTION_ACCESS_TYPE_PRIVATE = 'private';

    /**
     * The table that associated with the model.
     *
     * @var string
     */
    protected $table = 'collections';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = ['user_id', 'title', 'json_file', 'access_type'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string,string>
     */
    protected $casts = ['json_file' => 'array'];


    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function scopeAdmin($query)
    {
        return $query;
    }

    public function scopeProgrammer($query, $user_id)
    {
        return $query->where('user_id', $user_id);
    }

    public function scopePublic($query)
    {
        return $query->where('access_type', self::COLLECTION_ACCESS_TYPE_PUBLIC);
    }

}
