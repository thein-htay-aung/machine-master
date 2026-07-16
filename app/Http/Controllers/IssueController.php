<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesPlantOptions;
use App\Exports\IssuesExport;
use App\Models\Category;
use App\Models\CurrentStock;
use App\Models\DailyStock;
use App\Models\Issue;
use App\Models\Part;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class IssueController extends Controller
{
    use ResolvesPlantOptions;

    /**
     * Display a listing of issues.
     */
    public function index(Request $request)
    {
        $query = Issue::with(['part.category', 'plant', 'createdBy'])->latest('created_at');

        $issueNo = $request->query('issue_no');
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

        if ($issueNo !== null && $issueNo !== '') {
            $query->where('issue_no', 'like', '%' . $issueNo . '%');
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
            $query->whereDate('issued_date', '>=', $dateFrom);
        }

        if ($dateTo !== null && $dateTo !== '') {
            $query->whereDate('issued_date', '<=', $dateTo);
        }

        $issues = $query->paginate(10)->withQueryString();

        return view('issues.index', compact('issues', 'categories', 'plants', 'defaultPlantId', 'dateFrom', 'dateTo'));
    }

    public function export(Request $request)
    {
        return Excel::download(new IssuesExport($request->query()), 'issues.xlsx');
    }

    /**
     * Show the form for creating issues.
     */
    public function create()
    {
        $plants = $this->selectablePlants();
        $selectablePlantIds = $plants->pluck('id')->all();
        $defaultPlantId = $this->defaultPlantId();
        $categories = Category::orderBy('name')->get();
        $parts = Part::whereIn('plant_id', $selectablePlantIds)->orderBy('name')->get();

        return view('issues.create', compact('categories', 'parts', 'plants', 'defaultPlantId'));
    }

    /**
     * Store issues and update stock balances.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'issued_date' => 'required|date',
            'issue_by' => 'required|string|max:255',
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
            'items.*.qty' => 'required|integer|min:1',
            'items.*.remark' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $validated) {
            $issueNo = $this->generateIssueNo($validated['plant_id'], $validated['issued_date']);

            foreach ($validated['items'] as $item) {
                $qty = (int) $item['qty'];

                $currentStock = CurrentStock::with('item')
                    ->where('item_id', $item['part_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($currentStock->qty < $qty) {
                    throw ValidationException::withMessages([
                        'items' => ($currentStock->item?->name ?? 'Selected part') . ' has only ' . $currentStock->qty . ' in stock.',
                    ]);
                }

                $price = (float) ($currentStock->last_purchase_price ?? 0);
                $amount = $price * $qty;

                Issue::create([
                    'issue_no' => $issueNo,
                    'part_id' => $item['part_id'],
                    'plant_id' => $validated['plant_id'],
                    'qty' => $qty,
                    'price' => $price,
                    'amount' => $amount,
                    'remark' => $item['remark'] ?? null,
                    'issued_date' => $validated['issued_date'],
                    'issue_by' => $validated['issue_by'],
                    'created_by' => $request->user()->id,
                ]);

                $currentStock->qty -= $qty;
                $currentStock->save();

                $dailyStock = DailyStock::where('item_id', $item['part_id'])
                    ->whereDate('date', $validated['issued_date'])
                    ->lockForUpdate()
                    ->first();

                if ($dailyStock) {
                    $dailyStock->out_qty += $qty;
                    $dailyStock->stock_qty -= $qty;
                    $dailyStock->save();
                } else {
                    DailyStock::create([
                        'item_id' => $item['part_id'],
                        'date' => $validated['issued_date'],
                        'in_qty' => 0,
                        'out_qty' => $qty,
                        'stock_qty' => $currentStock->qty,
                    ]);
                }
            }
        });

        return redirect()->route('issues.index')->with('success', 'Issue created and stock updated successfully.');
    }

    private function generateIssueNo(int|string $plantId, string $issuedDate): string
    {
        $prefix = $plantId . '-IS-' . date('Ymd', strtotime($issuedDate)) . '-';
        $latestIssueNo = Issue::where('issue_no', 'like', $prefix . '%')->max('issue_no');
        $nextNumber = $latestIssueNo ? ((int) substr($latestIssueNo, -4)) + 1 : 1;

        return $prefix . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
