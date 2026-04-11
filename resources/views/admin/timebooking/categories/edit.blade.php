@extends('admin.layouts.app')

@section('title', 'Kategória szerkesztése')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Kategória szerkesztése: {{ $category->name }}</h3>
                    <div>
                        <a href="{{ route('admin.timebooking.categories.show', $category) }}" class="btn btn-info mr-2">
                            <i class="fas fa-eye"></i> Megtekintés
                        </a>
                        <a href="{{ route('admin.timebooking.categories.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Vissza a listához
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <form action="{{ route('admin.timebooking.categories.update', $category) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="name" class="form-label">Kategória neve <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name', $category->name) }}" 
                                           placeholder="Adja meg a kategória nevét"
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="description" class="form-label">Leírás</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" 
                                              name="description" 
                                              rows="4" 
                                              placeholder="Adja meg a kategória leírását">{{ old('description', $category->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="sort_order" class="form-label">Sorrend</label>
                                    <input type="number" 
                                           class="form-control @error('sort_order') is-invalid @enderror" 
                                           id="sort_order" 
                                           name="sort_order" 
                                           value="{{ old('sort_order', $category->sort_order) }}" 
                                           min="0" 
                                           placeholder="0">
                                    <small class="form-text text-muted">
                                        Alacsonyabb szám = előrébb jelenik meg
                                    </small>
                                    @error('sort_order')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" 
                                               class="custom-control-input" 
                                               id="is_active" 
                                               name="is_active" 
                                               value="1" 
                                               {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_active">
                                            Aktív kategória
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        Csak az aktív kategóriák jelennek meg a nyilvános oldalon
                                    </small>
                                </div>

                                <div class="card mt-3">
                                    <div class="card-body">
                                        <h6 class="card-title">Statisztikák</h6>
                                        <p class="card-text">
                                            <strong>Időpontok száma:</strong> {{ $category->appointments()->count() }}<br>
                                            <strong>Létrehozva:</strong> {{ $category->created_at->format('Y.m.d H:i') }}<br>
                                            <strong>Utolsó módosítás:</strong> {{ $category->updated_at->format('Y.m.d H:i') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Változások mentése
                            </button>
                            <a href="{{ route('admin.timebooking.categories.show', $category) }}" class="btn btn-info ml-2">
                                <i class="fas fa-eye"></i> Megtekintés
                            </a>
                            <a href="{{ route('admin.timebooking.categories.index') }}" class="btn btn-secondary ml-2">
                                <i class="fas fa-times"></i> Mégse
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            @if($category->appointments()->count() > 0)
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Kapcsolódó időpontok ({{ $category->appointments()->count() }} db)</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            Ez a kategória {{ $category->appointments()->count() }} időpontot tartalmaz. 
                            A kategória törléséhez először törölnie kell az összes kapcsolódó időpontot.
                        </div>
                        <a href="{{ route('admin.timebooking.appointments.index', ['category_id' => $category->id]) }}" 
                           class="btn btn-outline-primary">
                            <i class="fas fa-calendar-alt"></i> Időpontok megtekintése
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-focus on name field
    $('#name').focus();
    
    // Character counter for description
    $('#description').on('input', function() {
        const maxLength = 1000;
        const currentLength = $(this).val().length;
        const remaining = maxLength - currentLength;
        
        if (!$('#char-counter').length) {
            $(this).after('<small id="char-counter" class="form-text text-muted"></small>');
        }
        
        $('#char-counter').text(`${currentLength}/${maxLength} karakter`);
        
        if (remaining < 50) {
            $('#char-counter').removeClass('text-muted').addClass('text-warning');
        } else {
            $('#char-counter').removeClass('text-warning').addClass('text-muted');
        }
    });
    
    // Trigger character counter on page load
    $('#description').trigger('input');
});
</script>
@endpush