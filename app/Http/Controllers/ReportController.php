<?php

namespace App\Http\Controllers;

use App\Jobs\UploadDatasetJob;
use App\Models\Barang;
use App\Models\Nota_Jual;
use App\Models\Nota_Jual_Detil;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bestSellers = Nota_Jual_Detil::select(
            'barang.KodeBarang',
            'barang.Nama as NamaBarang',
            DB::raw('SUM(nota_jual_detil.Jumlah) as TotalTerjual'),
            DB::raw('SUM(nota_jual_detil.Jumlah * nota_jual_detil.Harga) as TotalPendapatan')
        )
            ->join('barang', 'barang.KodeBarang', '=', 'nota_jual_detil.KodeBarang')
            ->join('nota_jual', 'nota_jual.NoNota', '=', 'nota_jual_detil.NoNota')
            ->whereMonth('nota_jual.tanggal', date('m'))
            ->whereYear('nota_jual.tanggal', date('Y'))
            ->groupBy('barang.KodeBarang', 'barang.Nama')
            ->orderBy('TotalTerjual', 'desc')
            ->take(10)
            ->get();

        // Stok menipis (< 10)
        $lowStock = Barang::with('kategori')
            ->where('Stok', '<', 10)
            ->orderBy('Stok', 'asc')
            ->get();

        // Semua penjualan
        $allSales = Nota_Jual::with(['pegawai', 'detil.barang'])
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('report.report', compact('bestSellers', 'lowStock', 'allSales'));
    }

    public function triggerDownloadReport(Request $request)
    {
        $date = $request->get('date');

        if (! $date) {
            return response()->json([
                'success' => false,
                'message' => 'Unprocessable Content',
                'errors' => [
                    [
                        'key' => 'date',
                        'message' => 'Silahkan pilih bulan',
                    ],
                ],
            ], 422);
        }

        [$year, $month] = explode('-', $date);

        $parseDate = Carbon::createFromDate($year, $month);
        $period = $parseDate->format('F Y');
        $taskId = Str::uuid()->toString();
        $taskCategory = TASK_CATEGORY_DOWNLOAD_REPORT;

        try {
            UploadDatasetJob::dispatch($month, $year, $period, $taskId, $taskCategory);

            Log::debug("Dataset period $period upload in background process...");

            return response()->json([
                'success' => true,
                'message' => "Laporan periode $period sedang diproses dan akan siap diunduh setelah selesai.",
                'data' => ['taskId' => $taskId, 'taskCategory' => $taskCategory],
            ]);

        } catch (\Throwable $th) {
            Log::error("Dataset period $period gagal diupload", ['error' => $th->getMessage()]);
            throw $th;

            return response()->json([
                'success' => false,
                'message' => "Report period $period gagal download",
            ], 500);
        }
    }

    public function downloadReport(string $taskId, string $taskCategory)
    {
        $taskResults = getState(STATE_TASK_RESULT);
        $collection = collect($taskResults);
        $data = $collection->where('taskId', $taskId)->where('taskCategory', $taskCategory)->first();

        if (!$data) {
            return response()->json([
                'success' => true,
                'message' => 'Laporan sedang diproses. Mohon tunggu sebentar...',
                'data' => null,
            ]);
        }
       $collection->where('taskId', $taskId)->pop();
       $newTaskResults = $collection->toArray();
       setState(STATE_TASK_RESULT, $newTaskResults);

        return response()->json([
            'success' => true,
            'message' => 'Laporan berhasil diproses dan siap diunduh.',
            'data' => $data,
        ]);
    }
}
