<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model{
    protected $fillable=["code",'name','type','parent','status','parent_id','organization_id','comment'];

    // Relación para obtener la categoría padre
    // El método parent() establece una relación de pertenencia (belongsTo) para obtener la categoría padre.
    public function parent()
    {
        // return $this->belongsTo(Category::class, 'parent_id');
        return $this->belongsTo(Category::class, 'parent_id', 'id');
    }

    // Relación para obtener las subcategorías
    // El método children() establece una relación de uno a muchos (hasMany) para obtener las subcategorías.
    public function children()
    {
        // return $this->hasMany(Category::class, 'parent_id');
        return $this->hasMany(Category::class, 'parent_id', 'id');
    }
}

// // example
// // Obtener la categoría padre de una categoría específica
// $category = Category::find(1);
// $parentCategory = $category->parent;

// // Obtener las subcategorías de una categoría específica
// $childrenCategories = $category->children;
