<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        abort_if(!Auth::user()->can('view clients'), 403, 'Unauthorized access to client directory.');

        $query = Client::with(['createdBy', 'tasks']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('system_url', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        if ($request->filled('type'))   $query->where('type', $request->type);
        if ($request->filled('status')) $query->where('is_active', $request->status === 'active');

        $clients = $query->latest()->paginate(15)->withQueryString();
        $totalCount   = Client::count();
        $activeCount  = Client::where('is_active', true)->count();
        $posCount     = Client::where('type', 'pos')->count();
        $websiteCount = Client::where('type', 'website')->count();
        $bothCount    = Client::where('type', 'both')->count();

        return view('clients.index', compact('clients', 'totalCount', 'activeCount', 'posCount', 'websiteCount', 'bothCount'));
    }

    public function create()
    {
        abort_if(!Auth::user()->can('create clients'), 403, 'Unauthorized to add new clients.');
        return view('clients.create');
    }

    public function store(Request $request)
    {
        abort_if(!Auth::user()->can('create clients'), 403, 'Unauthorized to add new clients.');

        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'system_url'   => 'required|string|max:500',
            'name'         => 'nullable|string|max:255',
            'phone'        => 'nullable|string|max:50',
            'email'        => 'nullable|email|max:255',
            'address'      => 'nullable|string',
            'type'         => 'nullable|in:pos,website,both,other',
            'notes'        => 'nullable|string',
        ]);
        if (empty($validated['name'])) {
            $validated['name'] = $validated['company_name'];
        }
        if (empty($validated['type'])) {
            $validated['type'] = 'pos';
        }
        $validated['created_by'] = Auth::id();
        $client = Client::create($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Client created successfully!',
                'client'  => [
                    'id'           => $client->id,
                    'name'         => $client->name,
                    'company_name' => $client->company_name,
                    'display_name' => $client->company_name ? ($client->company_name . ($client->name && $client->name !== $client->company_name ? ' (' . $client->name . ')' : '')) : $client->name,
                    'system_url'   => $client->system_url,
                    'type'         => $client->type,
                    'type_label'   => $client->type_label,
                ],
            ]);
        }

        return redirect()->route('clients.show', $client)->with('success', 'Client added successfully!');
    }

    public function show(Client $client)
    {
        abort_if(!Auth::user()->can('view clients'), 403, 'Unauthorized access to client profile.');

        $client->load(['tasks.assignedTo', 'tasks.createdBy', 'reminders']);
        return view('clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        abort_if(!Auth::user()->can('edit clients'), 403, 'Unauthorized to edit client information.');
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        abort_if(!Auth::user()->can('edit clients'), 403, 'Unauthorized to edit client information.');

        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'system_url'   => 'required|string|max:500',
            'name'         => 'nullable|string|max:255',
            'phone'        => 'nullable|string|max:50',
            'email'        => 'nullable|email|max:255',
            'address'      => 'nullable|string',
            'type'         => 'nullable|in:pos,website,both,other',
            'notes'        => 'nullable|string',
            'is_active'    => 'nullable|boolean',
        ]);
        if (empty($validated['name'])) {
            $validated['name'] = $validated['company_name'];
        }
        if (empty($validated['type'])) {
            $validated['type'] = 'pos';
        }
        $validated['is_active'] = $request->has('is_active') ? (bool)$request->is_active : true;
        $client->update($validated);
        return redirect()->route('clients.show', $client)->with('success', 'Client updated!');
    }

    public function destroy(Client $client)
    {
        abort_if(!Auth::user()->can('delete clients'), 403, 'Unauthorized to delete client.');

        $client->delete();
        return redirect()->route('clients.index')->with('success', 'Client deleted.');
    }
}
