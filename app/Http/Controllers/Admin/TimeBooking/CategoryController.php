<?php

namespace Weboldalnet\TimeBooking\Http\Controllers\Admin\TimeBooking;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Weboldalnet\TimeBooking\Models\Category;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index(): View
    {
        $categories = Category::withCount('appointments')->ordered()->paginate(20);
        return view('timebooking::admin.timebooking.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create(): View
    {
        return view('timebooking::admin.timebooking.categories.create');
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:timebooking_categories,name',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        Category::create($validated);

        return redirect()->route('admin.timebooking.categories.index')
            ->with('success', 'Kategória sikeresen létrehozva.');
    }

    /**
     * Display the specified category.
     */
    public function show(Category $category): View
    {
        $category->load(['appointments' => function ($query) {
            $query->with('bookings')->ordered();
        }]);
        
        return view('timebooking::admin.timebooking.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $category): View
    {
        return view('timebooking::admin.timebooking.categories.edit', compact('category'));
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:timebooking_categories,name,' . $category->id,
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $category->update($validated);

        return redirect()->route('admin.timebooking.categories.index')
            ->with('success', 'Kategória sikeresen frissítve.');
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Category $category): RedirectResponse
    {
        // Check if category has appointments
        if ($category->appointments()->count() > 0) {
            return redirect()->route('admin.timebooking.categories.index')
                ->with('error', 'Nem törölhető olyan kategória, amelyhez időpontok tartoznak.');
        }

        $category->delete();

        return redirect()->route('admin.timebooking.categories.index')
            ->with('success', 'Kategória sikeresen törölve.');
    }

    /**
     * Toggle the active status of the category.
     */
    public function toggleStatus(Category $category): RedirectResponse
    {
        $category->update(['is_active' => !$category->is_active]);

        $status = $category->is_active ? 'aktiválva' : 'deaktiválva';
        
        return redirect()->route('admin.timebooking.categories.index')
            ->with('success', "Kategória sikeresen {$status}.");
    }

    /**
     * Update the sort order of categories.
     */
    public function updateOrder(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'categories' => 'required|array',
            'categories.*.id' => 'required|exists:timebooking_categories,id',
            'categories.*.sort_order' => 'required|integer|min:0',
        ]);

        foreach ($validated['categories'] as $categoryData) {
            Category::where('id', $categoryData['id'])
                ->update(['sort_order' => $categoryData['sort_order']]);
        }

        return redirect()->route('admin.timebooking.categories.index')
            ->with('success', 'Kategória sorrend sikeresen frissítve.');
    }
}