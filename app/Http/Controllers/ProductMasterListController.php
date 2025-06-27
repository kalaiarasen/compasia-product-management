<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessProductFile;
use App\Models\ProductMasterList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductMasterListController extends Controller
{
    public function index(Request $request)
    {
        $query = ProductMasterList::query()->orderBy('product_id');

        if ($request->has('product_id')) {
            $query->where('product_id', 'like', '%' . $request->product_id . '%');
        }

        $products = $query->paginate(5);
        return response()->json($products);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        $productFilePath = $request->file('file')->store('product_files');

        $jobId = Str::uuid();
        DB::table('job_trackings')->insert([
            'tracking_id' => $jobId,
            'status' => 'pending',
        ]);
        ProcessProductFile::dispatch($productFilePath, $jobId);

        return response()->json(['message' => 'File uploaded and processing started', 'job_id' => $jobId]);
    }

    public function queueStatus($jobId)
    {
        $job = DB::table('job_trackings')->where('tracking_id', $jobId)->first();
        return response()->json([
            'completed' => $job && $job->status === 'completed',
            'failed' => !$job
        ]);
    }
}
