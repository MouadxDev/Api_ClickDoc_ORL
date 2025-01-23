<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use Illuminate\Http\Request;

class StockController extends Controller
{
    /**
     * Display a listing of the Stocks with pagination.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $stocks = Stock::paginate(25); // Adjust the per-page limit if necessary

        return response()->json([
            'current_page' => $stocks->currentPage(),
            'data' => $stocks->items(),
            'first_page_url' => $stocks->url(1),
            'from' => $stocks->firstItem(),
            'last_page' => $stocks->lastPage(),
            'last_page_url' => $stocks->url($stocks->lastPage()),
            'links' => $stocks->linkCollection(),
            'next_page_url' => $stocks->nextPageUrl(),
            'path' => $stocks->path(),
            'per_page' => $stocks->perPage(),
            'prev_page_url' => $stocks->previousPageUrl(),
            'to' => $stocks->lastItem(),
            'total' => $stocks->total(),
        ]);
    }

    /**
     * Store a newly created Stock in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'stock' => 'required|integer',
            'expiration_date' => 'required|date',
        ]);

        $stock = Stock::create($validated);

        return response()->json($stock, 201);
    }

    /**
     * Display the specified Stock.
     *
     * @param  \App\Models\Stock  $stock
     * @return \Illuminate\Http\Response
     */
    public function show(Stock $stock)
    {
        return response()->json($stock);
    }

    /**
     * Update the specified Stock in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Stock  $stock
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Stock $stock)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'stock' => 'sometimes|required|integer',
            'expiration_date' => 'sometimes|required|date',
        ]);

        $stock->update($validated);

        return response()->json($stock);
    }

    /**
     * Remove the specified Stock from storage.
     *
     * @param  \App\Models\Stock  $stock
     * @return \Illuminate\Http\Response
     */
    public function destroy(Stock $stock)
    {
        $stock->delete();

        return response()->json(null, 204);
    }
}
