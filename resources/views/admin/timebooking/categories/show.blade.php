@extends('admin.layouts.layout')

@section('title', 'Kategória megtekintése')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Kategória részletei</h3>
                    <div>
                        <a href="{{ route('admin.timebooking.categories.edit', $category) }}" class="btn btn-warning mr-2">
                            <i class="fas fa-edit"></i> Szerkesztés
                        </a>
                        <a href="{{ route('admin.timebooking.categories.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Vissza a listához
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h4>{{ $category->name }}</h4>
                            
                            @if($category->description)
                                <div class="mt-3">
                                    <h6>Leírás:</h6>
                                    <p class="text-muted">{{ $category->description }}</p>
                                </div>
                            @endif
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Kategória információk</h6>
                                    
                                    <div class="mb-2">
                                        <strong>Státusz:</strong>
                                        @if($category->is_active)
                                            <span class="badge badge-success ml-1">Aktív</span>
                                        @else
                                            <span class="badge badge-secondary ml-1">Inaktív</span>
                                        @endif
                                    </div>
                                    
                                    <div class="mb-2">
                                        <strong>Sorrend:</strong>
                                        <span class="badge badge-info ml-1">{{ $category->sort_order }}</span>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <strong>Időpontok száma:</strong>
                                        <span class="badge badge-primary ml-1">{{ $category->appointments->count() }}</span>
                                    </div>
                                    
                                    <hr>
                                    
                                    <div class="mb-2">
                                        <strong>Létrehozva:</strong><br>
                                        <small class="text-muted">{{ $category->created_at->format('Y. m. d. H:i') }}</small>
                                    </div>
                                    
                                    <div class="mb-0">
                                        <strong>Utolsó módosítás:</strong><br>
                                        <small class="text-muted">{{ $category->updated_at->format('Y. m. d. H:i') }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($category->appointments->count() > 0)
                <div class="card mt-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Kapcsolódó időpontok ({{ $category->appointments->count() }} db)</h5>
                        <a href="{{ route('admin.timebooking.appointments.create', ['category_id' => $category->id]) }}" 
                           class="btn btn-sm btn-primary">
                            <i class="fas fa-plus"></i> Új időpont
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Cím</th>
                                        <th>Időpont</th>
                                        <th>Kapacitás</th>
                                        <th>Foglalások</th>
                                        <th>Státusz</th>
                                        <th>Műveletek</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($category->appointments->sortBy('start_time') as $appointment)
                                        <tr>
                                            <td>
                                                <strong>{{ $appointment->title }}</strong>
                                                @if($appointment->description)
                                                    <br><small class="text-muted">{{ Str::limit($appointment->description, 50) }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <strong>{{ $appointment->start_time->format('Y.m.d') }}</strong><br>
                                                <small class="text-muted">
                                                    {{ $appointment->start_time->format('H:i') }} - {{ $appointment->end_time->format('H:i') }}
                                                </small>
                                            </td>
                                            <td>
                                                <span class="badge badge-info">{{ $appointment->available_capacity }}/{{ $appointment->capacity }}</span>
                                            </td>
                                            <td>
                                                @if($appointment->bookings->count() > 0)
                                                    <span class="badge badge-success">{{ $appointment->bookings->count() }}</span>
                                                @else
                                                    <span class="badge badge-secondary">0</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($appointment->is_active)
                                                    @if($appointment->start_time > now())
                                                        @if($appointment->available_capacity > 0)
                                                            <span class="badge badge-success">Elérhető</span>
                                                        @else
                                                            <span class="badge badge-warning">Betelt</span>
                                                        @endif
                                                    @else
                                                        <span class="badge badge-secondary">Lejárt</span>
                                                    @endif
                                                @else
                                                    <span class="badge badge-danger">Inaktív</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.timebooking.appointments.show', $appointment) }}" 
                                                       class="btn btn-sm btn-info" title="Megtekintés">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if(!$appointment->has_bookings)
                                                        <a href="{{ route('admin.timebooking.appointments.edit', $appointment) }}" 
                                                           class="btn btn-sm btn-warning" title="Szerkesztés">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    @endif
                                                    @if($appointment->bookings->count() > 0)
                                                        <button type="button" 
                                                                class="btn btn-sm btn-success" 
                                                                title="Foglalások megtekintése"
                                                                onclick="showBookings({{ $appointment->id }})">
                                                            <i class="fas fa-users"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                <div class="card mt-4">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-calendar-plus fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Még nincsenek időpontok ehhez a kategóriához</h5>
                        <p class="text-muted">Kezdje el az első időpont létrehozásával.</p>
                        <a href="{{ route('admin.timebooking.appointments.create', ['category_id' => $category->id]) }}" 
                           class="btn btn-primary">
                            <i class="fas fa-plus"></i> Új időpont létrehozása
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Bookings Modal -->
<div class="modal fade" id="bookingsModal" tabindex="-1" role="dialog" aria-labelledby="bookingsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bookingsModalLabel">Foglalások</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="bookingsContent">
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="sr-only">Betöltés...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showBookings(appointmentId) {
    $('#bookingsModal').modal('show');
    
    // Reset content
    $('#bookingsContent').html(`
        <div class="text-center">
            <div class="spinner-border" role="status">
                <span class="sr-only">Betöltés...</span>
            </div>
        </div>
    `);
    
    // Load bookings via AJAX
    $.get(`/admin/timebooking/appointments/${appointmentId}/bookings`)
        .done(function(data) {
            let content = `<h6>${data.appointment.title}</h6>`;
            content += `<p class="text-muted">${data.appointment.start_time} - ${data.appointment.end_time}</p>`;
            
            if (data.bookings.length > 0) {
                content += '<div class="table-responsive"><table class="table table-sm">';
                content += '<thead><tr><th>Név</th><th>Email</th><th>Telefon</th><th>Státusz</th><th>Foglalás dátuma</th></tr></thead>';
                content += '<tbody>';
                
                data.bookings.forEach(function(booking) {
                    let statusBadge = '';
                    switch(booking.status) {
                        case 'confirmed':
                            statusBadge = '<span class="badge badge-success">Megerősítve</span>';
                            break;
                        case 'pending':
                            statusBadge = '<span class="badge badge-warning">Függőben</span>';
                            break;
                        case 'cancelled':
                            statusBadge = '<span class="badge badge-danger">Lemondva</span>';
                            break;
                    }
                    
                    content += `<tr>
                        <td>${booking.name}</td>
                        <td>${booking.email}</td>
                        <td>${booking.phone || '-'}</td>
                        <td>${statusBadge}</td>
                        <td>${booking.booking_date}</td>
                    </tr>`;
                });
                
                content += '</tbody></table></div>';
            } else {
                content += '<p class="text-muted">Még nincsenek foglalások ehhez az időponthoz.</p>';
            }
            
            $('#bookingsContent').html(content);
        })
        .fail(function() {
            $('#bookingsContent').html('<div class="alert alert-danger">Hiba történt a foglalások betöltése során.</div>');
        });
}
</script>
@endpush