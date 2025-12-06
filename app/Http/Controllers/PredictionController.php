<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Nota_Jual_Detil;
use App\Models\Prediction;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PredictionController extends Controller
{
    public function __construct(
        public PendingRequest $client
    ) {
        $baseUrl = config('app.api.fuzzy_base_url');
        $this->client = Http::baseUrl($baseUrl);
    }

    public function showForm()
    {
        $products = Barang::select(['KodeBarang', 'Nama'])->get();

        return view('prediksi.prediksi', compact('products'));
    }

    /**
     * Proses data input dan panggil API Python untuk mendapatkan prediksi.
     */
    public function predict(Request $request)
    {
        $path = '/predict';
        $validatedData = $request->validate([
            'qty' => 'required|numeric|min:0',
            'product_code' => 'required',
        ]);

        $validatedData['product_name'] = Barang::find($validatedData['product_code'])?->Nama ?? 'Unknown';

        try {
            $response = $this->client->post($path, $validatedData);

            return $response->json();

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function getTaskInfo(Request $request)
    {
        $validatedData = $request->validate([
            'task_id' => 'required',
        ]);

        $taskId = $validatedData['task_id'];

        $path = "/task/{$taskId}";

        try {
            $response = $this->client->get($path);
            $result = $response->json();

            Log::channel('PREDICTION')->debug('Task Completed', $result);

            return $result;

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function getOmzetPerItem()
    {
        $currentMonth = now()->month;
        $date = Carbon::createFromDate(now()->year, $currentMonth)->format('M Y');

        $details = Nota_Jual_Detil::query()
            ->with('barang.kategori')
            ->whereMonth('created_at', $currentMonth)
            ->get();

        $data = $details
            ->groupBy(fn ($item) => $item->barang->KodeBarang)
            ->map(fn ($group, $key) => [
                'code' => $key,
                'product_name' => $group->first()->barang->Nama,
                'category_name' => $group->first()->barang->kategori->Nama,
                'merk' => $group->first()->barang->Merek,
                'qty' => $group->sum('Jumlah'),
                'omzet' => $group->sum('Total'),
                'date' => $date,
            ])
            ->values();

        return response()->json([
            'success' => true,
            'message' => "Omzet per barang berhasil diambil ($date)",
            'data' => $data,
        ]);
    }

    public function savePrediction(Request $request)
    {
        $validatedData = $request->validate([
            'result_qty' => ['required'],
            'category_code' => ['required'],
        ]);

        Prediction::create($validatedData);

        return response()->json([
            'success' => true,
        ]);
    }
}
