<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesPlantOptions;
use App\Exports\PurchasesExport;
use App\Models\CurrentStock;
use App\Models\Category;
use App\Models\DailyStock;
use App\Models\Part;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class PurchaseController extends Controller
{
    use ResolvesPlantOptions;

    /**
     * Display a listing of purchases.
     */
    public function index(Request $request)
    {
        $query = Purchase::with(['part', 'plant', 'createdBy'])->latest('created_at');

        $invoice = $request->query('invoice');
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

        if ($invoice !== null && $invoice !== '') {
            $query->where('invoice', 'like', '%' . $invoice . '%');
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
            $query->whereDate('purchased_date', '>=', $dateFrom);
        }

        if ($dateTo !== null && $dateTo !== '') {
            $query->whereDate('purchased_date', '<=', $dateTo);
        }

        $purchases = $query->paginate(10)->withQueryString();

        return view('purchases.index', compact('purchases', 'categories', 'plants', 'defaultPlantId', 'dateFrom', 'dateTo'));
    }

    public function export(Request $request)
    {
        return Excel::download(new PurchasesExport($request->query()), 'purchases.xlsx');
    }

    /**
     * Show the form for creating a purchase.
     */
    public function create()
    {
        $plants = $this->selectablePlants();
        $selectablePlantIds = $plants->pluck('id')->all();
        $defaultPlantId = $this->defaultPlantId();
        $categories = Category::orderBy('name')->get();
        $parts = Part::whereIn('plant_id', $selectablePlantIds)->orderBy('name')->get();

        return view('purchases.create', compact('categories', 'parts', 'plants', 'defaultPlantId'));
    }

    /**
     * Store a purchase and update stock balances.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice' => ['required', 'string', 'max:255', Rule::unique('purchases', 'invoice')],
            'purchased_date' => 'required|date',
            'purchase_by' => 'required|nullable|string|max:255',
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
            'items.*.price' => 'required|numeric|min:0',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.remark' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $validated) {
            foreach ($validated['items'] as $item) {
                $qty = (int) $item['qty'];
                $amount = (float) $item['price'] * $qty;

                Purchase::create([
                    'invoice' => $validated['invoice'],
                    'part_id' => $item['part_id'],
                    'plant_id' => $validated['plant_id'],
                    'price' => $item['price'],
                    'qty' => $qty,
                    'amount' => $amount,
                    'remark' => $item['remark'] ?? null,
                    'purchased_date' => $validated['purchased_date'],
                    'purchase_by' => $validated['purchase_by'] ?? null,
                    'created_by' => $request->user()->id,
                ]);

                $currentStock = CurrentStock::where('item_id', $item['part_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                $currentStock->qty += $qty;
                $currentStock->last_purchase_price = $item['price'];
                $currentStock->save();

                $dailyStock = DailyStock::where('item_id', $item['part_id'])
                    ->whereDate('date', $validated['purchased_date'])
                    ->lockForUpdate()
                    ->first();

                if ($dailyStock) {
                    $dailyStock->in_qty += $qty;
                    $dailyStock->stock_qty += $qty;
                    $dailyStock->save();
                } else {
                    DailyStock::create([
                        'item_id' => $item['part_id'],
                        'date' => $validated['purchased_date'],
                        'in_qty' => $qty,
                        'out_qty' => 0,
                        'stock_qty' => $currentStock->qty,
                    ]);
                }
            }
        });

        return redirect()->route('purchases.index')->with('success', 'Purchase created and stock updated successfully.');
    }
}
