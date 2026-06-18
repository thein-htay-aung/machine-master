@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Edit Bill of Materials for {{ $machine->name }} | {{ $machine->control_no }}</h5>
                <div class="d-flex gap-2">
                    <button id="openPartsModalBtn" type="button" class="btn btn-sm btn-light">Add Parts</button>
                    <button id="openCopyModalBtn" type="button" class="btn btn-sm btn-light"
                        @if($machine->parts->isEmpty()) disabled aria-disabled="true" class="btn btn-sm btn-light disabled" @endif>
                        Copy To
                    </button>
                    <a href="{{ route('machines.show', $machine->id) }}" class="btn btn-sm btn-light">Back to Machine</a>
                </div>
            </div>

            <div class="card-body">
                @if (session('info'))
                    <div class="alert alert-success">
                        {{ session('info') }}
                    </div>
                @endif
                <form action="{{ route('machines.parts.update', $machine->id) }}" method="POST">
                    @csrf

                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead>
                                <tr>
                                    <th>Part</th>
                                    <th>Category</th>
                                    <th>Unit</th>
                                    <th style="width:120px">Quantity</th>
                                </tr>
                            </thead>
                            <tbody id="bom-body">
                                @foreach($machine->parts as $part)
                                    @php
                                        $qty = $part->pivot->quantity ?? 0;
                                    @endphp
                                    <tr data-part-id="{{ $part->id }}">
                                        <td>{{ $part->name }}</td>
                                        <td>{{ $part->category?->name }}</td>
                                        <td>{{ $part->unit?->name }}</td>
                                        <td>
                                            <input type="number" min="0" name="parts[{{ $part->id }}][quantity]" value="{{ old('parts.' . $part->id . '.quantity', $qty) }}" class="form-control form-control-sm text-center">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Save BOM</button>
                        <a href="{{ route('machines.show', $machine->id) }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Parts search modal -->
    <div class="modal fade" id="partsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Parts</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="partsSearchResults">Loading...</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Copy to modal -->
    <div class="modal fade" id="copyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Copy BOM from {{ $machine->control_no}} | {{$machine->name}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="copyListContainer">Loading...</div>
                </div>
                <div class="modal-footer">
                    <button id="copySaveBtn" type="button" class="btn btn-primary">Copy Selected</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modalEl = document.getElementById('partsModal');
            const partsModal = new bootstrap.Modal(modalEl);
            const resultsContainer = document.getElementById('partsSearchResults');
            const openBtn = document.getElementById('openPartsModalBtn');
            const searchRoute = '{{ route('parts.search') }}';

            function loadSearch(url) {
                resultsContainer.innerHTML = 'Loading...';
                fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(r => r.text())
                    .then(html => {
                        resultsContainer.innerHTML = html;
                        attachAddButtons();
                        attachPaginationLinks();
                        attachSearchForm();
                    });
            }

            function attachSearchForm() {
                const form = resultsContainer.querySelector('#partsSearchForm');
                if (!form) return;
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    const q = form.querySelector('input[name=q]').value || '';
                    loadSearch(searchRoute + '?q=' + encodeURIComponent(q));
                });
            }

            function attachPaginationLinks() {
                const links = resultsContainer.querySelectorAll('.pagination a');
                links.forEach(a => {
                    a.addEventListener('click', function (e) {
                        e.preventDefault();
                        loadSearch(this.href);
                    });
                });
            }

            function attachAddButtons() {
                const buttons = resultsContainer.querySelectorAll('.add-part-btn');
                buttons.forEach(btn => {
                    btn.addEventListener('click', function () {
                        const partId = this.dataset.id;
                        const name = this.dataset.name;
                        const category = this.dataset.category;
                        const unit = this.dataset.unit;
                        const qtyInput = document.getElementById('part-qty-' + partId);
                        let qty = parseInt(qtyInput?.value || '1', 10);
                        if (isNaN(qty) || qty <= 0) qty = 1;
                        addOrUpdateBOMRow(partId, name, category, unit, qty);
                    });
                });
            }

            function addOrUpdateBOMRow(partId, name, category, unit, qty) {
                const tbody = document.getElementById('bom-body');
                function escapeHtml(unsafe) {
                    return String(unsafe)
                        .replaceAll('&', '&amp;')
                        .replaceAll('<', '&lt;')
                        .replaceAll('>', '&gt;')
                        .replaceAll('"', '&quot;')
                        .replaceAll("'", '&#039;');
                }
                if (!tbody) return;
                let existing = tbody.querySelector('tr[data-part-id="' + partId + '"]');
                if (existing) {
                    const input = existing.querySelector('input[name="parts[' + partId + '][quantity]"]');
                    const current = parseInt(input.value || '0', 10);
                    input.value = current + qty;
                } else {
                    const tr = document.createElement('tr');
                    tr.setAttribute('data-part-id', partId);
                    tr.innerHTML = `
                        <td>${escapeHtml(name)}</td>
                        <td>${escapeHtml(category || '')}</td>
                        <td>${escapeHtml(unit || '')}</td>
                        <td><input type="number" min="0" name="parts[${partId}][quantity]" value="${qty}" class="form-control form-control-sm text-center"></td>
                    `;
                    tbody.appendChild(tr);
                }
            }

            // Copy To modal logic
            const copyModalEl = document.getElementById('copyModal');
            const copyModal = new bootstrap.Modal(copyModalEl);
            const copyListContainer = document.getElementById('copyListContainer');
            const openCopyBtn = document.getElementById('openCopyModalBtn');
            const copySaveBtn = document.getElementById('copySaveBtn');
            const copyListUrl = '{{ route('machines.copy.list', $machine->id) }}';
            const copyToUrl = '{{ route('machines.copy.to', $machine->id) }}';

            openCopyBtn.addEventListener('click', function () {
                copyListContainer.innerHTML = 'Loading...';
                fetch(copyListUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(r => r.text())
                    .then(html => {
                        copyListContainer.innerHTML = html;
                        attachCopyPagination();
                        attachCopyFilter();
                    });
                copyModal.show();
            });

            function attachCopyPagination() {
                const links = copyListContainer.querySelectorAll('.pagination a');
                links.forEach(a => {
                    a.addEventListener('click', function (e) {
                        e.preventDefault();
                        fetch(this.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                            .then(r => r.text())
                            .then(html => {
                                copyListContainer.innerHTML = html;
                                attachCopyPagination();
                                attachCopyFilter();
                            });
                    });
                });
            }

            function attachCopyFilter() {
                const form = copyListContainer.querySelector('#copyListForm');
                const input = copyListContainer.querySelector('#copySearch');
                const btn = copyListContainer.querySelector('#copyFilterBtn');
                if (!form || !input || !btn) return;
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    const q = input.value || '';
                    const url = copyListUrl + '?q=' + encodeURIComponent(q);
                    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                        .then(r => r.text())
                        .then(html => {
                            copyListContainer.innerHTML = html;
                            attachCopyPagination();
                            attachCopyFilter();
                        });
                });
            }

            copySaveBtn.addEventListener('click', function () {
                const checkboxes = copyListContainer.querySelectorAll('.copy-target-checkbox:checked');
                const targets = Array.from(checkboxes).map(cb => cb.value);
                if (targets.length === 0) {
                    alert('Select at least one target machine to copy to.');
                    return;
                }

                fetch(copyToUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ targets })
                }).then(r => r.json())
                  .then(data => {
                      if (data.status === 'ok') {
                          copyModal.hide();
                          // reload page to show updated BOM
                          location.reload();
                      } else {
                          alert('Copy failed');
                      }
                  }).catch(() => alert('Copy failed'));
            });

            openBtn.addEventListener('click', function () {
                loadSearch(searchRoute);
                partsModal.show();
            });
        });
    </script>
    @endpush
@endsection
