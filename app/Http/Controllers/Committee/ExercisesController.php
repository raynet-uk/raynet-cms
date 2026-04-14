<?php

namespace App\Http\Controllers\Committee;

use App\Http\Controllers\Controller;
use App\Models\CommitteeExercise;
use Illuminate\Http\Request;

class ExercisesController extends Controller
{
    public function index()
    {
        $exercises = CommitteeExercise::orderByDesc('date')->paginate(20);
        $upcoming  = CommitteeExercise::where('date', '>=', now()->toDateString())
            ->orderBy('date')->first();
        $last12months = CommitteeExercise::where('date', '>=', now()->subYear())->count();

        return view('committee.exercises.index', compact('exercises', 'upcoming', 'last12months'));
    }

    public function create()
    {
        return view('committee.exercises.form', ['exercise' => new CommitteeExercise()]);
    }

    public function store(Request $request)
    {
        CommitteeExercise::create(array_merge(
            $this->validated($request),
            ['created_by' => auth()->id()]
        ));
        return redirect()->route('committee.exercises.index')->with('success', 'Exercise logged.');
    }

    public function edit(CommitteeExercise $exercise)
    {
        return view('committee.exercises.form', compact('exercise'));
    }

    public function update(Request $request, CommitteeExercise $exercise)
    {
        $exercise->update($this->validated($request));
        return redirect()->route('committee.exercises.index')->with('success', 'Exercise updated.');
    }

    public function destroy(CommitteeExercise $exercise)
    {
        $exercise->delete();
        return redirect()->route('committee.exercises.index')->with('success', 'Exercise removed.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'date'               => 'required|date',
            'activity'           => 'required|string|max:255',
            'type'               => 'required|in:training_night,tabletop,practical_exercise,real_deployment,partner_exercise,other',
            'capability_tested'  => 'nullable|string|max:255',
            'lead'               => 'nullable|string|max:120',
            'outcome'            => 'nullable|string|max:1000',
            'lessons_identified' => 'nullable|string|max:1000',
            'action_owner'       => 'nullable|string|max:120',
            'due_date'           => 'nullable|date',
            'closed_date'        => 'nullable|date',
            'notes'              => 'nullable|string|max:1000',
        ]);
    }
}
