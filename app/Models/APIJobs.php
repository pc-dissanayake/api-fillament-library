<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class APIJobs extends Model
{
    use HasFactory,HasUuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'a_p_i_jobs';

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
        'public_uuid',
        'title',
        'description',
        'api_prefix_uuid',
        'url',
        'active',
        'locked',
        'table_name', //uuid
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
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
            $model->public_uuid = Str::uuid()->toString();
            $model->table_uuid = 'tbl-'.Str::uuid()->toString();
            $model->api_prefix_uuid = Str::uuid()->toString();
        });

        static::created(function ($model) {
            $model->tableStructure()->create([
                'column_name' => 'id',
                'column_type' => 'id',
                'is_required' => true,
                'is_nullable' => false,
                'table_key' => 'primary',
                'comments' => 'Primary key for the table',
                'laravel_validation_rule' => 'required|uuid',
            ]);
        });
    }



    /**
     * Get the parameters for the API job.
     */
    public function tableStructure()
    {
        return $this->hasMany(APITableStructure::class, 'api_job_id', 'id');
    }


    




}