<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('viewAny', Product::class);

        $products = Product::with('user')->get();

        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('create', Product::class);

        return view('products.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('create', Product::class);

        // Validation basique
        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
        ]);

        // Création du produit pour l'utilisateur connecté
        Product::create([
            'name'     => $validated['name'],
            'price'    => $validated['price'],
            'user_id'  => $request->user()->id,
            'is_public'=> $request->has('is_public'),
        ]);

        return redirect()
            ->route('products.index')
            ->with('status', 'Produit créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        Gate::authorize('view', $product);

        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        Gate::authorize('update', $product);

        return view('products.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,  Product $product)
    {
        Gate::authorize('update', $product);

        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
        ]);

        $product->update([
            'name'      => $validated['name'],
            'price'     => $validated['price'],
            'is_public' => $request->has('is_public'),
        ]);

        return redirect()
            ->route('products.index')
            ->with('status', 'Produit mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        Gate::authorize('delete', $product);
        
        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('status', 'Produit supprimé avec succès.');
    }
}
