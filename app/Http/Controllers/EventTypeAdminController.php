<?php

namespace App\Http\Controllers;

use App\Models\EventType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EventTypeAdminController extends Controller
{
    /**
     * List all event types and optionally load one for editing.
     */
    public function index(Request $request)
    {
        $types = EventType::orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $editingType = null;
        if ($request->filled('edit')) {
            $editingType = EventType::find($request->integer('edit'));
        }

        return view('admin.event-types.index', compact('types', 'editingType'));
    }

    /**
     * Store a new event type.
     */
    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        EventType::create($data);

        return redirect()
            ->route('admin.event-types')
            ->with('status', 'Event type created.');
    }

    /**
     * Update an existing event type.
     */
    public function update(Request $request, int $id)
    {
        $type = EventType::findOrFail($id);

        $data = $this->validatedData($request, $type);

        $type->update($data);

        return redirect()
            ->route('admin.event-types')
            ->with('status', 'Event type updated.');
    }

    /**
     * Delete an event type (if unused).
     */
    public function delete(int $id)
    {
        $type = EventType::findOrFail($id);

        if ($type->events()->exists()) {
            return redirect()
                ->route('admin.event-types')
                ->with('status', 'Cannot delete – there are events using this type.');
        }

        $type->delete();

        return redirect()
            ->route('admin.event-types')
            ->with('status', 'Event type deleted.');
    }

    /**
     * Shared validation + colour normalisation.
     */
    protected function validatedData(Request $request, ?EventType $existing = null): array
    {
        $data = $request->validate([
            'name'       => ['required', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'colour'     => ['nullable', 'string', 'max:7'], // "#RRGGBB"
        ], [], [
            'name'       => 'name',
            'sort_order' => 'sort order',
            'colour'     => 'badge colour',
        ]);

        // Sort order default
        $data['sort_order'] = $data['sort_order'] ?? 0;

        // Always regenerate slug from name
        $data['slug'] = Str::slug($data['name']);

        // Normalise colour to "#RRGGBB" or null
        $colour = $data['colour'] ?? null;
        if ($colour !== null && $colour !== '') {
            $colour = ltrim($colour, '#');
            $colour = substr($colour, 0, 6);
            $data['colour'] = '#' . $colour;
        } else {
            $data['colour'] = null;
        }

        return $data;
    }
}