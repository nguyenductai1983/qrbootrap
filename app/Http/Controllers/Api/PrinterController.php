<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PrintJob;

class PrinterController extends Controller
{
    /**
     * Get pending print jobs for a specific printer.
     */
    public function pendingJobs($mac)
    {
        $jobs = PrintJob::with('item')->where('printer_mac', $mac)->where('status', PrintJob::STATUS_PENDING)->get();
        if ($jobs->isNotEmpty()) {
            PrintJob::whereIn('id', $jobs->pluck('id'))->update(['status' => PrintJob::STATUS_PRINTING]);
        }

        return response()->json([
            'success' => true,
            'jobs' => $jobs
        ]);
    }

    /**
     * Mark a print job as printed.
     */
    public function markPrinted($jobId)
    {
        $job = PrintJob::find($jobId);

        if (!$job) {
            return response()->json(['success' => false, 'message' => 'Job not found'], 404);
        }

        $job->update(['status' => PrintJob::STATUS_SUCCESS]);

        return response()->json(['success' => true]);
    }
}
