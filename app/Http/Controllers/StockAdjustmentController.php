<?php

namespace App\Http\Controllers;

use App\Exports\StockAdjustmentsExport;
use App\Models\Category;
use App\Models\CurrentStock;
use App\Models\DailyStock;
use App\Models\Part;
use App\Models\StockAdjustment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class StockAdjustmentController extends Controller
{
    /**
     * Display a listing of stock adjustments.
     */
    public function index(Request $request)
    {
        $query = StockAdjustment::with(['part', 'createdBy'])->latest('created_at');

        $adjustmentNo = $request->query('adjustment_no');
        $partId = $request->query('part_id');
        $dateFrom = $request->query('date_from', now()->toDateString());
        $dateTo = $request->query('date_to', now()->toDateString());

        if ($adjustmentNo !== null && $adjustmentNo !== '') {
            $query->where('adjustment_no', 'like', '%' . $adjustmentNo . '%');
        }

        if ($partId !== null && $partId !== '') {
            $query->where('part_id', $partId);
        }

        if ($dateFrom !== null && $dateFrom !== '') {
            $query->whereDate('adjusted_date', '>=', $dateFrom);
        }

        if ($dateTo !== null && $dateTo !== '') {
            $query->whereDate('adjusted_date', '<=', $dateTo);
        }

        $adjustments = $query->paginate(10)->withQueryString();
        $parts = Part::orderBy('name')->get();

        return view('stock-adjustments.index', compact('adjustments', 'parts', 'dateFrom', 'dateTo'));
    }

    public function export(Request $request)
    {
        return Excel::download(new StockAdjustmentsExport($request->query()), 'stock-adjustments.xlsx');
    }

    /**
     * Show the form for creating stock adjustments.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $parts = Part::orderBy('name')->get();

        return view('stock-adjustments.create', compact('categories', 'parts'));
    }

    /**
     * Store stock adjustments and update balances.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'adjusted_date' => 'required|date',
            'adjusted_by' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.category_id' => 'nullable|exists:categories,id',
            'items.*.part_id' => 'required|exists:parts,id',
            'items.*.symbol' => 'required|in:+,-',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.reason' => 'required|string|max:1000',
        ]);

        DB::transaction(function () use ($request, $validated) {
            $adjustmentNo = $this->generateAdjustmentNo($validated['adjusted_date']);

            foreach ($validated['items'] as $item) {
                $qty = (int) $item['qty'];

                $currentStock = CurrentStock::with('item')
                    ->where('item_id', $item['part_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($item['symbol'] === '-' && $currentStock->qty < $qty) {
                    throw ValidationException::withMessages([
                        'items' => ($currentStock->item?->name ?? 'Selected part') . ' has only ' . $currentStock->qty . ' in stock.',
                    ]);
                }

                StockAdjustment::create([
                    'adjustment_no' => $adjustmentNo,
                    'part_id' => $item['part_id'],
                    'symbol' => $item['symbol'],
                    'qty' => $qty,
                    'reason' => $item['reason'],
                    'adjusted_date' => $validated['adjusted_date'],
                    'adjusted_by' => $validated['adjusted_by'],
                    'created_by' => $request->user()->id,
                ]);

                if ($item['symbol'] === '+') {
                    $currentStock->qty += $qty;
                } else {
                    $currentStock->qty -= $qty;
                }

                $currentStock->save();

                $dailyStock = DailyStock::where('item_id', $item['part_id'])
                    ->whereDate('date', $validated['adjusted_date'])
                    ->lockForUpdate()
                    ->first();

                if ($dailyStock) {
                    if ($item['symbol'] === '+') {
                        $dailyStock->in_qty += $qty;
                        $dailyStock->stock_qty += $qty;
                    } else {
                        $dailyStock->out_qty += $qty;
                        $dailyStock->stock_qty -= $qty;
                    }

                    $dailyStock->save();
                } else {
                    DailyStock::create([
                        'item_id' => $item['part_id'],
                        'date' => $validated['adjusted_date'],
                        'in_qty' => $item['symbol'] === '+' ? $qty : 0,
                        'out_qty' => $item['symbol'] === '-' ? $qty : 0,
                        'stock_qty' => $currentStock->qty,
                    ]);
                }
            }
        });

        return redirect()->route('stock-adjustments.index')->with('success', 'Stock adjustment created successfully.');
    }

    private function generateAdjustmentNo(string $adjustedDate): string
    {
        $prefix = 'ADJ-' . date('Ymd', strtotime($adjustedDate)) . '-';
        $latestAdjustmentNo = StockAdjustment::where('adjustment_no', 'like', $prefix . '%')->max('adjustment_no');
        $nextNumber = $latestAdjustmentNo ? ((int) substr($latestAdjustmentNo, -4)) + 1 : 1;

        return $prefix . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
