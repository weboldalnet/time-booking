@extends('admin.layouts.layout')

@section('title', 'Időpont megtekintése')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Időpont részletei</h3>
                    <div>
                        @if(!$appointment->has_bookings)
                            <a href="{{ route('admin.timebooking.appointments.edit', $appointment) }}" class="btn btn-warning mr-2">
                                <i class="fas fa-edit"></i> Szerkesztés
                            </a>
                        @endif
                        <a href="{{ route('admin.timebooking.appointments.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Vissza a listához
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h4>{{ $appointment->title }}</h4>

                            @if($appointment->description)
                                <div class="mt-3">
                                    <h6>Leírás:</h6>
                                    <p class="text-muted">{{ $appointment->description }}</p>
                                </div>
                            @endif

                            <div class="mt-4">
                                <h6>Időpont részletei:</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-2">
                                            <i class="fas fa-tag text-primary mr-2"></i>
                                            <strong>Kategória:</strong> 
                                            <a href="{{ route('admin.timebooking.categories.show', $appointment->category) }}">
                                                {{ $appointment->category->name }}
                                            </a>
                                        </div>

                                        <div class="mb-2">
                                            <i class="fas fa-calendar text-primary mr-2"></i>
                                            <strong>Dátum:</strong> {{ $appointment->start_time->format('Y. m. d.') }}
                                        </div>

                                        <div class="mb-2">
                                            <i class="fas fa-clock text-primary mr-2"></i>
                                            <strong>Időtartam:</strong> 
                                            {{ $appointment->start_time->format('H:i') }} - {{ $appointment->end_time->format('H:i') }}
                                            ({{ $appointment->start_time->diffInMinutes($appointment->end_time) }} perc)
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-2">
                                            <i class="fas fa-users text-primary mr-2"></i>
                                            <strong>Kapacitás:</strong> 
                                            <span class="badge badge-info">{{ $appointment->available_capacity }}/{{ $appointment->capacity }}</span>
                                        </div>

                                        <div class="mb-2">
                                            <i class="fas fa-info-circle text-primary mr-2"></i>
                                            <strong>Státusz:</strong>
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
                                        </div>

                                        <div class="mb-2">
                                            <i class="fas fa-calendar-check text-primary mr-2"></i>
                                            <strong>Foglalások száma:</strong> 
                                            <span class="badge badge-primary">{{ $appointment->bookings->count() }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Időpont információk</h6>

                                    <div class="mb-2">
                                        <strong>Létrehozva:</strong><br>
                                        <small class="text-muted">{{ $appointment->created_at->format('Y. m. d. H:i') }}</small>
                                    </div>

                                    <div class="mb-2">
                                        <strong>Utolsó módosítás:</strong><br>
                                        <small class="text-muted">{{ $appointment->updated_at->format('Y. m. d. H:i') }}</small>
                                    </div>

                                    @if($appointment->start_time > now())
                                        <div class="mb-2">
                                            <strong>Kezdésig hátralévő idő:</strong><br>
                                            <small class="text-muted">{{ $appointment->start_time->diffForHumans() }}</small>
                                        </div>
                                    @endif

                                    <hr>

                                    <div class="text-center">
                                        @if(!$appointment->has_bookings)
                                            <a href="{{ route('admin.timebooking.appointments.edit', $appointment) }}" 
                                               class="btn btn-sm btn-warning btn-block mb-2">
                                                <i class="fas fa-edit"></i> Szerkesztés
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-danger btn-block" 
                                                    onclick="confirmDelete({{ $appointment->id }}, '{{ $appointment->title }}')">
                                                <i class="fas fa-trash"></i> Törlés
                                            </button>
                                        @else
                                            <div class="alert alert-info">
                                                <small>
                                                    <i class="fas fa-info-circle"></i>
                                                    Ez az időpont nem szerkeszthető és nem törölhető, mert már vannak hozzá foglalások.
                                                </small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($appointment->bookings->count() > 0)
                <div class="card mt-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Foglalások ({{ $appointment->bookings->count() }} db)</h5>
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-info" onclick="exportBookings({{ $appointment->id }})">
                                <i class="fas fa-download"></i> Export
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Név</th>
                                        <th>Email</th>
                                        <th>Telefon</th>
                                        <th>Üzenet</th>
                                        <th>Státusz</th>
                                        <th>Foglalás dátuma</th>
                                        <th>Műveletek</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($appointment->bookings->sortBy('booking_date') as $booking)
                                        <tr>
                                            <td>
                                                <strong>{{ $booking->name }}</strong>
                                            </td>
                                            <td>
                                                <a href="mailto:{{ $booking->email }}">{{ $booking->email }}</a>
                                            </td>
                                            <td>
                                                @if($booking->phone)
                                                    <a href="tel:{{ $booking->phone }}">{{ $booking->phone }}</a>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($booking->message)
                                                    <span title="{{ $booking->message }}">
                                                        {{ Str::limit($booking->message, 30) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($booking->status === 'confirmed')
                                                    <span class="badge badge-success">Megerősítve</span>
                                                @elseif($booking->status === 'pending')
                                                    <span class="badge badge-warning">Függőben</span>
                                                @elseif($booking->status === 'cancelled')
                                                    <span class="badge badge-danger">Lemondva</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $booking->booking_date->format('Y.m.d H:i') }}</small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    @if($booking->status === 'pending')
                                                        <button type="button" 
                                                                class="btn btn-sm btn-success" 
                                                                title="Megerősítés"
                                                                onclick="confirmBooking({{ $booking->id }})">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    @endif

                                                    @if($booking->status !== 'cancelled')
                                                        <button type="button" 
                                                                class="btn btn-sm btn-danger" 
                                                                title="Lemondás"
                                                                onclick="cancelBooking({{ $booking->id }})">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    @endif

                                                    <button type="button" 
                                                            class="btn btn-sm btn-info" 
                                                            title="Részletek"
                                                            onclick="showBookingDetails({{ $booking->id }})">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
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
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Még nincsenek foglalások ehhez az időponthoz</h5>
                        <p class="text-muted">A foglalások automatikusan megjelennek itt, amint valaki lefoglalja az időpontot.</p>
                    </div>
                </div>
            @endif
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

<!-- Booking Details Modal -->
<div class="modal fade" id="bookingDetailsModal" tabindex="-1" role="dialog" aria-labelledby="bookingDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bookingDetailsModalLabel">Foglalás részletei</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="bookingDetailsContent">
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

function confirmBooking(bookingId) {
    if (confirm('Biztosan megerősíti ezt a foglalást?')) {
        $.ajax({
            url: '/timebooking/bookings/' + bookingId + '/confirm',
            method: 'PATCH',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                location.reload();
            },
            error: function() {
                alert('Hiba történt a foglalás megerősítése során.');
            }
        });
    }
}

function cancelBooking(bookingId) {
    if (confirm('Biztosan lemondja ezt a foglalást?')) {
        $.ajax({
            url: '/timebooking/bookings/' + bookingId + '/cancel',
            method: 'PATCH',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                location.reload();
            },
            error: function() {
                alert('Hiba történt a foglalás lemondása során.');
            }
        });
    }
}

function showBookingDetails(bookingId) {
    $('#bookingDetailsModal').modal('show');

    // Reset content
    $('#bookingDetailsContent').html(`
        <div class="text-center">
            <div class="spinner-border" role="status">
                <span class="sr-only">Betöltés...</span>
            </div>
        </div>
    `);

    // Load booking details via AJAX
    $.get('/timebooking/bookings/' + bookingId)
        .done(function(response) {
            $('#bookingDetailsContent').html(response);
        })
        .fail(function() {
            $('#bookingDetailsContent').html('<div class="alert alert-danger">Hiba történt a foglalás betöltése során.</div>');
        });
}

function exportBookings(appointmentId) {
    window.location.href = '/timebooking/appointments/' + appointmentId + '/bookings/export';
}
</script>
@endpush
