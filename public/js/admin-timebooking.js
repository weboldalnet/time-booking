/**
 * Admin TimeBooking JavaScript
 * Handles admin functionality for the timebooking module
 * Requires jQuery 3.5.1 and Bootstrap 4.6
 */

$(document).ready(function() {
    'use strict';

    // Initialize the admin timebooking functionality
    AdminTimeBooking.init();
});

var AdminTimeBooking = {

    /**
     * Initialize the admin timebooking functionality
     */
    init: function() {
        this.bindEvents();
        this.initializeDataTables();
        this.initializeTooltips();
        this.initializeDatePickers();
        this.initializeFormValidation();
    },

    /**
     * Bind event handlers
     */
    bindEvents: function() {
        // Delete confirmation modals
        $(document).on('click', '[data-toggle="delete-confirm"]', this.showDeleteConfirmation);

        // Booking details modal
        $(document).on('click', '.show-bookings-btn', this.showBookingsModal);

        // Category status toggle
        $(document).on('click', '.toggle-status-btn', this.toggleCategoryStatus);

        // Bulk appointment creation
        $('#bulkCreateForm').on('submit', this.handleBulkCreate);

        // Form enhancements
        $('.select2').select2();

        // Auto-refresh for real-time updates
        this.startAutoRefresh();

        // Export functionality
        $(document).on('click', '.export-btn', this.handleExport);

        // Filter form submission
        $('.filter-form').on('submit', this.handleFilterSubmission);

        // Clear filters
        $(document).on('click', '.clear-filters-btn', this.clearFilters);

        // Sortable categories
        this.initializeSortable();
    },

    /**
     * Initialize DataTables for listings
     */
    initializeDataTables: function() {
        if ($('.data-table').length) {
            $('.data-table').DataTable({
                responsive: true,
                pageLength: 25,
                order: [[0, 'desc']],
                language: {
                    url: '/assets/datatables/hu.json'
                },
                columnDefs: [
                    {
                        targets: 'no-sort',
                        orderable: false
                    }
                ]
            });
        }
    },

    /**
     * Initialize Bootstrap tooltips
     */
    initializeTooltips: function() {
        $('[data-toggle="tooltip"]').tooltip();
    },

    /**
     * Initialize date pickers
     */
    initializeDatePickers: function() {
        if ($('.datepicker').length) {
            $('.datepicker').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true,
                language: 'hu'
            });
        }

        if ($('.datetimepicker').length) {
            $('.datetimepicker').datetimepicker({
                format: 'YYYY-MM-DD HH:mm',
                locale: 'hu',
                sideBySide: true
            });
        }
    },

    /**
     * Initialize form validation
     */
    initializeFormValidation: function() {
        // Custom validation rules
        $.validator.addMethod('futureDate', function(value, element) {
            return this.optional(element) || new Date(value) > new Date();
        }, 'A dátumnak jövőbeli időpontnak kell lennie.');

        $.validator.addMethod('endAfterStart', function(value, element) {
            var startTime = $('#start_time').val();
            return this.optional(element) || !startTime || new Date(value) > new Date(startTime);
        }, 'A befejezés időpontja a kezdés után kell legyen.');

        // Apply validation to forms
        $('.needs-validation').validate({
            errorClass: 'is-invalid',
            validClass: 'is-valid',
            errorElement: 'div',
            errorPlacement: function(error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function(element) {
                $(element).addClass('is-invalid').removeClass('is-valid');
            },
            unhighlight: function(element) {
                $(element).removeClass('is-invalid').addClass('is-valid');
            }
        });
    },

    /**
     * Show delete confirmation modal
     */
    showDeleteConfirmation: function(e) {
        e.preventDefault();

        var $btn = $(this);
        var itemName = $btn.data('item-name');
        var deleteUrl = $btn.data('delete-url');

        $('#deleteModal').find('#itemName').text(itemName);
        $('#deleteModal').find('#deleteForm').attr('action', deleteUrl);
        $('#deleteModal').modal('show');
    },

    /**
     * Show bookings modal with appointment details
     */
    showBookingsModal: function(e) {
        e.preventDefault();

        var $btn = $(this);
        var appointmentId = $btn.data('appointment-id');

        $('#bookingsModal').modal('show');

        // Reset content
        $('#bookingsContent').html(`
            <div class="text-center py-4">
                <div class="spinner-border" role="status">
                    <span class="sr-only">Betöltés...</span>
                </div>
            </div>
        `);

        // Load bookings via AJAX
        $.get('/timebooking/appointments/' + appointmentId + '/bookings')
            .done(function(response) {
                AdminTimeBooking.renderBookingsContent(response);
            })
            .fail(function() {
                $('#bookingsContent').html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Hiba történt a foglalások betöltése során.
                    </div>
                `);
            });
    },

    /**
     * Render bookings content in modal
     */
    renderBookingsContent: function(response) {
        var content = `
            <div class="appointment-info mb-4">
                <h6>${response.appointment.title}</h6>
                <p class="text-muted mb-0">
                    <i class="fas fa-calendar mr-2"></i>
                    ${response.appointment.start_time} - ${response.appointment.end_time}
                </p>
            </div>
        `;

        if (response.bookings && response.bookings.length > 0) {
            content += `
                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead>
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
            `;

            response.bookings.forEach(function(booking) {
                var statusBadge = AdminTimeBooking.getStatusBadge(booking.status);
                var actions = AdminTimeBooking.getBookingActions(booking);

                content += `
                    <tr>
                        <td><strong>${booking.name}</strong></td>
                        <td>
                            <a href="mailto:${booking.email}">${booking.email}</a>
                        </td>
                        <td>
                            ${booking.phone ? `<a href="tel:${booking.phone}">${booking.phone}</a>` : '-'}
                        </td>
                        <td>
                            ${booking.message ? `<span title="${booking.message}">${AdminTimeBooking.truncate(booking.message, 30)}</span>` : '-'}
                        </td>
                        <td>${statusBadge}</td>
                        <td>
                            <small class="text-muted">${booking.booking_date}</small>
                        </td>
                        <td>${actions}</td>
                    </tr>
                `;
            });

            content += `
                        </tbody>
                    </table>
                </div>
            `;
        } else {
            content += `
                <div class="text-center py-4">
                    <i class="fas fa-calendar-times fa-2x text-muted mb-3"></i>
                    <p class="text-muted">Még nincsenek foglalások ehhez az időponthoz.</p>
                </div>
            `;
        }

        $('#bookingsContent').html(content);
    },

    /**
     * Get status badge HTML
     */
    getStatusBadge: function(status) {
        switch(status) {
            case 'confirmed':
                return '<span class="badge badge-success">Megerősítve</span>';
            case 'pending':
                return '<span class="badge badge-warning">Függőben</span>';
            case 'cancelled':
                return '<span class="badge badge-danger">Lemondva</span>';
            default:
                return '<span class="badge badge-secondary">Ismeretlen</span>';
        }
    },

    /**
     * Get booking actions HTML
     */
    getBookingActions: function(booking) {
        var actions = '<div class="btn-group" role="group">';

        if (booking.status === 'pending') {
            actions += `
                <button type="button" class="btn btn-sm btn-success confirm-booking-btn"
                        data-booking-id="${booking.id}" title="Megerősítés">
                    <i class="fas fa-check"></i>
                </button>
            `;
        }

        if (booking.status !== 'cancelled') {
            actions += `
                <button type="button" class="btn btn-sm btn-danger cancel-booking-btn"
                        data-booking-id="${booking.id}" title="Lemondás">
                    <i class="fas fa-times"></i>
                </button>
            `;
        }

        actions += `
            <button type="button" class="btn btn-sm btn-info view-booking-btn"
                    data-booking-id="${booking.id}" title="Részletek">
                <i class="fas fa-eye"></i>
            </button>
        `;

        actions += '</div>';
        return actions;
    },

    /**
     * Toggle category status
     */
    toggleCategoryStatus: function(e) {
        e.preventDefault();

        var $btn = $(this);
        var categoryId = $btn.data('category-id');
        var currentStatus = $btn.data('current-status');

        $.ajax({
            url: '/timebooking/categories/' + categoryId + '/toggle-status',
            method: 'PATCH',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                AdminTimeBooking.showAlert(response.message, 'success');

                // Update button appearance
                if (currentStatus) {
                    $btn.removeClass('btn-secondary').addClass('btn-success')
                        .find('i').removeClass('fa-pause').addClass('fa-play');
                    $btn.data('current-status', false);
                } else {
                    $btn.removeClass('btn-success').addClass('btn-secondary')
                        .find('i').removeClass('fa-play').addClass('fa-pause');
                    $btn.data('current-status', true);
                }

                // Refresh page after 2 seconds
                setTimeout(function() {
                    window.location.reload();
                }, 2000);
            },
            error: function() {
                AdminTimeBooking.showAlert('Hiba történt a státusz módosítása során.', 'danger');
            }
        });
    },

    /**
     * Handle bulk appointment creation
     */
    handleBulkCreate: function(e) {
        e.preventDefault();

        var $form = $(this);
        var $submitBtn = $form.find('button[type="submit"]');

        // Validate form
        if (!$form.valid()) {
            return false;
        }

        // Show loading state
        $submitBtn.prop('disabled', true);
        var originalText = $submitBtn.html();
        $submitBtn.html('<i class="fas fa-spinner fa-spin mr-2"></i>Létrehozás...');

        // Submit form
        $.ajax({
            url: $form.attr('action'),
            method: 'POST',
            data: $form.serialize(),
            success: function(response) {
                AdminTimeBooking.showAlert(response.message, 'success');

                // Redirect after 2 seconds
                setTimeout(function() {
                    window.location.href = '/timebooking/appointments';
                }, 2000);
            },
            error: function(xhr) {
                var response = xhr.responseJSON;
                var message = response && response.message ? response.message : 'Hiba történt a létrehozás során.';

                AdminTimeBooking.showAlert(message, 'danger');

                // Show validation errors
                if (response && response.errors) {
                    AdminTimeBooking.showValidationErrors(response.errors);
                }
            },
            complete: function() {
                // Reset button
                $submitBtn.prop('disabled', false);
                $submitBtn.html(originalText);
            }
        });

        return false;
    },

    /**
     * Start auto-refresh for real-time updates
     */
    startAutoRefresh: function() {
        // Refresh every 5 minutes
        setInterval(function() {
            if ($('.auto-refresh').length) {
                AdminTimeBooking.refreshData();
            }
        }, 300000);
    },

    /**
     * Refresh data without page reload
     */
    refreshData: function() {
        $('.auto-refresh').each(function() {
            var $element = $(this);
            var refreshUrl = $element.data('refresh-url');

            if (refreshUrl) {
                $.get(refreshUrl)
                    .done(function(response) {
                        $element.html(response);
                    });
            }
        });
    },

    /**
     * Handle export functionality
     */
    handleExport: function(e) {
        e.preventDefault();

        var $btn = $(this);
        var exportUrl = $btn.attr('href');
        var format = $btn.data('format');

        // Show loading state
        $btn.prop('disabled', true);
        var originalText = $btn.html();
        $btn.html('<i class="fas fa-spinner fa-spin mr-2"></i>Exportálás...');

        // Create hidden form for export
        var $form = $('<form>', {
            method: 'POST',
            action: exportUrl,
            style: 'display: none;'
        });

        $form.append($('<input>', {
            type: 'hidden',
            name: '_token',
            value: $('meta[name="csrf-token"]').attr('content')
        }));

        $form.append($('<input>', {
            type: 'hidden',
            name: 'format',
            value: format
        }));

        // Add current filters
        $('.filter-form input, .filter-form select').each(function() {
            if ($(this).val()) {
                $form.append($('<input>', {
                    type: 'hidden',
                    name: $(this).attr('name'),
                    value: $(this).val()
                }));
            }
        });

        $('body').append($form);
        $form.submit();
        $form.remove();

        // Reset button after 3 seconds
        setTimeout(function() {
            $btn.prop('disabled', false);
            $btn.html(originalText);
        }, 3000);
    },

    /**
     * Handle filter form submission
     */
    handleFilterSubmission: function(e) {
        e.preventDefault();

        var $form = $(this);
        var formData = $form.serialize();
        var currentUrl = window.location.pathname;

        // Update URL with filters
        window.location.href = currentUrl + '?' + formData;
    },

    /**
     * Clear all filters
     */
    clearFilters: function(e) {
        e.preventDefault();

        $('.filter-form')[0].reset();
        $('.filter-form select').val('').trigger('change');

        // Redirect to clean URL
        window.location.href = window.location.pathname;
    },

    /**
     * Initialize sortable functionality
     */
    initializeSortable: function() {
        if ($('.sortable-list').length) {
            $('.sortable-list').sortable({
                handle: '.sort-handle',
                placeholder: 'sort-placeholder',
                update: function(event, ui) {
                    var sortData = [];

                    $(this).children().each(function(index) {
                        sortData.push({
                            id: $(this).data('id'),
                            sort_order: index
                        });
                    });

                    // Save new order
                    $.ajax({
                        url: '/timebooking/categories/update-order',
                        method: 'PATCH',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            categories: sortData
                        },
                        success: function(response) {
                            AdminTimeBooking.showAlert(response.message, 'success');
                        },
                        error: function() {
                            AdminTimeBooking.showAlert('Hiba történt a sorrend mentése során.', 'danger');
                        }
                    });
                }
            });
        }
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

        // Insert alert at the top of the main content
        $('.main-content').prepend(alertHtml);

        // Auto-dismiss after 5 seconds
        setTimeout(function() {
            $('.alert').alert('close');
        }, 5000);

        // Scroll to top to show alert
        $('html, body').animate({
            scrollTop: 0
        }, 500);
    },

    /**
     * Show validation errors
     */
    showValidationErrors: function(errors) {
        $.each(errors, function(field, messages) {
            var $field = $('[name="' + field + '"]');
            if ($field.length) {
                $field.addClass('is-invalid');
                $field.closest('.form-group').find('.invalid-feedback').remove();
                $field.after('<div class="invalid-feedback">' + messages[0] + '</div>');
            }
        });
    },

    /**
     * Truncate text to specified length
     */
    truncate: function(text, length) {
        if (text.length <= length) {
            return text;
        }
        return text.substring(0, length) + '...';
    },

    /**
     * Format date for display
     */
    formatDate: function(dateString) {
        var date = new Date(dateString);
        return date.toLocaleDateString('hu-HU', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit'
        });
    },

    /**
     * Confirm action with custom message
     */
    confirmAction: function(message, callback) {
        if (confirm(message)) {
            callback();
        }
    }
};

// Additional utility functions
$(window).on('load', function() {
    // Hide loading overlay if present
    $('.loading-overlay').fadeOut();

    // Initialize any additional components
    AdminTimeBooking.initializeCharts();
});

// Extend AdminTimeBooking with chart functionality
AdminTimeBooking.initializeCharts = function() {
    // Initialize charts if Chart.js is available
    if (typeof Chart !== 'undefined') {
        // Booking statistics chart
        var bookingStatsCtx = document.getElementById('bookingStatsChart');
        if (bookingStatsCtx) {
            new Chart(bookingStatsCtx, {
                type: 'line',
                data: {
                    labels: [], // Will be populated from data attributes
                    datasets: [{
                        label: 'Foglalások',
                        data: [], // Will be populated from data attributes
                        borderColor: '#007bff',
                        backgroundColor: 'rgba(0, 123, 255, 0.1)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    }
};

// Handle browser back/forward buttons
$(window).on('popstate', function() {
    // Refresh page content if needed
    if ($('.ajax-content').length) {
        window.location.reload();
    }
});
