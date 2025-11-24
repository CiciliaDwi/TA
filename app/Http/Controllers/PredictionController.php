<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PredictionController extends Controller
{
     public function showForm()
    {
        // Mengirim data hasil prediksi null ke view pada awalnya
        return view('prediksi.prediksi', [
            'prediction_result' => null,
            // Memberikan URL API Python ke View untuk panduan
            'pythonApiUrl' => 'http://localhost:5000/predict' 
        ]);
    }

    /**
     * Proses data input dan panggil API Python untuk mendapatkan prediksi.
     */
    public function predict(Request $request)
    {
        // 1. Validasi Input (tanpa `harga_beli`)
        $validatedData = $request->validate([
            'qty_konversi' => 'required|numeric|min:0',
            'kategori_id' => 'required|integer|min:1',
        ]);

        // 2. Tentukan Endpoint API Python
        $pythonApiUrl = 'http://localhost:5000/predict';
        
        try {
            // 3. Kirim data ke API Python menggunakan HTTP Client
            $payload = [
                // PASTIKAN KEY/NAMA FIELD DI SINI SAMA PERSIS DENGAN YANG DITERIMA DI PYTHON
                'qty_konversi' => $validatedData['qty_konversi'],
                'kategori_id' => $validatedData['kategori_id'], // Mengirim hanya nilai, bukan seluruh array validasi
            ];

            Log::info('Prediction request payload: ' . json_encode($payload));

            $response = Http::timeout(30)->post($pythonApiUrl, $payload);

            Log::info('Python API response status: ' . $response->status());
            Log::info('Python API response body: ' . $response->body());

            // Cek apakah request berhasil
            if ($response->successful()) {
                $responseData = $response->json();
                
                // Pastikan key 'prediction' ada dalam respons JSON
                if (!isset($responseData['prediction'])) {
                     throw new \Exception("Respons API Python tidak memiliki key 'prediction'.");
                }
                
                $predictedQuantity = $responseData['prediction'];

                // 4. Kembali ke view dengan hasil prediksi
                return view('prediksi.prediksi', [
                    'prediction_result' => $predictedQuantity,
                    'input_data' => $validatedData,
                    'pythonApiUrl' => $pythonApiUrl // Kirimkan lagi URL API ke View
                ]);
            } else {
                // Jika API Python mengembalikan error (misal 404/500)
                $errorBody = $response->body();
                Log::error("Python API Error [Status: {$response->status()}]: " . $errorBody);
                return redirect()->back()->withInput()->with('error', "API Prediksi Python mengembalikan kesalahan (Status: {$response->status()}). Cek log server untuk detail: {$errorBody}");
            }

        } catch (\Exception $e) {
            // Jika ada kesalahan koneksi (misal, server Python mati/timeout)
            Log::error("Connection Error to Python API: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Gagal terhubung ke Server Prediksi Python. Pastikan server berjalan di ' . $pythonApiUrl);
        }
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
