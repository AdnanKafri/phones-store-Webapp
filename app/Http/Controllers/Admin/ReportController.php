<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Report::with(['reporter', 'reportable']);
        
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }
        
        $reports = $query->latest()->paginate(15);
        
        return view('admin.reports.index', compact('reports'));
    }

    public function show(Report $report)
    {
        $report->load(['reporter', 'reportable']);
        return view('admin.reports.show', compact('report'));
    }

    public function update(Request $request, Report $report)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,reviewed,resolved',
        ]);
        
        $report->update($validated);
        
        return back()->with('success', 'Report status updated successfully.');
    }

    public function destroy(Report $report)
    {
        $report->delete();
        
        return redirect()->route('admin.reports.index')
            ->with('success', 'Report deleted successfully.');
    }
}
