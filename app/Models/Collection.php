<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
    protected $fillable = [
        'user_id',
        'title',
        'json_file',
        'access_type',
        'project_name'
    ];

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

    /**
     * A scope to retrieve collections for admin.
     *
     * @param mixed $query
     * @return mixed
     */
    public function scopeAdmin($query): mixed
    {
        return $query;
    }

    /**
     * A scope to retrieve only programmer-owned collections.
     *
     * @param $query
     * @param $user_id
     * @return mixed
     */
    public function scopeProgrammer($query, $user_id): mixed
    {
        return $query->where('user_id', $user_id);
    }

    /**
     * A scope to retrieve only public collections.
     *
     * @param $query
     * @return mixed
     */
    public function scopePublic($query): mixed
    {
        return $query->where('access_type', self::COLLECTION_ACCESS_TYPE_PUBLIC);
    }

    /**
     * Retrieves the source code associated with this collection.
     *
     * @return HasMany
     */
    public function sourceCode(): HasMany
    {
        return $this->hasMany(SourceCode::class, 'collection_id', 'id');
    }

}
