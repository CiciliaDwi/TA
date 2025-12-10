<?php

namespace App\Jobs;

use App\Models\Nota_Jual_Detil;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UploadDatasetJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;         // retry 5x if failure

    public $timeout = 120;     // job timeout 120 detik

    public PendingRequest $connectorClient;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int|string $month,
        public int|string $year,
        public string $period,
        public string $taskId,
        public string $taskCategory,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $baseUrl = config('app.api.fuzzy_base_url');
        $this->connectorClient = Http::baseUrl($baseUrl);

        $period = $this->period;

        $details = Nota_Jual_Detil::with('barang.kategori')
            ->whereMonth('created_at', $this->month)
            ->whereYear('created_at', $this->year)
            ->get();

        if ($details->isEmpty()) {
            Log::warning("⚠ Report periode $period tidak ditemukan, skip upload.");

            $taskResults = getState(STATE_TASK_RESULT, []);
            $collection = collect($taskResults);
            $result = [
                'taskId' => $this->taskId,
                'taskCategory' => $this->taskCategory,
                'status' => TASK_FAILED,
                'period' => $period,
            ];
            $collection->push($result);
            $parseResults = $collection->toArray();
            setState(STATE_TASK_RESULT, $parseResults);

            return;
        }

        $arrData = $details->groupBy(fn ($item) => $item->barang->KodeBarang)
            ->map(function ($group, $key) use ($period) {
                $product = $group->first()->barang;
                $qty = $group->sum('Jumlah');
                $unit = $product->Satuan;

                return [
                    'Kode' => $key,
                    'Nama Produk' => $product->Nama,
                    'Kategori Produk' => $product->kategori->Nama,
                    'Merek' => $product->Merek,
                    'PID' => '',
                    'Qty' => "{$qty} {$unit}",
                    'Satuan' => $unit,
                    'Qty Konversi' => (int) $qty,
                    'Omzet' => $product->HargaJual * $qty,
                    'Period' => $period,
                ];
            })
            ->values()
            ->toArray();

        // create csv file
        $parsePeriod = Str::replace(' ', '_', $period);
        $filename = "Bulan_{$parsePeriod}.csv";
        $filepath = "reports/$filename";
        $path = storage_path("app/public/$filepath");
        if (! file_exists(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }

        $handle = fopen($path, 'w');
        fputcsv($handle, array_keys($arrData[0]));
        foreach ($arrData as $row) {
            fputcsv($handle, $row);
        }
        fclose($handle);

        // Upload to FastAPI
        $fileContent = file_get_contents($path);
        $response = $this->connectorClient
            ->attach('file', $fileContent, $filename)
            ->post('/upload-dataset');

        if ($response->failed()) {
            throw new \Exception('Gagal upload ke FastAPI: '.$response->body());
        }

        Log::info("📦 Dataset $period berhasil diupload", [
            'filename' => $filename,
            'response' => $response->json(),
        ]);

        $taskResults = getState(STATE_TASK_RESULT, []);
        $collection = collect($taskResults);
        $result = [
            'taskId' => $this->taskId,
            'taskCategory' => $this->taskCategory,
            'status' => TASK_SUCCESS,
            'filename' => $filename,
            'filepath' => asset('storage/'.$filepath),
        ];
        $collection->push($result);
        $parseResults = $collection->toArray();

        setState(STATE_TASK_RESULT, $parseResults);
        // unlink($path); // delete after upload
    }
}
