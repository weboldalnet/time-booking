@extends('site.layouts.layout')

@section('title', 'Időpontfoglalás')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="timebooking-container">
                <div class="timebooking-header text-center mb-5">
                    <h1 class="display-4">Időpontfoglalás</h1>
                    <p class="lead text-muted">Válasszon kategóriát és időpontot a foglaláshoz</p>
                </div>

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
                    <!-- Category Tabs -->
                    <div class="timebooking-categories">
                        <ul class="nav nav-pills nav-fill mb-4" id="categoryTabs" role="tablist">
                            @foreach($categories as $index => $category)
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link {{ $index === 0 ? 'active' : '' }}" 
                                       id="category-{{ $category->id }}-tab" 
                                       data-toggle="pill" 
                                       href="#category-{{ $category->id }}" 
                                       role="tab" 
                                       aria-controls="category-{{ $category->id }}" 
                                       aria-selected="{{ $index === 0 ? 'true' : 'false' }}"
                                       data-category-id="{{ $category->id }}">
                                        <i class="fas fa-calendar-alt mr-2"></i>
                                        {{ $category->name }}
                                        <span class="badge badge-light ml-2">{{ $category->activeAppointments->count() }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>

                        <!-- Category Content -->
                        <div class="tab-content" id="categoryTabsContent">
                            @foreach($categories as $index => $category)
                                <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" 
                                     id="category-{{ $category->id }}" 
                                     role="tabpanel" 
                                     aria-labelledby="category-{{ $category->id }}-tab">
                                    
                                    @if($category->description)
                                        <div class="category-description mb-4">
                                            <div class="card">
                                                <div class="card-body">
                                                    <p class="card-text text-muted mb-0">{{ $category->description }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    @if($category->activeAppointments->count() > 0)
                                        <div class="appointments-grid">
                                            <div class="row">
                                                @foreach($category->activeAppointments as $appointment)
                                                    <div class="col-lg-4 col-md-6 mb-4">
                                                        <div class="appointment-card card h-100 {{ $appointment->available_capacity <= 0 ? 'card-disabled' : '' }}">
                                                            <div class="card-body d-flex flex-column">
                                                                <h5 class="card-title">{{ $appointment->title }}</h5>
                                                                
                                                                @if($appointment->description)
                                                                    <p class="card-text text-muted">{{ Str::limit($appointment->description, 100) }}</p>
                                                                @endif
                                                                
                                                                <div class="appointment-details mt-auto">
                                                                    <div class="appointment-time mb-2">
                                                                        <i class="fas fa-calendar text-primary mr-2"></i>
                                                                        <strong>{{ $appointment->start_time->format('Y. m. d.') }}</strong>
                                                                    </div>
                                                                    <div class="appointment-duration mb-2">
                                                                        <i class="fas fa-clock text-primary mr-2"></i>
                                                                        {{ $appointment->start_time->format('H:i') }} - {{ $appointment->end_time->format('H:i') }}
                                                                    </div>
                                                                    <div class="appointment-capacity mb-3">
                                                                        <i class="fas fa-users text-primary mr-2"></i>
                                                                        @if($appointment->available_capacity > 0)
                                                                            <span class="text-success">{{ $appointment->available_capacity }} szabad hely</span>
                                                                        @else
                                                                            <span class="text-danger">Betelt</span>
                                                                        @endif
                                                                    </div>
                                                                    
                                                                    @if($appointment->available_capacity > 0)
                                                                        <button type="button" 
                                                                                class="btn btn-primary btn-block book-appointment-btn"
                                                                                data-appointment-id="{{ $appointment->id }}"
                                                                                data-appointment-title="{{ $appointment->title }}"
                                                                                data-appointment-date="{{ $appointment->start_time->format('Y. m. d.') }}"
                                                                                data-appointment-time="{{ $appointment->start_time->format('H:i') }} - {{ $appointment->end_time->format('H:i') }}">
                                                                            <i class="fas fa-calendar-check mr-2"></i>
                                                                            Foglalás
                                                                        </button>
                                                                    @else
                                                                        <button type="button" class="btn btn-secondary btn-block" disabled>
                                                                            <i class="fas fa-times mr-2"></i>
                                                                            Betelt
                                                                        </button>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        <div class="no-appointments text-center py-5">
                                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">Jelenleg nincsenek elérhető időpontok</h5>
                                            <p class="text-muted">Kérjük, próbálja meg később vagy válasszon másik kategóriát.</p>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="no-categories text-center py-5">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Jelenleg nincsenek elérhető kategóriák</h5>
                        <p class="text-muted">Kérjük, próbálja meg később.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Booking Modal -->
<div class="modal fade" id="bookingModal" tabindex="-1" role="dialog" aria-labelledby="bookingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bookingModalLabel">Időpont foglalása</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="bookingForm" method="POST" action="{{ route('site.timebooking.book') }}">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="appointment_id" id="appointmentId">
                    
                    <div class="appointment-summary mb-4">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">Kiválasztott időpont</h6>
                                <p class="card-text mb-1">
                                    <strong id="selectedTitle"></strong>
                                </p>
                                <p class="card-text mb-0">
                                    <i class="fas fa-calendar text-primary mr-2"></i>
                                    <span id="selectedDate"></span>
                                    <i class="fas fa-clock text-primary ml-3 mr-2"></i>
                                    <span id="selectedTime"></span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name" class="form-label">Név <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name') }}" 
                                       placeholder="Adja meg a nevét"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email" class="form-label">E-mail cím <span class="text-danger">*</span></label>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       placeholder="pelda@email.com"
                                       required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="phone" class="form-label">Telefonszám</label>
                        <input type="tel" 
                               class="form-control @error('phone') is-invalid @enderror" 
                               id="phone" 
                               name="phone" 
                               value="{{ old('phone') }}" 
                               placeholder="+36 30 123 4567">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="message" class="form-label">Üzenet</label>
                        <textarea class="form-control @error('message') is-invalid @enderror" 
                                  id="message" 
                                  name="message" 
                                  rows="3" 
                                  placeholder="További információk, kérések...">{{ old('message') }}</textarea>
                        @error('message')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="privacy" required>
                            <label class="custom-control-label" for="privacy">
                                Elfogadom az <a href="#" target="_blank">adatvédelmi szabályzatot</a> <span class="text-danger">*</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-2"></i>Mégse
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-calendar-check mr-2"></i>Foglalás leadása
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('timebooking/css/site-timebooking.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('timebooking/js/site-timebooking.js') }}"></script>
@endpush