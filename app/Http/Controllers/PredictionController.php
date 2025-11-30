<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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
        $categories = Kategori::all();
        // Mengirim data hasil prediksi null ke view pada awalnya
        // return view('prediksi.prediksi', [
        //     'prediction_result' => null,
        //     // Memberikan URL API Python ke View untuk panduan
        //     'pythonApiUrl' => 'http://localhost:5000/predict',
        // ]);

        return view('prediksi.prediksi', compact('categories'));
    }

    /**
     * Proses data input dan panggil API Python untuk mendapatkan prediksi.
     */
    public function predict(Request $request)
    {
        $path = '/predict';
        $validatedData = $request->validate([
            'qty' => 'required|numeric|min:0',
            'category' => 'required',
        ]);

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

            return $response->json();

        } catch (\Throwable $th) {
            throw $th;
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
