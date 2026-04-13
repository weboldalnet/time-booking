@extends('admin.layouts.layout')

@section('title', 'Kategóriák kezelése')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Kategóriák kezelése</h3>
                    <a href="{{ route('admin.timebooking.categories.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Új kategória
                    </a>
                </div>
                
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if($categories->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Név</th>
                                        <th>Leírás</th>
                                        <th>Sorrend</th>
                                        <th>Időpontok száma</th>
                                        <th>Státusz</th>
                                        <th>Létrehozva</th>
                                        <th width="200">Műveletek</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($categories as $category)
                                        <tr>
                                            <td>
                                                <strong>{{ $category->name }}</strong>
                                            </td>
                                            <td>
                                                {{ Str::limit($category->description, 50) }}
                                            </td>
                                            <td>
                                                <span class="badge badge-secondary">{{ $category->sort_order }}</span>
                                            </td>
                                            <td>
                                                <span class="badge badge-info">{{ $category->appointments_count }}</span>
                                            </td>
                                            <td>
                                                @if($category->is_active)
                                                    <span class="badge badge-success">Aktív</span>
                                                @else
                                                    <span class="badge badge-secondary">Inaktív</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $category->created_at->format('Y.m.d H:i') }}
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.timebooking.categories.show', $category) }}" 
                                                       class="btn btn-sm btn-info" title="Megtekintés">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.timebooking.categories.edit', $category) }}" 
                                                       class="btn btn-sm btn-warning" title="Szerkesztés">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('admin.timebooking.categories.toggle-status', $category) }}" 
                                                          method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" 
                                                                class="btn btn-sm {{ $category->is_active ? 'btn-secondary' : 'btn-success' }}" 
                                                                title="{{ $category->is_active ? 'Deaktiválás' : 'Aktiválás' }}">
                                                            <i class="fas {{ $category->is_active ? 'fa-pause' : 'fa-play' }}"></i>
                                                        </button>
                                                    </form>
                                                    @if($category->appointments_count == 0)
                                                        <button type="button" 
                                                                class="btn btn-sm btn-danger" 
                                                                title="Törlés"
                                                                onclick="confirmDelete({{ $category->id }}, '{{ $category->name }}')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center">
                            {{ $categories->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Még nincsenek kategóriák</h5>
                            <p class="text-muted">Kezdje el az első kategória létrehozásával.</p>
                            <a href="{{ route('admin.timebooking.categories.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Új kategória létrehozása
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Kategória törlése</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Biztosan törölni szeretné a(z) <strong id="categoryName"></strong> kategóriát?
                <br><small class="text-muted">Ez a művelet nem vonható vissza.</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Mégse</button>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Törlés</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete(categoryId, categoryName) {
    document.getElementById('categoryName').textContent = categoryName;
    document.getElementById('deleteForm').action = '/timebooking/categories/' + categoryId;
    $('#deleteModal').modal('show');
}
</script>
@endpush