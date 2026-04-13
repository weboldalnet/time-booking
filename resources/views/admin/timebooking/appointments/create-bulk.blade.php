@extends('admin.layouts.layout')

@section('title', 'Tömeges időpont létrehozás')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Tömeges időpont létrehozás</h3>
                    <a href="{{ route('admin.timebooking.appointments.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Vissza a listához
                    </a>
                </div>
                
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Tömeges létrehozás:</strong> Ezzel a funkcióval egyszerre több időpontot hozhat létre a megadott időszakban és napokon.
                    </div>

                    <form action="{{ route('admin.timebooking.appointments.store-bulk') }}" method="POST" id="bulkCreateForm">
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
                                    <small class="form-text text-muted">
                                        Ez a cím minden létrehozott időpontnál ugyanaz lesz
                                    </small>
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
                                              rows="3" 
                                              placeholder="Adja meg az időpont leírását">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="start_date" class="form-label">Kezdő dátum <span class="text-danger">*</span></label>
                                            <input type="date" 
                                                   class="form-control @error('start_date') is-invalid @enderror" 
                                                   id="start_date" 
                                                   name="start_date" 
                                                   value="{{ old('start_date') }}" 
                                                   required>
                                            @error('start_date')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="end_date" class="form-label">Befejező dátum <span class="text-danger">*</span></label>
                                            <input type="date" 
                                                   class="form-control @error('end_date') is-invalid @enderror" 
                                                   id="end_date" 
                                                   name="end_date" 
                                                   value="{{ old('end_date') }}" 
                                                   required>
                                            @error('end_date')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="start_time" class="form-label">Kezdés időpontja <span class="text-danger">*</span></label>
                                            <input type="time" 
                                                   class="form-control @error('start_time') is-invalid @enderror" 
                                                   id="start_time" 
                                                   name="start_time" 
                                                   value="{{ old('start_time', '09:00') }}" 
                                                   required>
                                            @error('start_time')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="end_time" class="form-label">Befejezés időpontja <span class="text-danger">*</span></label>
                                            <input type="time" 
                                                   class="form-control @error('end_time') is-invalid @enderror" 
                                                   id="end_time" 
                                                   name="end_time" 
                                                   value="{{ old('end_time', '10:00') }}" 
                                                   required>
                                            @error('end_time')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="capacity" class="form-label">Kapacitás <span class="text-danger">*</span></label>
                                    <input type="number" 
                                           class="form-control @error('capacity') is-invalid @enderror" 
                                           id="capacity" 
                                           name="capacity" 
                                           value="{{ old('capacity', 1) }}" 
                                           min="1" 
                                           max="100" 
                                           placeholder="1"
                                           required>
                                    <small class="form-text text-muted">
                                        Maximum 100 fő
                                    </small>
                                    @error('capacity')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Napok kiválasztása <span class="text-danger">*</span></label>
                                    <div class="form-check-container">
                                        @php
                                            $days = [
                                                'monday' => 'Hétfő',
                                                'tuesday' => 'Kedd',
                                                'wednesday' => 'Szerda',
                                                'thursday' => 'Csütörtök',
                                                'friday' => 'Péntek',
                                                'saturday' => 'Szombat',
                                                'sunday' => 'Vasárnap'
                                            ];
                                            $oldDays = old('days', []);
                                        @endphp
                                        @foreach($days as $key => $label)
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" 
                                                       class="custom-control-input" 
                                                       id="day_{{ $key }}" 
                                                       name="days[]" 
                                                       value="{{ $key }}"
                                                       {{ in_array($key, $oldDays) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="day_{{ $key }}">
                                                    {{ $label }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                    @error('days')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Válassza ki, hogy mely napokon szeretne időpontokat létrehozni
                                    </small>
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
                                            Aktív időpontok
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        Csak az aktív időpontok jelennek meg a nyilvános oldalon
                                    </small>
                                </div>

                                <div class="card mt-3">
                                    <div class="card-body">
                                        <h6 class="card-title">Előnézet</h6>
                                        <div id="preview-content">
                                            <p class="text-muted">Töltse ki a mezőket az előnézet megtekintéséhez</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-calendar-plus"></i> Időpontok létrehozása
                            </button>
                            <button type="button" class="btn btn-info ml-2" id="previewBtn">
                                <i class="fas fa-eye"></i> Előnézet
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

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewModalLabel">Létrehozandó időpontok előnézete</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="previewModalContent">
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="sr-only">Betöltés...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Bezárás</button>
                <button type="button" class="btn btn-primary" onclick="$('#bulkCreateForm').submit();">
                    <i class="fas fa-calendar-plus"></i> Létrehozás
                </button>
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
    var today = new Date().toISOString().split('T')[0];
    $('#start_date, #end_date').attr('min', today);
    
    // Auto-set end date when start date changes
    $('#start_date').on('change', function() {
        var startDate = $(this).val();
        if (startDate && !$('#end_date').val()) {
            // Set end date to 1 month after start date
            var date = new Date(startDate);
            date.setMonth(date.getMonth() + 1);
            var endDateString = date.toISOString().split('T')[0];
            $('#end_date').val(endDateString);
        }
        updatePreview();
    });
    
    // Validate end date is after start date
    $('#end_date').on('change', function() {
        var startDate = new Date($('#start_date').val());
        var endDate = new Date($(this).val());
        
        if (startDate && endDate && endDate < startDate) {
            alert('A befejező dátum a kezdő dátum után kell legyen!');
            $(this).focus();
        }
        updatePreview();
    });
    
    // Auto-set end time when start time changes
    $('#start_time').on('change', function() {
        var startTime = $(this).val();
        if (startTime && !$('#end_time').val()) {
            // Add 1 hour to start time
            var [hours, minutes] = startTime.split(':');
            var endHour = parseInt(hours) + 1;
            if (endHour > 23) endHour = 23;
            var endTimeString = endHour.toString().padStart(2, '0') + ':' + minutes;
            $('#end_time').val(endTimeString);
        }
        updatePreview();
    });
    
    // Validate end time is after start time
    $('#end_time').on('change', function() {
        var startTime = $('#start_time').val();
        var endTime = $(this).val();
        
        if (startTime && endTime && endTime <= startTime) {
            alert('A befejezés időpontja a kezdés után kell legyen!');
            $(this).focus();
        }
        updatePreview();
    });
    
    // Update preview when form changes
    $('#category_id, #title, input[name="days[]"], #capacity').on('change', updatePreview);
    
    // Preview button click
    $('#previewBtn').on('click', function() {
        if (validateForm()) {
            showPreviewModal();
        }
    });
    
    // Select all weekdays by default
    $('#day_monday, #day_tuesday, #day_wednesday, #day_thursday, #day_friday').prop('checked', true);
    
    // Initial preview update
    updatePreview();
});

function updatePreview() {
    var startDate = $('#start_date').val();
    var endDate = $('#end_date').val();
    var selectedDays = $('input[name="days[]"]:checked').length;
    var title = $('#title').val();
    var capacity = $('#capacity').val();
    
    if (startDate && endDate && selectedDays > 0) {
        var start = new Date(startDate);
        var end = new Date(endDate);
        var dayCount = Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1;
        var estimatedAppointments = Math.floor(dayCount / 7 * selectedDays);
        
        var previewHtml = `
            <p><strong>Becsült időpontok száma:</strong> <span class="badge badge-primary">${estimatedAppointments}</span></p>
            <p><strong>Időszak:</strong> ${formatDate(startDate)} - ${formatDate(endDate)}</p>
            <p><strong>Kiválasztott napok:</strong> ${selectedDays}</p>
            ${title ? `<p><strong>Cím:</strong> ${title}</p>` : ''}
            ${capacity ? `<p><strong>Kapacitás:</strong> ${capacity} fő</p>` : ''}
        `;
        
        $('#preview-content').html(previewHtml);
    } else {
        $('#preview-content').html('<p class="text-muted">Töltse ki a mezőket az előnézet megtekintéséhez</p>');
    }
}

function showPreviewModal() {
    $('#previewModal').modal('show');
    
    // Generate detailed preview
    var formData = $('#bulkCreateForm').serialize();
    
    $('#previewModalContent').html(`
        <div class="text-center">
            <div class="spinner-border" role="status">
                <span class="sr-only">Betöltés...</span>
            </div>
            <p class="mt-2">Előnézet generálása...</p>
        </div>
    `);
    
    // Simulate preview generation (in real implementation, this would be an AJAX call)
    setTimeout(function() {
        generatePreviewContent();
    }, 1000);
}

function generatePreviewContent() {
    var startDate = new Date($('#start_date').val());
    var endDate = new Date($('#end_date').val());
    var startTime = $('#start_time').val();
    var endTime = $('#end_time').val();
    var title = $('#title').val();
    var capacity = $('#capacity').val();
    var selectedDays = [];
    
    $('input[name="days[]"]:checked').each(function() {
        selectedDays.push($(this).val());
    });
    
    var dayMap = {
        'sunday': 0, 'monday': 1, 'tuesday': 2, 'wednesday': 3,
        'thursday': 4, 'friday': 5, 'saturday': 6
    };
    
    var appointments = [];
    var current = new Date(startDate);
    
    while (current <= endDate) {
        var dayName = Object.keys(dayMap).find(key => dayMap[key] === current.getDay());
        if (selectedDays.includes(dayName)) {
            appointments.push({
                date: new Date(current),
                title: title,
                startTime: startTime,
                endTime: endTime,
                capacity: capacity
            });
        }
        current.setDate(current.getDate() + 1);
    }
    
    var previewHtml = `
        <div class="alert alert-info">
            <strong>Összesen ${appointments.length} időpont lesz létrehozva</strong>
        </div>
        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
            <table class="table table-sm table-striped">
                <thead class="thead-light">
                    <tr>
                        <th>Dátum</th>
                        <th>Nap</th>
                        <th>Időpont</th>
                        <th>Cím</th>
                        <th>Kapacitás</th>
                    </tr>
                </thead>
                <tbody>
    `;
    
    appointments.forEach(function(appointment) {
        var dayNames = ['Vasárnap', 'Hétfő', 'Kedd', 'Szerda', 'Csütörtök', 'Péntek', 'Szombat'];
        previewHtml += `
            <tr>
                <td>${formatDate(appointment.date.toISOString().split('T')[0])}</td>
                <td>${dayNames[appointment.date.getDay()]}</td>
                <td>${appointment.startTime} - ${appointment.endTime}</td>
                <td>${appointment.title}</td>
                <td>${appointment.capacity} fő</td>
            </tr>
        `;
    });
    
    previewHtml += `
                </tbody>
            </table>
        </div>
    `;
    
    $('#previewModalContent').html(previewHtml);
}

function validateForm() {
    var isValid = true;
    var errors = [];
    
    if (!$('#category_id').val()) {
        errors.push('Válasszon kategóriát');
        isValid = false;
    }
    
    if (!$('#title').val()) {
        errors.push('Adja meg az időpont címét');
        isValid = false;
    }
    
    if (!$('#start_date').val()) {
        errors.push('Adja meg a kezdő dátumot');
        isValid = false;
    }
    
    if (!$('#end_date').val()) {
        errors.push('Adja meg a befejező dátumot');
        isValid = false;
    }
    
    if (!$('#start_time').val()) {
        errors.push('Adja meg a kezdés időpontját');
        isValid = false;
    }
    
    if (!$('#end_time').val()) {
        errors.push('Adja meg a befejezés időpontját');
        isValid = false;
    }
    
    if ($('input[name="days[]"]:checked').length === 0) {
        errors.push('Válasszon legalább egy napot');
        isValid = false;
    }
    
    if (!$('#capacity').val() || $('#capacity').val() < 1) {
        errors.push('Adja meg a kapacitást (minimum 1)');
        isValid = false;
    }
    
    if (!isValid) {
        alert('Kérjük, javítsa a következő hibákat:\n\n' + errors.join('\n'));
    }
    
    return isValid;
}

function formatDate(dateString) {
    var date = new Date(dateString);
    return date.toLocaleDateString('hu-HU');
}
</script>
@endpush