<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Language;
use App\Models\Question;
use App\Models\QuestionReport;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class QuestionController extends Controller
{
    public function index()
    {
        $languages = Language::where('status', 1)->get();
        $categories = Category::where('type', 1)->get();
        
        return view('questions_create', compact('languages', 'categories'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'language_id' => 'nullable|exists:languages,id',
            'category' => 'required|exists:categories,id',
            'subcategory' => 'nullable|exists:subcategories,id',
            'question' => 'required',
            'question_type' => 'required|in:1,2',
            'a' => 'required',
            'b' => 'required',
            'c' => 'required_if:question_type,1',
            'd' => 'required_if:question_type,1',
            'e' => 'nullable',
            'answer' => 'required',
            'level' => 'required|integer|min:1',
            'note' => 'nullable',
            'file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $question = new Question();
        $question->language_id = $request->language_id ?? 0;
        $question->category_id = $request->category;
        $question->subcategory_id = $request->subcategory ?? 0;
        $question->question = $request->question;
        $question->question_type = $request->question_type;
        $question->optiona = $request->a;
        $question->optionb = $request->b;
        $question->optionc = $request->question_type == 1 ? $request->c : "";
        $question->optiond = $request->question_type == 1 ? $request->d : "";
        $question->optione = $request->e ?? "";
        $question->answer = $request->answer;
        $question->level = $request->level;
        $question->note = $request->note;

        if ($request->hasFile('file')) {
            $image = $request->file('file');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/questions'), $imageName);
            $question->image = $imageName;
        }

        $question->save();

        // Store session data for form persistence
        session([$request->segment(1) => [
            'language_id' => $request->language_id ?? 0,
            'category' => $request->category,
            'subcategory' => $request->subcategory ?? 0
        ]]);

        return redirect()->route('questions.index')->with('success', 'Question created successfully');
    }

    public function edit($id)
    {
        $question = Question::findOrFail($id);
        $languages = Language::where('status', 1)->get();
        $categories = Category::where('type', 1)->get();
        $subcategories = Subcategory::where('maincat_id', $question->category_id)->get();
        
        return view('questions_create', compact('question', 'languages', 'categories', 'subcategories'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'language_id' => 'nullable|exists:languages,id',
            'category' => 'required|exists:categories,id',
            'subcategory' => 'nullable|exists:subcategories,id',
            'question' => 'required',
            'question_type' => 'required|in:1,2',
            'a' => 'required',
            'b' => 'required',
            'c' => 'required_if:question_type,1',
            'd' => 'required_if:question_type,1',
            'e' => 'nullable',
            'answer' => 'required',
            'level' => 'required|integer|min:1',
            'note' => 'nullable',
            'file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $question = Question::findOrFail($id);
        
        if (config('app.language_mode')) {
            $question->language_id = $request->language_id ?? 0;
        }
        
        $question->category_id = $request->category;
        $question->subcategory_id = $request->subcategory ?? 0;
        $question->question = $request->question;
        $question->question_type = $request->question_type;
        $question->optiona = $request->a;
        $question->optionb = $request->b;
        $question->optionc = $request->question_type == 1 ? $request->c : "";
        $question->optiond = $request->question_type == 1 ? $request->d : "";
        $question->optione = $request->e ?? "";
        $question->answer = $request->answer;
        $question->level = $request->level;
        $question->note = $request->note;

        if ($request->hasFile('file')) {
            // Delete old image
            if ($question->image && File::exists(public_path('images/questions/' . $question->image))) {
                File::delete(public_path('images/questions/' . $question->image));
            }
            
            $image = $request->file('file');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/questions'), $imageName);
            $question->image = $imageName;
        }

        $question->save();

        return redirect()->route('questions.manage')->with('success', 'Question updated successfully');
    }

    public function destroy($id)
    {
        $question = Question::findOrFail($id);
        
        // Delete image
        if ($question->image && File::exists(public_path('images/questions/' . $question->image))) {
            File::delete(public_path('images/questions/' . $question->image));
        }
        
        // Delete related reports
        $question->reports()->delete();
        
        // Delete related bookmarks
        $question->bookmarks()->delete();
        
        $question->delete();
        
        return response()->json(['success' => true]);
    }

    public function manage()
    {
        $languages = Language::where('status', 1)->get();
        $categories = Category::where('type', 1)->get();
        
        return view('questions_manage', compact('languages', 'categories'));
    }

    public function reports()
    {
        $languages = Language::where('status', 1)->get();
        $categories = Category::where('type', 1)->get();
        
        return view('question_reports', compact('languages', 'categories'));
    }

    public function editReport($id)
    {
        $report = QuestionReport::findOrFail($id);
        $question = $report->question;
        $languages = Language::where('status', 1)->get();
        $categories = Category::where('type', 1)->get();
        $subcategories = Subcategory::where('maincat_id', $question->category_id)->get();
        
        return view('questions_create', compact('question', 'languages', 'categories', 'subcategories'));
    }

    public function deleteReport($id)
    {
        $report = QuestionReport::findOrFail($id);
        $report->delete();
        
        return response()->json(['success' => true]);
    }

    public function import()
    {
        $languages = Language::where('status', 1)->get();
        $categories = Category::where('type', 1)->get();
        
        return view('import_questions', compact('languages', 'categories'));
    }

    public function importProcess(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:csv,txt',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $file = $request->file('file');
        $csvData = file_get_contents($file);
        $rows = array_map('str_getcsv', explode("\n", $csvData));
        $header = array_shift($rows);
        
        $csvArray = [];
        foreach ($rows as $row) {
            if (count($row) > 2) {
                $csvArray[] = array_combine($header, $row);
            }
        }
        
        $errorCount = 0;
        $successCount = 0;
        
        foreach ($csvArray as $data) {
            if (empty($data['category']) || empty($data['question_type']) || empty($data['question']) || 
                empty($data['optiona']) || empty($data['optionb']) || empty($data['answer']) || empty($data['level'])) {
                $errorCount++;
                continue;
            }
            
            if ($data['question_type'] == 1 && (empty($data['optionc']) || empty($data['optiond']))) {
                $errorCount++;
                continue;
            }
            
            $question = new Question();
            $question->category_id = $data['category'];
            $question->subcategory_id = $data['subcategory'] ?? 0;
            $question->language_id = config('app.language_mode') ? $data['language_id'] : 0;
            $question->question = $data['question'];
            $question->question_type = $data['question_type'];
            $question->optiona = $data['optiona'];
            $question->optionb = $data['optionb'];
            $question->optionc = $data['optionc'] ?? '';
            $question->optiond = $data['optiond'] ?? '';
            $question->optione = $data['optione'] ?? '';
            $question->answer = strtolower(trim($data['answer']));
            $question->level = $data['level'];
            $question->note = $data['note'] ?? '';
            
            $question->save();
            $successCount++;
        }
        
        if ($errorCount > 0) {
            return redirect()->back()->with('error', "Imported $successCount questions successfully. $errorCount questions had errors and were skipped.");
        }
        
        return redirect()->back()->with('success', "Imported $successCount questions successfully.");
    }
}