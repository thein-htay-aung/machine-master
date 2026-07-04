@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Add Issue</h5>
                <a href="{{ route('issues.index', request()->query()) }}" class="btn btn-sm btn-light">Back to List</a>
            </div>

            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('issues.store') }}" method="POST">
                    @csrf

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Issued Date</label>
                            <input type="date" name="issued_date" value="{{ old('issued_date', now()->format('Y-m-d')) }}" class="form-control @error('issued_date') is-invalid @enderror">
                            @error('issued_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Issue By</label>
                            <input type="text" name="issue_by" value="{{ old('issue_by') }}" class="form-control @error('issue_by') is-invalid @enderror" required>
                            @error('issue_by')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Plant</label>
                            <select name="plant_id" class="form-select @error('plant_id') is-invalid @enderror" id="plantSelect">
                                @if($plants->count() > 1)
                                    <option value="">Select Plant</option>
                                @endif
                                @foreach ($plants as $plant)
                                    <option value="{{ $plant->id }}" {{ old('plant_id', $defaultPlantId) == $plant->id ? 'selected' : '' }}>{{ $plant->name }}</option>
                                @endforeach
                            </select>
                            @error('plant_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">Issue Items</h6>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="addItemRow">+ Add Part</button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-success">
                                <tr>
                                    <th style="min-width: 190px;">Category</th>
                                    <th style="min-width: 240px;">Part Name</th>
                                    <th style="min-width: 150px;">Brand</th>
                                    <th style="min-width: 150px;">Model</th>
                                    <th style="width: 120px;" class="text-end">Qty</th>
                                    <th style="min-width: 220px;">Remark</th>
                                    <th style="width: 90px;" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="issueItems">
                                @php
                                    $oldItems = old('items', [['category_id' => '', 'part_id' => '', 'qty' => '', 'remark' => '']]);
                                @endphp

                                @foreach ($oldItems as $index => $item)
                                    <tr class="issue-item-row">
                                        <td>
                                            <select name="items[{{ $index }}][category_id]" class="form-select item-category @error('items.' . $index . '.category_id') is-invalid @enderror">
                                                <option value="">All categories</option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}" {{ ($item['category_id'] ?? '') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('items.' . $index . '.category_id')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <select name="items[{{ $index }}][part_id]" class="form-select item-part @error('items.' . $index . '.part_id') is-invalid @enderror">
                                                <option value="">Select part</option>
                                                @foreach ($parts as $part)
                                                    <option value="{{ $part->id }}" data-plant-id="{{ $part->plant_id }}" data-category-id="{{ $part->category_id }}" data-brand="{{ $part->brand ?? '-' }}" data-model="{{ $part->model ?? '-' }}" {{ ($item['part_id'] ?? '') == $part->id ? 'selected' : '' }}>{{ $part->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('items.' . $index . '.part_id')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="text" class="form-control item-brand" value="-" readonly>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control item-model" value="-" readonly>
                                        </td>
                                        <td>
                                            <input type="number" min="1" name="items[{{ $index }}][qty]" value="{{ $item['qty'] ?? '' }}" class="form-control text-end @error('items.' . $index . '.qty') is-invalid @enderror">
                                            @error('items.' . $index . '.qty')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="text" name="items[{{ $index }}][remark]" value="{{ $item['remark'] ?? '' }}" class="form-control @error('items.' . $index . '.remark') is-invalid @enderror">
                                            @error('items.' . $index . '.remark')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm p-0 border-0 bg-transparent text-danger remove-item-row" title="Delete"><i class="bi bi-trash fs-5"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Save Issue</button>
                        <a href="{{ route('issues.index', request()->query()) }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <template id="issueItemTemplate">
        <tr class="issue-item-row">
            <td>
                <select name="items[__INDEX__][category_id]" class="form-select item-category">
                    <option value="">All categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <select name="items[__INDEX__][part_id]" class="form-select item-part">
                    <option value="">Select part</option>
                    @foreach ($parts as $part)
                        <option value="{{ $part->id }}" data-plant-id="{{ $part->plant_id }}" data-category-id="{{ $part->category_id }}" data-brand="{{ $part->brand ?? '-' }}" data-model="{{ $part->model ?? '-' }}">{{ $part->name }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="text" class="form-control item-brand" value="-" readonly>
            </td>
            <td>
                <input type="text" class="form-control item-model" value="-" readonly>
            </td>
            <td>
                <input type="number" min="1" name="items[__INDEX__][qty]" class="form-control text-end">
            </td>
            <td>
                <input type="text" name="items[__INDEX__][remark]" class="form-control">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm p-0 border-0 bg-transparent text-danger remove-item-row" title="Delete"><i class="bi bi-trash fs-5"></i></button>
            </td>
        </tr>
    </template>
@endsection

@push('scripts')
    <script>
        const itemsBody = document.getElementById('issueItems');
        const addItemRowButton = document.getElementById('addItemRow');
        const itemTemplate = document.getElementById('issueItemTemplate').innerHTML;
        const plantSelect = document.getElementById('plantSelect');
        let nextItemIndex = {{ count($oldItems) }};

        function filterCategoryOptions(row) {
            const categorySelect = row.querySelector('.item-category');
            categorySelect.disabled = false;
        }

        function filterPartOptions(row) {
            const plantId = plantSelect?.value || '';
            const categoryId = row.querySelector('.item-category')?.value || '';
            const partSelect = row.querySelector('.item-part');
            const selectedOption = partSelect.options[partSelect.selectedIndex];

            Array.from(partSelect.options).forEach((option) => {
                if (!option.value) {
                    option.hidden = false;
                    return;
                }

                option.hidden = plantId === ''
                    || option.dataset.plantId !== plantId
                    || (categoryId !== '' && option.dataset.categoryId !== categoryId);
            });

            if (selectedOption && selectedOption.value && selectedOption.hidden) {
                partSelect.value = '';
            }

            partSelect.disabled = plantId === '';
        }

        function filterItemRow(row) {
            filterCategoryOptions(row);
            filterPartOptions(row);
            updatePartDetails(row);
        }

        function filterAllRows() {
            itemsBody.querySelectorAll('.issue-item-row').forEach((row) => {
                filterItemRow(row);
            });
        }

        function updatePartDetails(row) {
            const partSelect = row.querySelector('.item-part');
            const selectedOption = partSelect.options[partSelect.selectedIndex];

            row.querySelector('.item-brand').value = selectedOption?.dataset.brand || '-';
            row.querySelector('.item-model').value = selectedOption?.dataset.model || '-';
        }

        function refreshRemoveButtons() {
            const rows = itemsBody.querySelectorAll('.issue-item-row');

            rows.forEach((row) => {
                row.querySelector('.remove-item-row').disabled = rows.length === 1;
            });
        }

        addItemRowButton.addEventListener('click', () => {
            itemsBody.insertAdjacentHTML('beforeend', itemTemplate.replaceAll('__INDEX__', nextItemIndex));
            nextItemIndex += 1;

            const newRow = itemsBody.querySelector('.issue-item-row:last-child');
            filterItemRow(newRow);
            refreshRemoveButtons();
        });

        itemsBody.addEventListener('change', (event) => {
            const row = event.target.closest('.issue-item-row');

            if (event.target.classList.contains('item-category')) {
                filterPartOptions(row);
                updatePartDetails(row);
            }

            if (event.target.classList.contains('item-part')) {
                updatePartDetails(row);
            }
        });

        plantSelect?.addEventListener('change', filterAllRows);

        itemsBody.addEventListener('click', (event) => {
            const removeButton = event.target.closest('.remove-item-row');

            if (removeButton) {
                removeButton.closest('.issue-item-row').remove();
                refreshRemoveButtons();
            }
        });

        itemsBody.querySelectorAll('.issue-item-row').forEach((row) => {
            filterItemRow(row);
        });
        refreshRemoveButtons();
    </script>
@endpush
