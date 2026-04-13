@extends('admin.layouts.layout')

@section('title', 'Új időpont létrehozása')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Új időpont létrehozása</h3>
                    <a href="{{ route('admin.timebooking.appointments.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Vissza a listához
                    </a>
                </div>

                <div class="card-body">
                    <form action="{{ route('admin.timebooking.appointments.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="category_id" class="form-label">Kategória <span class="text-danger">*</span></label>
                                    <select class="form-control @error('category_id') is-invalid @enderror" 
                                            id="category_id" 
                                            name="category_id" 
                                            required>
                                        <option value="">Válasszon kategóriát</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="title" class="form-label">Időpont címe <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('title') is-invalid @enderror" 
                                           id="title" 
                                           name="title" 
                                           value="{{ old('title') }}" 
                                           placeholder="Adja meg az időpont címét"
                                           required>
                                    @error('title')
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
                                              placeholder="Adja meg az időpont leírását">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="start_time" class="form-label">Kezdés időpontja <span class="text-danger">*</span></label>
                                    <input type="datetime-local" 
                                           class="form-control @error('start_time') is-invalid @enderror" 
                                           id="start_time" 
                                           name="start_time" 
                                           value="{{ old('start_time') }}" 
                                           required>
                                    @error('start_time')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="end_time" class="form-label">Befejezés időpontja <span class="text-danger">*</span></label>
                                    <input type="datetime-local" 
                                           class="form-control @error('end_time') is-invalid @enderror" 
                                           id="end_time" 
                                           name="end_time" 
                                           value="{{ old('end_time') }}" 
                                           required>
                                    @error('end_time')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="capacity" class="form-label">Kapacitás <span class="text-danger">*</span></label>
                                    <input type="number" 
                                           class="form-control @error('capacity') is-invalid @enderror" 
                                           id="capacity" 
                                           name="capacity" 
                                           value="{{ old('capacity', config('timebooking.booking.default_capacity')) }}" 
                                           min="1" 
                                           max="{{ config('timebooking.booking.max_capacity') }}" 
                                           placeholder="{{ config('timebooking.booking.default_capacity') }}"
                                           required>
                                    <small class="form-text text-muted">
                                        Maximum {{ config('timebooking.booking.max_capacity') }} fő
                                    </small>
                                    @error('capacity')
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
                                               {{ old('is_active', true) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_active">
                                            Aktív időpont
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        Csak az aktív időpontok jelennek meg a nyilvános oldalon
                                    </small>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Időpont mentése
                            </button>
                            <a href="{{ route('admin.timebooking.appointments.index') }}" class="btn btn-secondary ml-2">
                                <i class="fas fa-times"></i> Mégse
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-focus on category field
    $('#category_id').focus();

    // Set minimum date to today
    var now = new Date();
    var minDateTime = now.toISOString().slice(0, 16);
    $('#start_time, #end_time').attr('min', minDateTime);

    // Auto-set end time when start time changes
    $('#start_time').on('change', function() {
        var startTime = new Date($(this).val());
        if (startTime) {
            startTime.setHours(startTime.getHours() + 1);
            var endTimeString = startTime.toISOString().slice(0, 16);
            $('#end_time').val(endTimeString);
        }
    });

    // Validate end time is after start time
    $('#end_time').on('change', function() {
        var startTime = new Date($('#start_time').val());
        var endTime = new Date($(this).val());

        if (startTime && endTime && endTime <= startTime) {
            alert('A befejezés időpontja a kezdés után kell legyen!');
            $(this).focus();
        }
    });

    // Character counter for description
    $('#description').on('input', function() {
        const maxLength = 1000;
        const currentLength = $(this).val().length;

        if (!$('#char-counter').length) {
            $(this).after('<small id="char-counter" class="form-text text-muted"></small>');
        }

        $('#char-counter').text(`${currentLength}/${maxLength} karakter`);

        if (currentLength > maxLength * 0.9) {
            $('#char-counter').removeClass('text-muted').addClass('text-warning');
        } else {
            $('#char-counter').removeClass('text-warning').addClass('text-muted');
        }
    });
});
</script>
@endpush
