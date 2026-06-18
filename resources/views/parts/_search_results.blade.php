<div>
    <form id="partsSearchForm" class="mb-2 d-flex" onsubmit="return false;">
        <input type="search" name="q" placeholder="Search parts by name or model" value="{{ request()->query('q') }}" class="form-control me-2">
        <button class="btn btn-primary" id="partsSearchBtn">Search</button>
    </form>

    <div class="table-responsive">
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>Part Name</th>
                    <th>Model</th>
                    <th>Brand</th>
                    <th>Category</th>
                    <th>Unit</th>
                    <th style="width:120px">Quantity</th>
                </tr>
            </thead>
            <tbody>
                @foreach($parts as $part)
                    <tr>
                        <td>{{ $part->name }}</td>
                        <td>{{ $part->model }}</td>
                        <td>{{ $part->brand }}</td>
                        <td>{{ $part->category?->name }}</td>
                        <td>{{ $part->unit?->name }}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <input id="part-qty-{{ $part->id }}" type="number" min="1" value="1" class="form-control form-control-sm text-center">
                                <button data-id="{{ $part->id }}" data-name="{{ $part->name }}" data-model="{{ $part->model }}" data-brand="{{ $part->brand }}" data-category="{{ $part->category?->name }}" data-unit="{{ $part->unit?->name }}" class="btn btn-sm btn-outline-primary add-part-btn">Add</button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-2">
        {!! $parts->links('pagination::bootstrap-5') !!}
    </div>
</div>
