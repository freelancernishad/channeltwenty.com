<?php
namespace App\Http\Controllers;

use App\Models\SelectedArticle;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class SelectedArticleController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->input('date');
        $selectedArticles = SelectedArticle::with('article')
                                        //    ->whereDate('date', $date)
                                           ->paginate(10);

        return response()->json($selectedArticles);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'article_ids' => 'required|array',
            'article_ids.*' => 'exists:articles,id',
            // 'date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $articleIds = $request->input('article_ids');
        $date = date("Y-m-d");

        $selectedArticles = [];
        $duplicateArticles = [];

        foreach ($articleIds as $articleId) {
            $exists = SelectedArticle::where('article_id', $articleId)->whereDate('date', $date)->exists();
            if ($exists) {
                $duplicateArticles[] = $articleId;
            } else {
                $selectedArticles[] = SelectedArticle::create([
                    'article_id' => $articleId,
                    'date' => $date,
                ]);
            }
        }

        $response = [
            'created' => $selectedArticles,
        ];

        if (!empty($duplicateArticles)) {
            $response['duplicates'] = $duplicateArticles;
            $response['message'] = 'Some articles were not added because they already exist for the given date.';
        }

        return response()->json($response, 201);
    }

    public function show($id)
    {
        $selectedArticle = SelectedArticle::with('article')->findOrFail($id);

        return response()->json($selectedArticle);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'article_id' => 'required|exists:articles,id',
            // 'date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $selectedArticle = SelectedArticle::findOrFail($id);
        $selectedArticle->update($validator->validated());

        return response()->json($selectedArticle);
    }

    public function destroy($id)
    {
        $selectedArticle = SelectedArticle::findOrFail($id);
        $selectedArticle->delete();

        return response()->json(null, 204);
    }

    // Method to delete SelectedArticle entries by date
    public function deleteByDate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $date = $request->input('date');

        // Delete records by date
        $deletedCount = SelectedArticle::whereDate('date', $date)->delete();

        return response()->json([
            'message' => 'Selected articles deleted successfully.',
            'deleted_count' => $deletedCount,
        ], 200);
    }


    public function relatedArticles($id)
    {
        $selectedArticle = SelectedArticle::with('article')->findOrFail($id);
        $article = $selectedArticle->article;

        $relatedArticles = Article::whereHas('categories', function ($query) use ($article) {
            $query->whereIn('id', $article->categories()->pluck('id'));
        })
        ->where('id', '!=', $article->id) // Exclude the current article
        ->latest()
        ->limit(10)
        ->get();

        return response()->json($relatedArticles);
    }


     // Method to update multiple SelectedArticle entries by date
     public function updateMultipleByDate(Request $request)
     {
         $validator = Validator::make($request->all(), [
             'date' => 'required|date',
             'article_ids' => 'required|array',
             'article_ids.*' => 'exists:articles,id',
         ]);

         if ($validator->fails()) {
             return response()->json($validator->errors(), 422);
         }

         $date = $request->input('date');
         $articleIds = $request->input('article_ids');

         // Find existing records by date
         $existingRecords = SelectedArticle::whereDate('date', $date)->get();

         // Get the article IDs that are already present
         $existingArticleIds = $existingRecords->pluck('article_id')->toArray();

         // Determine which article IDs are new and which are existing
         $newArticleIds = array_diff($articleIds, $existingArticleIds);

         // Delete existing records that are not in the new article IDs
         SelectedArticle::whereDate('date', $date)
             ->whereNotIn('article_id', $articleIds)
             ->delete();

         // Update existing records
         foreach ($existingRecords as $existingRecord) {
             if (in_array($existingRecord->article_id, $articleIds)) {
                 // Optionally update other fields here if needed
                 $existingRecord->save();
             }
         }

         // Create new records for new article IDs
         foreach ($newArticleIds as $newArticleId) {
             SelectedArticle::create([
                 'article_id' => $newArticleId,
                 'date' => $date,
             ]);
         }

         return response()->json([
             'message' => 'Selected articles updated and new articles added successfully.',
             'updated_records' => SelectedArticle::whereDate('date', $date)->get(),
         ], 200);
     }

     // Method to filter SelectedArticle entries by date
     public function filterByDate(Request $request)
     {
         $validator = Validator::make($request->all(), [
             'date' => 'required|date',
         ]);

         if ($validator->fails()) {
             return response()->json($validator->errors(), 422);
         }

         $date = $request->input('date');

         $selectedArticles = SelectedArticle::whereDate('date', $date)->with('article')->get();

            return response()->json($selectedArticles, 200);
    }







}
