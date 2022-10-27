<?php

namespace App\Models;

use App\Models\Traits\RelatesToTeams;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Staudenmeir\LaravelAdjacencyList\Eloquent\HasRecursiveRelationships;

class Obj extends Model
{
    use HasFactory;
    use RelatesToTeams;
    use HasRecursiveRelationships;

    protected $table = 'objects';

    protected $fillable = [
        'parent_id'
    ];

    public static function booted()
    {
        static::creating(function ($model) {
            $model->uuid = Str::uuid();
        });

        static::deleting(function ($model) {
            optional($model->objectable)->delete();
            $model->descendants->each->delete();
        });
    }

    public function objectable()
    {
        return $this->morphTo();
    }

//    public function children()
//    {
//        return $this->hasMany(Obj::class, 'parent_id', 'id');
//    }
//
//    public function parent()
//    {
//        return $this->belongsTo(Obj::class, 'parent_id', 'id');
//    }
//
//    public function ancestors()
//    {
//        $ancestor = $this;
//        $ancestors = collect();
//
//        while ($ancestor->parent) {
//            $ancestor = $ancestor->parent;
//            $ancestors->push($ancestor);
//        }
//
//        return $ancestors;
//    }
}
