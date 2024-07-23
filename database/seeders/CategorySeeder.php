<?php

namespace Database\Seeders;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Category::factory()->count(4)->create();
        $category = Category::factory()
            ->has(Product::factory()->count(3))
            ->create();

}}