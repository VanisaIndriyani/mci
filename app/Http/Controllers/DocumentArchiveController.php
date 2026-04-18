<?php

namespace App\Http\Controllers;

use App\Models\DocumentArchive;
use Illuminate\Http\Request;

class DocumentArchiveController extends Controller
{
    public function index(Request $request)
    {
        $query = DocumentArchive::query()
            ->with(['documentable', 'uploader'])
            ->latest();

        if ($request->filled('kind')) {
            $query->where('kind', $request->kind);
        }

        if ($request->filled('search')) {
            $query->where('original_name', 'like', '%' . $request->search . '%');
        }

        $archives = $query->paginate(20);

        return view('archives.index', [
            'archives' => $archives,
        ]);
    }
}
