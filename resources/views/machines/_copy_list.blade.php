<div>
    <form id="copyListForm" onsubmit="return false;">
        {{-- <div class="mb-2 d-flex">
            <input type="search" id="copySearch" name="q" value="{{ request()->query('q', '') }}" class="form-control me-2" placeholder="Filter by control no or name">
            <button id="copyFilterBtn" class="btn btn-primary">Filter</button>
        </div>

        @if(!request()->query('q'))
            <div class="mb-2"><small class="text-muted">Showing machines with the same name ("{{ $machine->name }}") by default. Use filter to find other machines.</small></div>
        @endif --}}

        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th style="width:40px"></th>
                        <th>Control No.</th>
                        <th>Name</th>
                        <th>Plant</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($machines as $m)
                        <tr>
                            <td><input type="checkbox" name="targets[]" value="{{ $m->id }}" class="copy-target-checkbox"></td>
                            <td>{{ $m->control_no }}</td>
                            <td>{{ $m->name }}</td>
                            <td>{{ $m->plant?->name }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center">
            <div>
                Showing {{ $machines->firstItem() }} - {{ $machines->lastItem() }} of {{ $machines->total() }}
            </div>
            <div>
                {!! $machines->links('pagination::bootstrap-5') !!}
            </div>
        </div>
    </form>
</div>
