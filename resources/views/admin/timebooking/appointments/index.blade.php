@extends('admin.layouts.layout')

@section('title', 'Időpontok kezelése')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Időpontok kezelése</h3>
                    <div>
                        <a href="{{ route('admin.timebooking.appointments.create-bulk') }}" class="btn btn-info mr-2">
                            <i class="fas fa-calendar-plus"></i> Tömeges létrehozás
                        </a>
                        <a href="{{ route('admin.timebooking.appointments.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Új időpont
                        </a>
                    </div>
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

                    <!-- Filters -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <form method="GET" action="{{ route('admin.timebooking.appointments.index') }}">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="category_id">Kategória</label>
                                            <select name="category_id" id="category_id" class="form-control">
                                                <option value="">Összes kategória</option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}" 
                                                            {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="date_from">Dátum-tól</label>
                                            <input type="date" name="date_from" id="date_from" 
                                                   class="form-control" value="{{ request('date_from') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="date_to">Dátum-ig</label>
                                            <input type="date" name="date_to" id="date_to" 
                                                   class="form-control" value="{{ request('date_to') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="status">Státusz</label>
                                            <select name="status" id="status" class="form-control">
                                                <option value="">Összes státusz</option>
                                                <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>
                                                    Elérhető
                                                </option>
                                                <option value="booked" {{ request('status') == 'booked' ? 'selected' : '' }}>
                                                    Foglalt
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search"></i> Szűrés
                                        </button>
                                        <a href="{{ route('admin.timebooking.appointments.index') }}" class="btn btn-secondary ml-2">
                                            <i class="fas fa-times"></i> Szűrők törlése
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    @if($appointments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Cím</th>
                                        <th>Kategória</th>
                                        <th>Időpont</th>
                                        <th>Kapacitás</th>
                                        <th>Foglalások</th>
                                        <th>Státusz</th>
                                        <th>Létrehozva</th>
                                        <th width="200">Műveletek</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($appointments as $appointment)
                                        <tr>
                                            <td>
                                                <strong>{{ $appointment->title }}</strong>
                                                @if($appointment->description)
                                                    <br><small class="text-muted">{{ Str::limit($appointment->description, 50) }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-info">{{ $appointment->category->name }}</span>
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
                                                    <button type="button" 
                                                            class="btn btn-sm btn-success" 
                                                            onclick="showBookings({{ $appointment->id }})">
                                                        {{ $appointment->bookings->count() }} foglalás
                                                    </button>
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
                                                {{ $appointment->created_at->format('Y.m.d H:i') }}
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
                                                        <button type="button" 
                                                                class="btn btn-sm btn-danger" 
                                                                title="Törlés"
                                                                onclick="confirmDelete({{ $appointment->id }}, '{{ $appointment->title }}')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @else
                                                        <span class="btn btn-sm btn-secondary disabled" title="Nem szerkeszthető, mert van foglalás">
                                                            <i class="fas fa-lock"></i>
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center">
                            {{ $appointments->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Még nincsenek időpontok</h5>
                            <p class="text-muted">Kezdje el az első időpont létrehozásával.</p>
                            <a href="{{ route('admin.timebooking.appointments.create') }}" class="btn btn-primary mr-2">
                                <i class="fas fa-plus"></i> Új időpont létrehozása
                            </a>
                            <a href="{{ route('admin.timebooking.appointments.create-bulk') }}" class="btn btn-info">
                                <i class="fas fa-calendar-plus"></i> Tömeges létrehozás
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
                <h5 class="modal-title" id="deleteModalLabel">Időpont törlése</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Biztosan törölni szeretné a(z) <strong id="appointmentTitle"></strong> időpontot?
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
function confirmDelete(appointmentId, appointmentTitle) {
    document.getElementById('appointmentTitle').textContent = appointmentTitle;
    document.getElementById('deleteForm').action = '/timebooking/appointments/' + appointmentId;
    $('#deleteModal').modal('show');
}

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
    $.get(`/timebooking/appointments/${appointmentId}/bookings`)
        .done(function(data) {
            let content = `<h6>${data.appointment.title}</h6>`;
            content += `<p class="text-muted">${data.appointment.start_time} - ${data.appointment.end_time}</p>`;
            
            if (data.bookings.length > 0) {
                content += '<div class="table-responsive"><table class="table table-sm">';
                content += '<thead><tr><th>Név</th><th>Email</th><th>Telefon</th><th>Üzenet</th><th>Státusz</th><th>Foglalás dátuma</th></tr></thead>';
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
                        <td>${booking.message || '-'}</td>
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