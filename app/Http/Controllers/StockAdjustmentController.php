<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesPlantOptions;
use App\Exports\StockAdjustmentsExport;
use App\Models\Category;
use App\Models\CurrentStock;
use App\Models\DailyStock;
use App\Models\Part;
use App\Models\StockAdjustment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class StockAdjustmentController extends Controller
{
    use ResolvesPlantOptions;

    /**
     * Display a listing of stock adjustments.
     */
    public function index(Request $request)
    {
        $query = StockAdjustment::with(['part.category', 'plant', 'createdBy'])->latest('created_at');

        $adjustmentNo = $request->query('adjustment_no');
        $partName = $request->query('part_name');
        $categoryId = $request->query('category_id');
        $plantId = $request->query('plant_id');
        $dateFrom = $request->query('date_from', now()->toDateString());
        $dateTo = $request->query('date_to', now()->toDateString());
        $categories = $this->selectableCategories();
        $selectableCategoryIds = $categories->pluck('id')->all();
        $plants = $this->selectablePlants();
        $defaultPlantId = $this->defaultPlantId();
        $selectablePlantIds = $plants->pluck('id')->all();

        if ($adjustmentNo !== null && $adjustmentNo !== '') {
            $query->where('adjustment_no', 'like', '%' . $adjustmentNo . '%');
        }

        if ($partName !== null && $partName !== '') {
            $query->whereHas('part', function ($builder) use ($partName) {
                $builder->where('name', 'like', '%' . $partName . '%')
                    ->orWhere('model', 'like', '%' . $partName . '%');
            });
        }

        if ($categoryId !== null && $categoryId !== '' && in_array((int) $categoryId, $selectableCategoryIds, true)) {
            $query->whereHas('part', fn ($builder) => $builder->where('category_id', $categoryId));
        }

        if ($plantId !== null && $plantId !== '' && in_array((int) $plantId, $selectablePlantIds, true)) {
            $query->where('plant_id', $plantId);
        } elseif ($defaultPlantId) {
            $query->where('plant_id', $defaultPlantId);
        }

        if ($dateFrom !== null && $dateFrom !== '') {
            $query->whereDate('adjusted_date', '>=', $dateFrom);
        }

        if ($dateTo !== null && $dateTo !== '') {
            $query->whereDate('adjusted_date', '<=', $dateTo);
        }

        $adjustments = $query->paginate(10)->withQueryString();

        return view('stock-adjustments.index', compact('adjustments', 'categories', 'plants', 'defaultPlantId', 'dateFrom', 'dateTo'));
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
        $plants = $this->selectablePlants();
        $selectablePlantIds = $plants->pluck('id')->all();
        $defaultPlantId = $this->defaultPlantId();
        $categories = Category::orderBy('name')->get();
        $parts = Part::whereIn('plant_id', $selectablePlantIds)->orderBy('name')->get();

        return view('stock-adjustments.create', compact('categories', 'parts', 'plants', 'defaultPlantId'));
    }

    /**
     * Store stock adjustments and update balances.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'adjusted_date' => 'required|date',
            'adjusted_by' => 'required|string|max:255',
            'plant_id' => ['required', $this->plantValidationRule()],
            'items' => 'required|array|min:1',
            'items.*.category_id' => [
                'nullable',
                Rule::exists('categories', 'id'),
            ],
            'items.*.part_id' => [
                'required',
                Rule::exists('parts', 'id')->where(fn ($query) => $query->where('plant_id', $request->input('plant_id'))),
            ],
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

                $price = (float) ($currentStock->last_purchase_price ?? 0);
                $amount = $price * $qty;

                StockAdjustment::create([
                    'adjustment_no' => $adjustmentNo,
                    'part_id' => $item['part_id'],
                    'plant_id' => $validated['plant_id'],
                    'symbol' => $item['symbol'],
                    'qty' => $qty,
                    'price' => $price,
                    'amount' => $amount,
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
        $prefix = 'AD-' . date('Ymd', strtotime($adjustedDate)) . '-';
        $latestAdjustmentNo = StockAdjustment::where('adjustment_no', 'like', $prefix . '%')->max('adjustment_no');
        $nextNumber = $latestAdjustmentNo ? ((int) substr($latestAdjustmentNo, -4)) + 1 : 1;

        return $prefix . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
