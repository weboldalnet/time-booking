@extends('site.layouts.app')

@section('title', 'Köszönjük a foglalását!')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="timebooking-thank-you">
                <div class="text-center mb-5">
                    <div class="success-icon mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
                    </div>
                    <h1 class="display-4 text-success">Köszönjük!</h1>
                    <p class="lead text-muted">Foglalása sikeresen rögzítve lett</p>
                </div>

                <div class="booking-details">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-calendar-check mr-2"></i>
                                Foglalás részletei
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-muted">Időpont információk</h6>
                                    <div class="mb-3">
                                        <strong>{{ $booking->appointment->title }}</strong>
                                        @if($booking->appointment->description)
                                            <br><small class="text-muted">{{ $booking->appointment->description }}</small>
                                        @endif
                                    </div>
                                    
                                    <div class="mb-2">
                                        <i class="fas fa-tag text-primary mr-2"></i>
                                        <strong>Kategória:</strong> {{ $booking->appointment->category->name }}
                                    </div>
                                    
                                    <div class="mb-2">
                                        <i class="fas fa-calendar text-primary mr-2"></i>
                                        <strong>Dátum:</strong> {{ $booking->appointment->start_time->format('Y. m. d.') }}
                                    </div>
                                    
                                    <div class="mb-2">
                                        <i class="fas fa-clock text-primary mr-2"></i>
                                        <strong>Időpont:</strong> 
                                        {{ $booking->appointment->start_time->format('H:i') }} - {{ $booking->appointment->end_time->format('H:i') }}
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <h6 class="text-muted">Személyes adatok</h6>
                                    
                                    <div class="mb-2">
                                        <i class="fas fa-user text-primary mr-2"></i>
                                        <strong>Név:</strong> {{ $booking->name }}
                                    </div>
                                    
                                    <div class="mb-2">
                                        <i class="fas fa-envelope text-primary mr-2"></i>
                                        <strong>E-mail:</strong> {{ $booking->email }}
                                    </div>
                                    
                                    @if($booking->phone)
                                        <div class="mb-2">
                                            <i class="fas fa-phone text-primary mr-2"></i>
                                            <strong>Telefon:</strong> {{ $booking->phone }}
                                        </div>
                                    @endif
                                    
                                    @if($booking->message)
                                        <div class="mb-2">
                                            <i class="fas fa-comment text-primary mr-2"></i>
                                            <strong>Üzenet:</strong><br>
                                            <small class="text-muted">{{ $booking->message }}</small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <hr>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <i class="fas fa-info-circle text-primary mr-2"></i>
                                        <strong>Foglalás azonosító:</strong> #{{ $booking->id }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <i class="fas fa-calendar-plus text-primary mr-2"></i>
                                        <strong>Foglalás dátuma:</strong> {{ $booking->booking_date->format('Y. m. d. H:i') }}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="status-badge mt-3">
                                @if($booking->status === 'confirmed')
                                    <span class="badge badge-success badge-lg">
                                        <i class="fas fa-check mr-1"></i>
                                        Megerősítve
                                    </span>
                                @elseif($booking->status === 'pending')
                                    <span class="badge badge-warning badge-lg">
                                        <i class="fas fa-clock mr-1"></i>
                                        Függőben
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="important-info mt-4">
                    <div class="alert alert-info">
                        <h6 class="alert-heading">
                            <i class="fas fa-info-circle mr-2"></i>
                            Fontos információk
                        </h6>
                        <ul class="mb-0">
                            <li>Foglalása megerősítve lett, kérjük, érkezzen pontosan a megadott időpontban.</li>
                            <li>Amennyiben változtatni szeretne a foglaláson, kérjük, vegye fel velünk a kapcsolatot.</li>
                            <li>A foglalás részleteiről e-mailt küldtünk a megadott címre.</li>
                            <li>Kérjük, őrizze meg a foglalás azonosítóját: <strong>#{{ $booking->id }}</strong></li>
                        </ul>
                    </div>
                </div>

                <div class="action-buttons text-center mt-4">
                    <a href="{{ route('site.timebooking.index') }}" class="btn btn-primary btn-lg mr-3">
                        <i class="fas fa-calendar-alt mr-2"></i>
                        Új foglalás
                    </a>
                    <a href="{{ url('/') }}" class="btn btn-outline-secondary btn-lg">
                        <i class="fas fa-home mr-2"></i>
                        Főoldal
                    </a>
                </div>

                <div class="contact-info mt-5">
                    <div class="card">
                        <div class="card-body text-center">
                            <h6 class="card-title">Kérdése van?</h6>
                            <p class="card-text text-muted">
                                Ha bármilyen kérdése van a foglalással kapcsolatban, 
                                kérjük, vegye fel velünk a kapcsolatot.
                            </p>
                            <div class="contact-methods">
                                <a href="mailto:info@example.com" class="btn btn-outline-primary mr-2">
                                    <i class="fas fa-envelope mr-1"></i>
                                    E-mail
                                </a>
                                <a href="tel:+36301234567" class="btn btn-outline-primary">
                                    <i class="fas fa-phone mr-1"></i>
                                    Telefon
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('timebooking/css/site-timebooking.css') }}">
<style>
.badge-lg {
    font-size: 1rem;
    padding: 0.5rem 1rem;
}

.success-icon {
    animation: bounceIn 1s ease-in-out;
}

@keyframes bounceIn {
    0% {
        transform: scale(0.3);
        opacity: 0;
    }
    50% {
        transform: scale(1.05);
    }
    70% {
        transform: scale(0.9);
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

.contact-methods .btn {
    min-width: 120px;
}

@media (max-width: 768px) {
    .action-buttons .btn {
        display: block;
        width: 100%;
        margin-bottom: 1rem;
    }
    
    .contact-methods .btn {
        display: block;
        width: 100%;
        margin-bottom: 0.5rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-scroll to top
    $('html, body').animate({
        scrollTop: 0
    }, 500);
    
    // Add some interactive effects
    $('.card').hover(
        function() {
            $(this).addClass('shadow-lg').removeClass('shadow');
        },
        function() {
            $(this).removeClass('shadow-lg').addClass('shadow');
        }
    );
});
</script>
@endpush