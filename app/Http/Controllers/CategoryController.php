<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $languages = Language::where('status', 1)->get();
        return view('category', compact('languages'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:categories,slug',
            'file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',
            'is_premium' => 'required|in:0,1',
            'coins' => 'required_if:is_premium,1|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $category = new Category();
        $category->language_id = $request->language_id ?? 0;
        $category->category_name = $request->name;
        $category->slug = Str::slug($request->slug);
        $category->type = $request->type ?? 1;
        $category->is_premium = $request->is_premium;
        $category->coins = $request->is_premium ? $request->coins : 0;
        $category->row_order = 0;

        if ($request->hasFile('file')) {
            $image = $request->file('file');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/category'), $imageName);
            $category->image = $imageName;
        }

        $category->save();

        return redirect()->route('category.index')->with('success', 'Category created successfully');
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'edit_slug' => 'required|unique:categories,slug,' . $id,
            'update_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',
            'edit_is_premium' => 'required|in:0,1',
            'edit_coins' => 'required_if:edit_is_premium,1|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $category = Category::findOrFail($id);
        
        if (config('app.language_mode')) {
            $category->language_id = $request->language_id ?? 0;
        }
        
        $category->category_name = $request->name;
        $category->slug = Str::slug($request->edit_slug);
        $category->is_premium = $request->edit_is_premium;
        $category->coins = $request->edit_is_premium ? $request->edit_coins : 0;

        if ($request->hasFile('update_file')) {
            // Delete old image
            if ($category->image && File::exists(public_path('images/category/' . $category->image))) {
                File::delete(public_path('images/category/' . $category->image));
            }
            
            $image = $request->file('update_file');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/category'), $imageName);
            $category->image = $imageName;
        }

        $category->save();

        return redirect()->route('category.index')->with('success', 'Category updated successfully');
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        
        // Delete image
        if ($category->image && File::exists(public_path('images/category/' . $category->image))) {
            File::delete(public_path('images/category/' . $category->image));
        }
        
        // Delete related subcategories
        foreach ($category->subcategories as $subcategory) {
            if ($subcategory->image && File::exists(public_path('images/subcategory/' . $subcategory->image))) {
                File::delete(public_path('images/subcategory/' . $subcategory->image));
            }
        }
        
        // Delete related questions
        foreach ($category->questions as $question) {
            if ($question->image && File::exists(public_path('images/questions/' . $question->image))) {
                File::delete(public_path('images/questions/' . $question->image));
            }
        }
        
        $category->delete();
        
        return response()->json(['success' => true]);
    }

    public function updateOrder(Request $request)
    {
        $ids = explode(',', $request->row_order);
        
        foreach ($ids as $index => $id) {
            Category::where('id', $id)->update(['row_order' => $index]);
        }
        
        return redirect()->back()->with('success', 'Category order updated successfully');
    }

    public function getSlug(Request $request)
    {
        $name = $request->category_name;
        $editId = $request->id;
        
        // Check if the category name is in English and contains only letters, numbers, spaces and hyphens
        if (!preg_match('/^[a-z0-9- ]+$/i', $name)) {
            return '';
        }
        
        $slug = Str::slug($name);
        
        if (!Category::isUniqueSlug($slug, $editId)) {
            $counter = 1;
            $originalSlug = $slug;
            
            while (!Category::isUniqueSlug($slug, $editId)) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
        }
        
        return $slug;
    }

    public function verifySlug(Request $request)
    {
        $slug = $request->slug;
        $id = $request->id;
        $slug = trim($slug);
        
        if (!preg_match('/^[a-z0-9-]+$/i', $slug)) {
            return response()->json(3);
        } else if (!Category::isUniqueSlug($slug, $id)) {
            return response()->json(false);
        }
        
        return response()->json(true);
    }
}