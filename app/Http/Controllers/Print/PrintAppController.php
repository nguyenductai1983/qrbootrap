<?php

namespace App\Http\Controllers\Print;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Events\PrintLabelAppEvent;
use Illuminate\Support\Facades\Log;
use App\Models\PrintStation;
use App\Models\PrintJob;

class PrintAppController extends Controller
{

    /**
     * Lấy danh sách lệnh in đang chờ cho App C# khi kết nối lại
     */
    public function pendingJobs($station_token)
    {
        $station = PrintStation::where('station_token', $station_token)->first();

        if (!$station) {
            return response()->json(['success' => false, 'message' => 'Station not found'], 404);
        }

        $jobs = PrintJob::with('item')
            ->where('printer_mac', $station->code)
            ->where('status', PrintJob::STATUS_PENDING)
            ->get();

        if ($jobs->isNotEmpty()) {
            PrintJob::whereIn('id', $jobs->pluck('id'))->update(['status' => PrintJob::STATUS_PRINTING]);
        }

        $formattedJobs = $jobs->map(function ($job) use ($station) {
            return [
                'JobId' => $job->id,
                'Path' => $station->template_name,
                'Data' => [
                    ['Name' => 'MaSP', 'Value' => $job->item->code ?? ''],
                    ['Name' => 'TenSP', 'Value' => $job->item->product->name ?? ''],
                ]
            ];
        });

        return response()->json([
            'success' => true,
            'jobs' => $formattedJobs
        ]);
    }

    /**
     * Nhận phản hồi từ ứng dụng C# (API)
     */
    public function receiveStatus(Request $request)
    {
        // Nhận JobId và Status từ C#
        $jobId = $request->input('job_id') ?? $request->input('JobId');
        $status = $request->input('status') ?? $request->input('Status');

        if ($jobId) {
            $job = PrintJob::find($jobId);
            if ($job) {
                $job->update(['status' => (int) $status]);

                return response()->json([
                    'success' => true,
                    'message' => "Job {$jobId} status updated.",
                    'received_at' => now()->toDateTimeString()
                ]);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid JobId or Job not found.'
        ], 400);
    }
    public function getSocketConfig()
    {
        return response()->json([
            'protocol' => '7',
            'client' => 'js',
            'version' => '7.0.3',
            'flash' => 'false',
            'app_key' => config('reverb.apps.common.key'), // Lấy từ env Laravel
        ]);
    }
}
