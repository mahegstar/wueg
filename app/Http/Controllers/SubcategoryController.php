<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Language;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SubcategoryController extends Controller
{
    public function index()
    {
        $languages = Language::where('status', 1)->get();
        $type = request()->segment(1);
        $categoryType = config('quiz.category_type')[$type] ?? 1;
        $categories = Category::where('type', $categoryType)->get();
        
        return view('sub_category', compact('languages', 'categories'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'maincat_id' => 'required|exists:categories,id',
            'name' => 'required',
            'slug' => 'required|unique:subcategories,slug',
            'file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $subcategory = new Subcategory();
        $subcategory->language_id = $request->language_id ?? 0;
        $subcategory->maincat_id = $request->maincat_id;
        $subcategory->subcategory_name = $request->name;
        $subcategory->slug = Str::slug($request->slug);
        $subcategory->status = 1;
        $subcategory->is_premium = 0;
        $subcategory->coins = 0;
        $subcategory->row_order = 0;

        if ($request->hasFile('file')) {
            $image = $request->file('file');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/subcategory'), $imageName);
            $subcategory->image = $imageName;
        }

        $subcategory->save();

        return redirect()->back()->with('success', 'Subcategory created successfully');
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'maincat_id' => 'required|exists:categories,id',
            'name' => 'required',
            'slug' => 'required|unique:subcategories,slug,' . $id,
            'update_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',
            'status' => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $subcategory = Subcategory::findOrFail($id);
        
        if (config('app.language_mode')) {
            $subcategory->language_id = $request->language_id ?? 0;
        }
        
        $subcategory->maincat_id = $request->maincat_id;
        $subcategory->subcategory_name = $request->name;
        $subcategory->slug = Str::slug($request->slug);
        $subcategory->status = $request->status;

        if ($request->hasFile('update_file')) {
            // Delete old image
            if ($subcategory->image && File::exists(public_path('images/subcategory/' . $subcategory->image))) {
                File::delete(public_path('images/subcategory/' . $subcategory->image));
            }
            
            $image = $request->file('update_file');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/subcategory'), $imageName);
            $subcategory->image = $imageName;
        }

        $subcategory->save();

        // Update related questions
        $subcategory->questions()->update(['category_id' => $request->maincat_id]);

        return redirect()->back()->with('success', 'Subcategory updated successfully');
    }

    public function destroy($id)
    {
        $subcategory = Subcategory::findOrFail($id);
        
        // Delete image
        if ($subcategory->image && File::exists(public_path('images/subcategory/' . $subcategory->image))) {
            File::delete(public_path('images/subcategory/' . $subcategory->image));
        }
        
        // Delete related questions
        foreach ($subcategory->questions as $question) {
            if ($question->image && File::exists(public_path('images/questions/' . $question->image))) {
                File::delete(public_path('images/questions/' . $question->image));
            }
        }
        
        $subcategory->delete();
        
        return response()->json(['success' => true]);
    }

    public function updateOrder(Request $request)
    {
        $ids = explode(',', $request->row_order1);
        
        foreach ($ids as $index => $id) {
            Subcategory::where('id', $id)->update(['row_order' => $index]);
        }
        
        return redirect()->back()->with('success', 'Subcategory order updated successfully');
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
        
        if (!Subcategory::isUniqueSlug($slug, $editId)) {
            $counter = 1;
            $originalSlug = $slug;
            
            while (!Subcategory::isUniqueSlug($slug, $editId)) {
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
        } else if (!Subcategory::isUniqueSlug($slug, $id)) {
            return response()->json(false);
        }
        
        return response()->json(true);
    }
}