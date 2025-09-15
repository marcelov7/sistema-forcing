<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UnitController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('super.admin');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $units = Unit::withCount(['users', 'forcings'])->paginate(10);
        return view('admin.units.index', compact('units'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.units.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:units',
            'name' => 'required|string|max:100',
            'company' => 'required|string|max:100',
            'description' => 'nullable|string',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:50',
            'state' => 'nullable|string|max:2',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'active' => 'boolean',
        ]);

        $validated['active'] = $request->has('active');

        Unit::create($validated);

        return redirect()->route('admin.units.index')
            ->with('success', 'Unidade criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Unit $unit)
    {
        $unit->load(['users' => function($query) {
            $query->orderBy('name');
        }, 'forcings' => function($query) {
            $query->latest()->limit(10);
        }]);

        return view('admin.units.show', compact('unit'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Unit $unit)
    {
        return view('admin.units.edit', compact('unit'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Unit $unit)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:units,code,' . $unit->id,
            'name' => 'required|string|max:100',
            'company' => 'required|string|max:100',
            'description' => 'nullable|string',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:50',
            'state' => 'nullable|string|max:2',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'active' => 'boolean',
        ]);

        $validated['active'] = $request->has('active');

        $unit->update($validated);

        return redirect()->route('admin.units.index')
            ->with('success', 'Unidade atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Unit $unit)
    {
        // Verificar se a unidade tem usuários ou forcings
        if ($unit->users()->count() > 0) {
            return redirect()->route('admin.units.index')
                ->with('error', 'Não é possível excluir uma unidade que possui usuários associados.');
        }

        if ($unit->forcings()->count() > 0) {
            return redirect()->route('admin.units.index')
                ->with('error', 'Não é possível excluir uma unidade que possui forcings associados.');
        }

        $unit->delete();

        return redirect()->route('admin.units.index')
            ->with('success', 'Unidade excluída com sucesso!');
    }

    /**
     * Show users of a specific unit
     */
    public function users(Unit $unit)
    {
        $users = $unit->users()->orderBy('name')->get();
        return view('admin.units.users', compact('unit', 'users'));
    }

    /**
     * Show forcings of a specific unit
     */
    public function forcings(Unit $unit)
    {
        $forcings = $unit->forcings()
            ->with(['executante', 'liberador'])
            ->latest()
            ->get();
        return view('admin.units.forcings', compact('unit', 'forcings'));
    }
}
