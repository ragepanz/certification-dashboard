<?php

namespace App\Http\Controllers;

use App\Models\JobTrainingMatrix;
use Illuminate\Http\Request;

class JobTrainingMatrixController extends Controller
{
    public function index(Request $request)
    {
        $query = JobTrainingMatrix::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('job_title', 'like', "%{$search}%")
                  ->orWhere('training_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('job_title')) {
            $query->where('job_title', $request->input('job_title'));
        }

        if ($request->filled('validity_type')) {
            $query->where('validity_type', $request->input('validity_type'));
        }

        $matrices = $query->orderBy('job_title', 'asc')
            ->orderBy('training_name', 'asc')
            ->paginate(25)
            ->withQueryString();

        $jobTitles = JobTrainingMatrix::distinct()->pluck('job_title');
        $trainingNames = JobTrainingMatrix::distinct()->pluck('training_name');

        $totalRules = JobTrainingMatrix::count();
        $twoYearCount = JobTrainingMatrix::where('validity_type', '2-Year')->where('no_need_training', false)->count();
        $foreverCount = JobTrainingMatrix::where('validity_type', 'Forever')->orWhere('no_need_training', true)->count();

        return view('settings.matrix', [
            'matrices' => $matrices,
            'jobTitles' => $jobTitles,
            'trainingNames' => $trainingNames,
            'totalRules' => $totalRules,
            'twoYearCount' => $twoYearCount,
            'foreverCount' => $foreverCount,
            'filters' => $request->all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'job_title' => 'required|string|max:255',
            'training_name' => 'required|string|max:255',
            'validity_type' => 'required|in:2-Year,Forever',
            'no_need_training' => 'nullable|boolean',
        ]);

        $validated['no_need_training'] = $request->has('no_need_training');

        JobTrainingMatrix::updateOrCreate(
            [
                'job_title' => $validated['job_title'],
                'training_name' => $validated['training_name'],
            ],
            $validated
        );

        return redirect()->route('matrix.index')->with('success', 'Aturan Training Mandatory berhasil disimpan.');
    }

    public function update(Request $request, JobTrainingMatrix $matrix)
    {
        $validated = $request->validate([
            'validity_type' => 'required|in:2-Year,Forever',
            'no_need_training' => 'nullable|boolean',
        ]);

        $matrix->update([
            'validity_type' => $validated['validity_type'],
            'no_need_training' => $request->has('no_need_training'),
        ]);

        return redirect()->route('matrix.index')->with('success', "Aturan untuk {$matrix->job_title} - {$matrix->training_name} berhasil diperbarui.");
    }

    public function destroy(JobTrainingMatrix $matrix)
    {
        $name = "{$matrix->job_title} - {$matrix->training_name}";
        $matrix->delete();

        return redirect()->route('matrix.index')->with('success', "Aturan {$name} berhasil dihapus.");
    }
}
