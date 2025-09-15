<!-- Paginação -->
@if($forcings->hasPages())
    <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="text-muted" id="pagination-info">
            Mostrando {{ $forcings->firstItem() }} a {{ $forcings->lastItem() }} de {{ $forcings->total() }} forcings
        </div>
        <div>
            {{ $forcings->appends(request()->query())->links('pagination::bootstrap-4') }}
        </div>
    </div>
@endif
