<?php

namespace App\Http\Controllers;

use App\Exports\IssuesExport;
use App\Models\Category;
use App\Models\CurrentStock;
use App\Models\DailyStock;
use App\Models\Issue;
use App\Models\Part;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class IssueController extends Controller
{
    /**
     * Display a listing of issues.
     */
    public function index(Request $request)
    {
        $query = Issue::with(['part', 'createdBy'])->latest('created_at');

        $issueNo = $request->query('issue_no');
        $partId = $request->query('part_id');
        $dateFrom = $request->query('date_from', now()->toDateString());
        $dateTo = $request->query('date_to', now()->toDateString());

        if ($issueNo !== null && $issueNo !== '') {
            $query->where('issue_no', 'like', '%' . $issueNo . '%');
        }

        if ($partId !== null && $partId !== '') {
            $query->where('part_id', $partId);
        }

        if ($dateFrom !== null && $dateFrom !== '') {
            $query->whereDate('issued_date', '>=', $dateFrom);
        }

        if ($dateTo !== null && $dateTo !== '') {
            $query->whereDate('issued_date', '<=', $dateTo);
        }

        $issues = $query->paginate(10)->withQueryString();
        $parts = Part::orderBy('name')->get();

        return view('issues.index', compact('issues', 'parts', 'dateFrom', 'dateTo'));
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
        $categories = Category::orderBy('name')->get();
        $parts = Part::orderBy('name')->get();

        return view('issues.create', compact('categories', 'parts'));
    }

    /**
     * Store issues and update stock balances.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'issued_date' => 'required|date',
            'issue_by' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.category_id' => 'nullable|exists:categories,id',
            'items.*.part_id' => 'required|exists:parts,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.remark' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $validated) {
            $issueNo = $this->generateIssueNo($validated['issued_date']);

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

                Issue::create([
                    'issue_no' => $issueNo,
                    'part_id' => $item['part_id'],
                    'qty' => $qty,
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

    private function generateIssueNo(string $issuedDate): string
    {
        $prefix = 'ISS-' . date('Ymd', strtotime($issuedDate)) . '-';
        $latestIssueNo = Issue::where('issue_no', 'like', $prefix . '%')->max('issue_no');
        $nextNumber = $latestIssueNo ? ((int) substr($latestIssueNo, -4)) + 1 : 1;

        return $prefix . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
