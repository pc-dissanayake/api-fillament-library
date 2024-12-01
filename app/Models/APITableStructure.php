<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class APITableStructure extends Model
{
    use HasFactory, HasUuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'api_table_structures';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The data type of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'api_job_id',
        'column_name',
        'column_type',
        'length',
        'attributes',
        'is_required',
        'is_nullable',
        'default_value',
        'table_key',
        'comments',
        'laravel_validation_rule',
        'laravel_validation_rule_value',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'attributes' => 'array',
        'is_required' => 'boolean',
        'is_nullable' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = Str::uuid()->toString();
        });
    }

    /**
     * Get the API job that owns the table structure.
     */
    public function apiJob()
    {
        return $this->belongsTo(APIJobs::class, 'api_job_id', 'id');
    }
}