<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;
    protected $fillable = ['name','id', 'description', 'image', 'parent_category'];

    public function products(){
        $this->belongsToMany(Product::class);
    }

    public function parentCategory()
    {
        return $this->belongsTo(Category::class, 'parent_category');
    }

    public function childCategories()
    {
        return $this->hasMany(Category::class, 'parent_category');
    }
}