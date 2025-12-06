<?php

namespace App\Jobs;

use App\Models\Nota_Jual_Detil;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UploadDatasetJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;         // retry 5x if failure
    public $timeout = 120;     // job timeout 120 detik
    public PendingRequest $client;
    
    /**
     * Create a new job instance.
     */
    public function __construct(
      public int $month
    ){ }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $baseUrl = config('app.api.fuzzy_base_url');
        $this->client = Http::baseUrl($baseUrl);

        $currentMonth = $this->month;
        $date = Carbon::create(now()->year, $currentMonth)->format('M Y');

        $details = Nota_Jual_Detil::with('barang.kategori')
            ->whereMonth('created_at', $currentMonth)
            ->get();

        if ($details->isEmpty()) {
            Log::warning("⚠ Dataset $date kosong, skip upload.");
            return;
        }

        $arrData = $details->groupBy(fn ($item) => $item->barang->KodeBarang)
            ->map(function ($group, $key) use ($date) {
                $product = $group->first()->barang;
                $qty = $group->sum('Jumlah');
                $unit = $product->Satuan;

                return [
                    "Kode" => $key,
                    "Nama Produk" => $product->Nama,
                    "Kategori Produk" => $product->kategori->Nama,
                    "Merek" => $product->Merek,
                    "PID" => "",
                    "Qty" => "{$qty} {$unit}",
                    "Satuan" => $unit,
                    "Qty Konversi" => (int)$qty,
                    "Omzet" => $product->HargaJual * $qty,
                    "Date" => $date,
                ];
            })
            ->values()
            ->toArray();

        // create csv file
        $filename = "Bulan-{$currentMonth}-{$date}.csv";
        $path = storage_path("app/dataset/$filename");
        if (!file_exists(dirname($path))) mkdir(dirname($path), 0777, true);

        $handle = fopen($path, 'w');
        fputcsv($handle, array_keys($arrData[0]));
        foreach ($arrData as $row) fputcsv($handle, $row);
        fclose($handle);

        // Upload to FastAPI
        $response = $this->client
            ->attach("file", file_get_contents($path), $filename)
            ->post('/upload-dataset');

        if ($response->failed()) {
            throw new \Exception("Gagal upload ke FastAPI: " . $response->body());
        }

        Log::info("📦 Dataset $date berhasil diupload", [
            "filename" => $filename,
            "response" => $response->json()
        ]);

        unlink($path); // delete after upload
    }
}
