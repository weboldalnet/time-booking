@extends('admin.layouts.layout')

@section('title', 'Időpont szerkesztése')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Időpont szerkesztése: {{ $appointment->title }}</h3>
                    <div>
                        <a href="{{ route('admin.timebooking.appointments.show', $appointment) }}" class="btn btn-info mr-2">
                            <i class="fas fa-eye"></i> Megtekintés
                        </a>
                        <a href="{{ route('admin.timebooking.appointments.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Vissza a listához
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if($appointment->has_bookings)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            <strong>Figyelem!</strong> Ez az időpont már tartalmaz foglalásokat, ezért nem szerkeszthető.
                            <a href="{{ route('admin.timebooking.appointments.show', $appointment) }}" class="alert-link">
                                Megtekintés
                            </a>
                        </div>
                    @else
                        <form action="{{ route('admin.timebooking.appointments.update', $appointment) }}" method="POST">
                            @csrf
                            @method('PUT')

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
                                                <option value="{{ $category->id }}" 
                                                        {{ old('category_id', $appointment->category_id) == $category->id ? 'selected' : '' }}>
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
                                               value="{{ old('title', $appointment->title) }}" 
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
                                                  placeholder="Adja meg az időpont leírását">{{ old('description', $appointment->description) }}</textarea>
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
                                               value="{{ old('start_time', $appointment->start_time->format('Y-m-d\TH:i')) }}" 
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
                                               value="{{ old('end_time', $appointment->end_time->format('Y-m-d\TH:i')) }}" 
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
                                               value="{{ old('capacity', $appointment->capacity) }}" 
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
                                                   {{ old('is_active', $appointment->is_active) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="is_active">
                                                Aktív időpont
                                            </label>
                                        </div>
                                        <small class="form-text text-muted">
                                            Csak az aktív időpontok jelennek meg a nyilvános oldalon
                                        </small>
                                    </div>

                                    <div class="card mt-3">
                                        <div class="card-body">
                                            <h6 class="card-title">Statisztikák</h6>
                                            <p class="card-text">
                                                <strong>Jelenlegi kapacitás:</strong> {{ $appointment->available_capacity }}/{{ $appointment->capacity }}<br>
                                                <strong>Foglalások száma:</strong> {{ $appointment->bookings()->count() }}<br>
                                                <strong>Létrehozva:</strong> {{ $appointment->created_at->format('Y.m.d H:i') }}<br>
                                                <strong>Utolsó módosítás:</strong> {{ $appointment->updated_at->format('Y.m.d H:i') }}
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
                                <a href="{{ route('admin.timebooking.appointments.show', $appointment) }}" class="btn btn-info ml-2">
                                    <i class="fas fa-eye"></i> Megtekintés
                                </a>
                                <a href="{{ route('admin.timebooking.appointments.index') }}" class="btn btn-secondary ml-2">
                                    <i class="fas fa-times"></i> Mégse
                                </a>
                            </div>
                        </form>
                    @endif
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

    // Set minimum date to today for future appointments
    var now = new Date();
    var appointmentStart = new Date('{{ $appointment->start_time->format('Y-m-d\TH:i:s') }}');

    // Only set minimum date if appointment is in the future
    if (appointmentStart > now) {
        var minDateTime = now.toISOString().slice(0, 16);
        $('#start_time, #end_time').attr('min', minDateTime);
    }

    // Auto-set end time when start time changes
    $('#start_time').on('change', function() {
        var startTime = new Date($(this).val());
        var currentEndTime = new Date($('#end_time').val());

        if (startTime && currentEndTime) {
            // Calculate duration and maintain it
            var originalStart = new Date('{{ $appointment->start_time->format('Y-m-d\TH:i:s') }}');
            var originalEnd = new Date('{{ $appointment->end_time->format('Y-m-d\TH:i:s') }}');
            var duration = originalEnd - originalStart;

            var newEndTime = new Date(startTime.getTime() + duration);
            var endTimeString = newEndTime.toISOString().slice(0, 16);
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

    // Trigger character counter on page load
    $('#description').trigger('input');
});
</script>
@endpush
