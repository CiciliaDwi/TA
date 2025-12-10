<?php

namespace App\Http\Controllers;

use App\Jobs\UploadDatasetJob;
use App\Models\Barang;
use App\Models\Prediction;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

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

            Log::debug('Task Completed', $result);

            return $result;

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function savePrediction(Request $request)
    {
        $validatedData = $request->validate([
            'response' => ['required'],
            'response.status' => ['required', Rule::in(['DONE', 'RUNNING'])],
            'response.result' => ['required'],
        ]);

        try {

            $response = $validatedData['response'];

            $data['user_id'] = auth('web')->id();
            $data['product_id'] = $response['result']['product_code'];
            $data['result_qty'] = $response['result']['result_qty'];
            $data['response_payload'] = json_encode($response);

            Prediction::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Prediction berhasil disimpan',
            ]);
        } catch (\Throwable $th) {
            Log::error('savePrediction', ['event' => 'savePrediction', 'error' => $th->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Prediction gagal disimpan',
            ], 500);
        }
    }

    // public function uploadDataset()
    // {
    //     $currentMonth = now()->month;
    //     $date = Carbon::createFromDate(now()->year, $currentMonth)->format('M Y');

    //     try {
    //         UploadDatasetJob::dispatch($currentMonth);
    //         Log::debug("Dataset period $date upload in background process...");

    //         return response()->json([
    //             'success' => true,
    //             'message' => "Dataset period $date upload in background process",
    //         ]);

    //     } catch (\Throwable $th) {
    //         Log::error("Dataset period $date gagal diupload", ['error' => $th->getMessage()]);
    //         throw $th;

    //         return response()->json([
    //             'success' => false,
    //             'message' => "Dataset period $date gagal diupload",
    //         ], 500);
    //     }
    // }
}
