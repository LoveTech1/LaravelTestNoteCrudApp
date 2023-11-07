<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $numberOfPages = $request->numberOfPages ?? 10;

        //  it gives the search Query
        $search_query = $request->search_query ?? '';

        $user_id = $request->user_id ?? '';

        $sort_by = $request->sort_by ?? "";


        $notes = Note::with('user')
            ->when($search_query, function ($query) use ($search_query) {
                return $query->where('content', 'like', '%' . $search_query . '%');
            })
            ->when($user_id, function ($query) use ($user_id) {
                return $query->where('user_id', $user_id);
            })
            ->when($sort_by, function ($query) use ($sort_by) {
                return $query->orderBy('id', $sort_by);
            })
            ->paginate($numberOfPages);

        return $notes;
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
        $data = $request->validate([
            'content' => 'string',
        ]);
        $userId = Auth::id();
        $note = Note::create($data);
        return response()->json([
            'message' => 'Note successfully created',
            'note' => $note,
        ]);
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
        $note = Note::find($id);
        if ($note) {
            return response()->json([
                $note
            ]);
        } else {
            return response()->json([
                'message' => 'Note not found',
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, string $id)
    {
        $note = Note::find($id);

        if (!$note) {
            return response()->json(['message' => 'Note not found'], 404);
        }

        $note->update($request->only(['content', 'user_id']));

        return response()->json([
            'message' => 'Note successfully updated',
            'note' => $note,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $noteItem = Note::where('id', $id)->first();
        if ($noteItem) {
            $noteItem->delete();
            return response()->json([
                'message' => 'Note Item deleted Successfully',
            ]);
        }
    }

}
