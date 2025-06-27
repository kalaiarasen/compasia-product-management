<?php

namespace App\Jobs;

use App\Models\ProductMasterList;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ProcessProductFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $filePath;
    private $trackingId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($filePath, $trackingId)
    {
        $this->filePath = $filePath;
        $this->trackingId = $trackingId;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        DB::table('job_trackings')->where('tracking_id', $this->trackingId)->update(['status' => 'processing']);

        $file = Storage::path($this->filePath);
        $spreadsheet = IOFactory::load($file);
        $worksheet = $spreadsheet->getActiveSheet();
        $productArray = $worksheet->toArray();
        $productRows = array_slice($productArray, 1);

        foreach ($productRows as $productRow) {
            $productId = $productRow[0];
            $status = strtolower(trim($productRow[5]));

            $product = ProductMasterList::where('product_id', $productId)->first();
            if ($product) {
                if ($status === 'sold') {
                    $product->quantity -= 1;
                } elseif ($status === 'buy') {
                    $product->quantity += 1;
                }
                $product->save();

            }
        }
        DB::table('job_trackings')->where('tracking_id', $this->trackingId)->update(['status' => 'completed']);
        Storage::delete($this->filePath);
    }
}
