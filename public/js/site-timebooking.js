/**
 * Site TimeBooking JavaScript
 * Handles booking functionality for the public site
 * Requires jQuery 3.5.1 and Bootstrap 4.6
 */

$(document).ready(function() {
    'use strict';

    // Initialize the timebooking functionality
    TimeBooking.init();
});

var TimeBooking = {
    
    /**
     * Initialize the timebooking functionality
     */
    init: function() {
        this.bindEvents();
        this.initializeTooltips();
        this.checkAvailability();
    },

    /**
     * Bind event handlers
     */
    bindEvents: function() {
        // Book appointment button click
        $(document).on('click', '.book-appointment-btn', this.openBookingModal);
        
        // Booking form submission
        $('#bookingForm').on('submit', this.handleBookingSubmission);
        
        // Category tab change
        $('#categoryTabs a[data-toggle="pill"]').on('shown.bs.tab', this.onCategoryChange);
        
        // Modal events
        $('#bookingModal').on('hidden.bs.modal', this.resetBookingForm);
        $('#bookingModal').on('shown.bs.modal', this.focusFirstInput);
        
        // Form validation
        $('#bookingForm input, #bookingForm textarea').on('blur', this.validateField);
        
        // Phone number formatting
        $('#phone').on('input', this.formatPhoneNumber);
        
        // Real-time availability check
        setInterval(this.checkAvailability, 30000); // Check every 30 seconds
    },

    /**
     * Initialize Bootstrap tooltips
     */
    initializeTooltips: function() {
        $('[data-toggle="tooltip"]').tooltip();
    },

    /**
     * Open booking modal with appointment details
     */
    openBookingModal: function(e) {
        e.preventDefault();
        
        var $btn = $(this);
        var appointmentId = $btn.data('appointment-id');
        var appointmentTitle = $btn.data('appointment-title');
        var appointmentDate = $btn.data('appointment-date');
        var appointmentTime = $btn.data('appointment-time');
        
        // Check availability before opening modal
        TimeBooking.checkAppointmentAvailability(appointmentId, function(available) {
            if (!available) {
                TimeBooking.showAlert('Ez az időpont már nem elérhető foglalásra.', 'danger');
                return;
            }
            
            // Populate modal with appointment details
            $('#appointmentId').val(appointmentId);
            $('#selectedTitle').text(appointmentTitle);
            $('#selectedDate').text(appointmentDate);
            $('#selectedTime').text(appointmentTime);
            
            // Show modal
            $('#bookingModal').modal('show');
        });
    },

    /**
     * Handle booking form submission
     */
    handleBookingSubmission: function(e) {
        e.preventDefault();
        
        var $form = $(this);
        var $submitBtn = $form.find('button[type="submit"]');
        
        // Validate form
        if (!TimeBooking.validateForm($form)) {
            return false;
        }
        
        // Disable submit button and show loading
        $submitBtn.prop('disabled', true);
        var originalText = $submitBtn.html();
        $submitBtn.html('<i class="fas fa-spinner fa-spin mr-2"></i>Foglalás leadása...');
        
        // Submit form via AJAX
        $.ajax({
            url: $form.attr('action'),
            method: 'POST',
            data: $form.serialize(),
            success: function(response) {
                // Redirect to thank you page
                window.location.href = response.redirect || '/idopontfoglalas/koszonjuk';
            },
            error: function(xhr) {
                var response = xhr.responseJSON;
                var message = response && response.message ? response.message : 'Hiba történt a foglalás során.';
                
                TimeBooking.showAlert(message, 'danger');
                
                // Show validation errors
                if (response && response.errors) {
                    TimeBooking.showValidationErrors(response.errors);
                }
            },
            complete: function() {
                // Re-enable submit button
                $submitBtn.prop('disabled', false);
                $submitBtn.html(originalText);
            }
        });
        
        return false;
    },

    /**
     * Handle category tab change
     */
    onCategoryChange: function(e) {
        var categoryId = $(e.target).data('category-id');
        
        // Optional: Load appointments dynamically
        // TimeBooking.loadCategoryAppointments(categoryId);
        
        // Update URL hash
        window.location.hash = 'category-' + categoryId;
    },

    /**
     * Reset booking form
     */
    resetBookingForm: function() {
        $('#bookingForm')[0].reset();
        $('#bookingForm .is-invalid').removeClass('is-invalid');
        $('#bookingForm .invalid-feedback').remove();
    },

    /**
     * Focus first input when modal is shown
     */
    focusFirstInput: function() {
        setTimeout(function() {
            $('#name').focus();
        }, 500);
    },

    /**
     * Validate individual form field
     */
    validateField: function() {
        var $field = $(this);
        var value = $field.val().trim();
        var isValid = true;
        var errorMessage = '';
        
        // Remove existing validation
        $field.removeClass('is-invalid');
        $field.siblings('.invalid-feedback').remove();
        
        // Required field validation
        if ($field.prop('required') && !value) {
            isValid = false;
            errorMessage = 'Ez a mező kötelező.';
        }
        
        // Email validation
        if ($field.attr('type') === 'email' && value) {
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                isValid = false;
                errorMessage = 'Kérjük, adjon meg egy érvényes e-mail címet.';
            }
        }
        
        // Phone validation
        if ($field.attr('name') === 'phone' && value) {
            var phoneRegex = /^[\+]?[0-9\s\-\(\)]{8,}$/;
            if (!phoneRegex.test(value)) {
                isValid = false;
                errorMessage = 'Kérjük, adjon meg egy érvényes telefonszámot.';
            }
        }
        
        // Show validation error
        if (!isValid) {
            $field.addClass('is-invalid');
            $field.after('<div class="invalid-feedback">' + errorMessage + '</div>');
        }
        
        return isValid;
    },

    /**
     * Validate entire form
     */
    validateForm: function($form) {
        var isValid = true;
        
        // Validate all required fields
        $form.find('[required]').each(function() {
            if (!TimeBooking.validateField.call(this)) {
                isValid = false;
            }
        });
        
        // Validate privacy checkbox
        if (!$('#privacy').is(':checked')) {
            TimeBooking.showAlert('Kérjük, fogadja el az adatvédelmi szabályzatot.', 'warning');
            isValid = false;
        }
        
        return isValid;
    },

    /**
     * Format phone number input
     */
    formatPhoneNumber: function() {
        var value = $(this).val().replace(/\D/g, '');
        var formattedValue = '';
        
        if (value.startsWith('36')) {
            // Hungarian format: +36 XX XXX XXXX
            formattedValue = '+36 ' + value.substring(2, 4) + ' ' + 
                           value.substring(4, 7) + ' ' + value.substring(7, 11);
        } else if (value.length > 0) {
            // Default format with spaces
            formattedValue = value.replace(/(\d{2})(\d{3})(\d{4})/, '$1 $2 $3');
        }
        
        $(this).val(formattedValue.trim());
    },

    /**
     * Check appointment availability
     */
    checkAppointmentAvailability: function(appointmentId, callback) {
        $.get('/idopontfoglalas/appointment/' + appointmentId + '/availability')
            .done(function(response) {
                callback(response.available);
            })
            .fail(function() {
                callback(false);
            });
    },

    /**
     * Check availability for all visible appointments
     */
    checkAvailability: function() {
        $('.book-appointment-btn').each(function() {
            var $btn = $(this);
            var appointmentId = $btn.data('appointment-id');
            
            TimeBooking.checkAppointmentAvailability(appointmentId, function(available) {
                if (!available) {
                    var $card = $btn.closest('.appointment-card');
                    $card.addClass('card-disabled');
                    $btn.prop('disabled', true)
                        .removeClass('btn-primary')
                        .addClass('btn-secondary')
                        .html('<i class="fas fa-times mr-2"></i>Betelt');
                }
            });
        });
    },

    /**
     * Load appointments for a category (optional AJAX loading)
     */
    loadCategoryAppointments: function(categoryId) {
        var $tabPane = $('#category-' + categoryId);
        var $appointmentsGrid = $tabPane.find('.appointments-grid');
        
        // Show loading spinner
        $appointmentsGrid.html('<div class="text-center py-4"><div class="spinner-border" role="status"></div></div>');
        
        $.get('/idopontfoglalas/category/' + categoryId + '/appointments')
            .done(function(response) {
                if (response.success && response.appointments.length > 0) {
                    var html = '<div class="row">';
                    
                    response.appointments.forEach(function(appointment) {
                        html += TimeBooking.generateAppointmentCard(appointment);
                    });
                    
                    html += '</div>';
                    $appointmentsGrid.html(html);
                } else {
                    $appointmentsGrid.html(
                        '<div class="no-appointments text-center py-5">' +
                        '<i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>' +
                        '<h5 class="text-muted">Jelenleg nincsenek elérhető időpontok</h5>' +
                        '</div>'
                    );
                }
            })
            .fail(function() {
                $appointmentsGrid.html(
                    '<div class="alert alert-danger">Hiba történt az időpontok betöltése során.</div>'
                );
            });
    },

    /**
     * Generate appointment card HTML
     */
    generateAppointmentCard: function(appointment) {
        var isDisabled = appointment.available_capacity <= 0;
        var cardClass = isDisabled ? 'card-disabled' : '';
        var btnClass = isDisabled ? 'btn-secondary' : 'btn-primary';
        var btnText = isDisabled ? '<i class="fas fa-times mr-2"></i>Betelt' : '<i class="fas fa-calendar-check mr-2"></i>Foglalás';
        var btnDisabled = isDisabled ? 'disabled' : '';
        
        return `
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="appointment-card card h-100 ${cardClass}">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">${appointment.title}</h5>
                        ${appointment.description ? `<p class="card-text text-muted">${appointment.description}</p>` : ''}
                        <div class="appointment-details mt-auto">
                            <div class="appointment-time mb-2">
                                <i class="fas fa-calendar text-primary mr-2"></i>
                                <strong>${appointment.formatted_date}</strong>
                            </div>
                            <div class="appointment-duration mb-2">
                                <i class="fas fa-clock text-primary mr-2"></i>
                                ${appointment.formatted_time}
                            </div>
                            <div class="appointment-capacity mb-3">
                                <i class="fas fa-users text-primary mr-2"></i>
                                ${isDisabled ? '<span class="text-danger">Betelt</span>' : `<span class="text-success">${appointment.available_capacity} szabad hely</span>`}
                            </div>
                            <button type="button" 
                                    class="btn ${btnClass} btn-block book-appointment-btn"
                                    data-appointment-id="${appointment.id}"
                                    data-appointment-title="${appointment.title}"
                                    data-appointment-date="${appointment.formatted_date}"
                                    data-appointment-time="${appointment.formatted_time}"
                                    ${btnDisabled}>
                                ${btnText}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    },

    /**
     * Show alert message
     */
    showAlert: function(message, type) {
        type = type || 'info';
        
        var alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `;
        
        // Insert alert at the top of the container
        $('.timebooking-container').prepend(alertHtml);
        
        // Auto-dismiss after 5 seconds
        setTimeout(function() {
            $('.alert').alert('close');
        }, 5000);
        
        // Scroll to top to show alert
        $('html, body').animate({
            scrollTop: $('.timebooking-container').offset().top - 20
        }, 500);
    },

    /**
     * Show validation errors
     */
    showValidationErrors: function(errors) {
        $.each(errors, function(field, messages) {
            var $field = $('#' + field);
            if ($field.length) {
                $field.addClass('is-invalid');
                $field.siblings('.invalid-feedback').remove();
                $field.after('<div class="invalid-feedback">' + messages[0] + '</div>');
            }
        });
    }
};

// Utility functions
$(window).on('load', function() {
    // Handle URL hash for direct category linking
    if (window.location.hash) {
        var hash = window.location.hash.substring(1);
        if (hash.startsWith('category-')) {
            var categoryId = hash.replace('category-', '');
            $('#category-' + categoryId + '-tab').tab('show');
        }
    }
});

// Handle browser back/forward buttons
$(window).on('hashchange', function() {
    if (window.location.hash) {
        var hash = window.location.hash.substring(1);
        if (hash.startsWith('category-')) {
            var categoryId = hash.replace('category-', '');
            $('#category-' + categoryId + '-tab').tab('show');
        }
    }
});