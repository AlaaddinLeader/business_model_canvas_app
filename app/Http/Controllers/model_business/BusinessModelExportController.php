<?php

namespace App\Http\Controllers\model_business;

use App\Http\Controllers\Controller;
use App\Models\BusinessModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Facades\Storage;

class BusinessModelExportController extends Controller
{
    /**
     * Export business model as PDF
     */
    public function exportPDF($id)
    {
        $user = Auth::user();

        // Get the business model with all relations
        $businessModel = BusinessModel::with([
            'project',
            'valuePropositions',
            'customerSegments.segmentType',
            'customerRelationships.relationshipType',
            'channels.channelType',
            'keyActivities',
            'keyResources.resourceType',
            'keyPartnerships',
            'revenueStreams.streamType',
            'expenses'
        ])->findOrFail($id);

        // Check if user owns this model
        if ($businessModel->project->user_id !== $user->id) {
            abort(403, 'غير مصرح لك بتصدير هذا النموذج');
        }

        try {
            // Generate HTML content
            $html = view('exports.business-model-pdf', compact('businessModel'))->render();

            // Generate filename
            $filename = 'business-model-' . $businessModel->id . '-' . now()->format('Y-m-d') . '.pdf';
            $filepath = storage_path('app/temp/' . $filename);

            // Ensure temp directory exists
            if (!file_exists(storage_path('app/temp'))) {
                mkdir(storage_path('app/temp'), 0755, true);
            }

            // Generate PDF using Browsershot
            $browsershot = Browsershot::html($html);
            $browsershot->setOption('args', ['--no-sandbox', '--disable-setuid-sandbox'])
                ->showBackground()
                ->margins(10, 10, 10, 10)
                ->format('A4')
                ->landscape() // Business Model Canvas looks better in landscape
                ->save($filepath);

            // Download the file
            return response()->download($filepath, $filename)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'حدث خطأ أثناء تصدير PDF: ' . $e->getMessage()]);
        }
    }

    /**
     * Export business model as Image (PNG)
     */
    public function exportImage($id)
    {
        $user = Auth::user();

        // Get the business model with all relations
        $businessModel = BusinessModel::with([
            'project',
            'valuePropositions',
            'customerSegments.segmentType',
            'customerRelationships.relationshipType',
            'channels.channelType',
            'keyActivities',
            'keyResources.resourceType',
            'keyPartnerships',
            'revenueStreams.streamType',
            'expenses'
        ])->findOrFail($id);

        // Check if user owns this model
        if ($businessModel->project->user_id !== $user->id) {
            abort(403, 'غير مصرح لك بتصدير هذا النموذج');
        }

        try {
            // Generate HTML content
            $html = view('exports.business-model-image', compact('businessModel'))->render();

            // Generate filename
            $filename = 'business-model-' . $businessModel->id . '-' . now()->format('Y-m-d') . '.png';
            $filepath = storage_path('app/temp/' . $filename);

            // Ensure temp directory exists
            if (!file_exists(storage_path('app/temp'))) {
                mkdir(storage_path('app/temp'), 0755, true);
            }

            // Generate Image using Browsershot
            $browsershot = Browsershot::html($html);
            $browsershot->setOption('args', ['--no-sandbox', '--disable-setuid-sandbox'])
                ->showBackground()
                ->windowSize(1920, 1080)
                ->save($filepath);

            // Download the file
            return response()->download($filepath, $filename)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'حدث خطأ أثناء تصدير الصورة: ' . $e->getMessage()]);
        }
    }

    /**
     * Preview export (for testing)
     */
    public function preview($id)
    {
        $user = Auth::user();

        $businessModel = BusinessModel::with([
            'project',
            'valuePropositions',
            'customerSegments.segmentType',
            'customerRelationships.relationshipType',
            'channels.channelType',
            'keyActivities',
            'keyResources.resourceType',
            'keyPartnerships',
            'revenueStreams.streamType',
            'expenses'
        ])->findOrFail($id);

        if ($businessModel->project->user_id !== $user->id) {
            abort(403, 'غير مصرح لك بعرض هذا النموذج');
        }

        return view('exports.business-model-pdf', compact('businessModel'));
    }
}
