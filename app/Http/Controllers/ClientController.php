<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        abort_if(!$user->can('view clients'), 403, 'Unauthorized access to client directory.');

        $query = Client::with(['createdBy', 'tasks'])->withCount(['tasks', 'reminders']);

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
        /** @var User $user */
        $user = Auth::user();
        abort_if(!$user->can('create clients'), 403, 'Unauthorized to add new clients.');
        return view('clients.create');
    }

    public function checkUrl(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        abort_if(!$user->can('view clients') && !$user->can('create clients'), 403, 'Unauthorized.');

        $url = trim($request->query('url', ''));
        $ignoreId = $request->query('client_id') ? (int) $request->query('client_id') : null;

        if (!$url) {
            return response()->json(['exists' => false]);
        }

        $existing = $this->findDuplicateUrlClient($url, $ignoreId);

        if ($existing) {
            $name = $existing->company_name ?: $existing->name;
            return response()->json([
                'exists'      => true,
                'message'     => "This URL already exists (used by: {$name}).",
                'client_name' => $name,
                'client_id'   => $existing->id,
            ]);
        }

        return response()->json([
            'exists'  => false,
            'message' => 'URL is available.',
        ]);
    }

    public function store(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        abort_if(!$user->can('create clients'), 403, 'Unauthorized to add new clients.');

        $request->merge([
            'system_url'   => trim($request->system_url ?? ''),
            'company_name' => trim($request->company_name ?? ''),
        ]);

        // Duplicate URL Check (handles http/https and trailing slash variations)
        $existing = $this->findDuplicateUrlClient($request->system_url);
        if ($existing) {
            $existingName = $existing->company_name ?: $existing->name;
            $msg = "This System URL already exists (already assigned to '{$existingName}').";

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'message' => $msg,
                    'errors'  => [
                        'system_url' => [$msg],
                    ],
                ], 422);
            }

            return back()->withErrors(['system_url' => $msg])->withInput();
        }

        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'system_url'   => [
                'required',
                'string',
                'max:500',
                Rule::unique('clients', 'system_url')->whereNull('deleted_at'),
            ],
            'name'         => 'nullable|string|max:255',
            'phone'        => 'nullable|string|max:50',
            'email'        => 'nullable|email|max:255',
            'address'      => 'nullable|string',
            'type'         => 'nullable|in:pos,website,both,other',
            'notes'        => 'nullable|string',
        ], [
            'company_name.required' => 'The Company / Business name field is required.',
            'system_url.required'   => 'The System URL field is required.',
            'system_url.unique'     => 'This System URL already exists. (Already exist)',
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
        /** @var User $user */
        $user = Auth::user();
        abort_if(!$user->can('view clients'), 403, 'Unauthorized access to client profile.');

        $client->load(['tasks.assignedTo', 'tasks.createdBy', 'reminders']);
        return view('clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        /** @var User $user */
        $user = Auth::user();
        abort_if(!$user->can('edit clients'), 403, 'Unauthorized to edit client information.');
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        /** @var User $user */
        $user = Auth::user();
        abort_if(!$user->can('edit clients'), 403, 'Unauthorized to edit client information.');

        $request->merge([
            'system_url'   => trim($request->system_url ?? ''),
            'company_name' => trim($request->company_name ?? ''),
        ]);

        // Duplicate URL Check
        $existing = $this->findDuplicateUrlClient($request->system_url, $client->id);
        if ($existing) {
            $existingName = $existing->company_name ?: $existing->name;
            $msg = "This System URL already exists (already assigned to '{$existingName}').";

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'message' => $msg,
                    'errors'  => [
                        'system_url' => [$msg],
                    ],
                ], 422);
            }

            return back()->withErrors(['system_url' => $msg])->withInput();
        }

        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'system_url'   => [
                'required',
                'string',
                'max:500',
                Rule::unique('clients', 'system_url')->ignore($client->id)->whereNull('deleted_at'),
            ],
            'name'         => 'nullable|string|max:255',
            'phone'        => 'nullable|string|max:50',
            'email'        => 'nullable|email|max:255',
            'address'      => 'nullable|string',
            'type'         => 'nullable|in:pos,website,both,other',
            'notes'        => 'nullable|string',
            'is_active'    => 'nullable|boolean',
        ], [
            'company_name.required' => 'The Company / Business name field is required.',
            'system_url.required'   => 'The System URL field is required.',
            'system_url.unique'     => 'This System URL already exists. (Already exist)',
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

    /**
     * Find duplicate client by exact or normalized URL variation.
     */
    private function findDuplicateUrlClient(string $url, ?int $ignoreId = null): ?Client
    {
        $url = trim($url);
        if (!$url) {
            return null;
        }

        $cleanUrl = preg_replace('~^https?://~i', '', rtrim($url, '/'));

        $query = Client::where(function ($q) use ($url, $cleanUrl) {
            $q->where('system_url', $url);
            if ($cleanUrl) {
                $q->orWhere('system_url', 'http://' . $cleanUrl)
                  ->orWhere('system_url', 'https://' . $cleanUrl)
                  ->orWhere('system_url', $cleanUrl)
                  ->orWhere('system_url', 'http://' . $cleanUrl . '/')
                  ->orWhere('system_url', 'https://' . $cleanUrl . '/');
            }
        });

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        return $query->first();
    }

    public function destroy(Client $client)
    {
        /** @var User $user */
        $user = Auth::user();
        abort_if(!$user->can('delete clients'), 403, 'Unauthorized to delete client.');

        $taskCount = $client->tasks()->withTrashed()->count();
        $reminderCount = $client->reminders()->count();

        if ($taskCount > 0 || $reminderCount > 0) {
            $details = [];
            if ($taskCount > 0) {
                $details[] = "{$taskCount} task(s)";
            }
            if ($reminderCount > 0) {
                $details[] = "{$reminderCount} reminder(s)";
            }
            $historyStr = implode(' and ', $details);

            return redirect()->route('clients.index')
                ->with('error', "Cannot delete client '{$client->company_name}'. This client has {$historyStr} associated with it in history.");
        }

        $client->delete();
        return redirect()->route('clients.index')->with('success', 'Client deleted successfully.');
    }
}
