/**
 * Account Settings V2 Modular JavaScript Asset
 * 
 * Intent: Handles async AJAX tab loading, boost keyword status toggling,
 * dynamic stage additions, and modal form submissions using clean event delegation.
 * 
 * Author: Antigravity AI
 * Date: August 12, 2026
 */
(function ($) {
    'use strict';

    var AccountV2 = {
        init: function () {
            this.bindEvents();
            this.initDefaultTab();
        },

        initDefaultTab: function () {
            // Check URL hash or default to overview tab
            var hash = window.location.hash.replace('#', '');
            if (hash && $('#' + hash).length) {
                this.loadTab(hash);
            }
        },

        bindEvents: function () {
            var self = this;

            // Delegated click handler for tab navigation links
            $(document).on('click', '[data-bs-toggle="tab"], .nav-link[href^="#"]', function (e) {
                var href = $(this).attr('href');
                if (href && href.indexOf('#') === 0) {
                    var tabName = href.replace('#', '');
                    self.loadTab(tabName);
                }
            });

            // Boost keyword status toggle handler
            $(document).on('change', '.boost-status-switch', function () {
                self.handleBoostStatusToggle(this);
            });

            // Add interview sub-stage handler
            $(document).on('click', '#ltb_submit', function (e) {
                e.preventDefault();
                self.handleAddInterviewStage();
            });
        },

        loadTab: function (tabName) {
            var $container = $('#' + tabName);
            if (!$container.length) return;

            // If already loaded or currently loading, skip refetching
            if ($container.data('loaded') === true || $container.data('loading') === true) {
                return;
            }

            $container.data('loading', true);
            var $spinner = $('<div class="text-center py-10"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');
            $container.html($spinner);

            var url = '/service_company/account/tab/' + encodeURIComponent(tabName);

            $.ajax({
                type: 'GET',
                url: url,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    $container.data('loading', false);
                    if (response.status === 'success' && response.html) {
                        $container.html(response.html);
                        $container.data('loaded', true);
                    } else {
                        $container.html('<div class="alert alert-danger">Failed to load tab content.</div>');
                    }
                },
                error: function () {
                    $container.data('loading', false);
                    $container.html('<div class="alert alert-danger">Error fetching tab content. Please try again.</div>');
                }
            });
        },

        handleBoostStatusToggle: function (element) {
            var chkStatus = element.checked;
            var userId = $(element).data('id');
            var $statusText = $(element).parents('td').find('.status_text');

            if (chkStatus) {
                $statusText.text('Active').removeClass('text-danger').addClass('text-success');
            } else {
                $statusText.text('Inactive').removeClass('text-success').addClass('text-danger');
            }

            $.ajax({
                type: 'POST',
                url: '/service_company/boostStatusUpdate',
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'chkStatus': chkStatus,
                    'userId': userId
                },
                success: function (response) {
                    if (response.status === 'success' && typeof fetchTeams === 'function') {
                        fetchTeams();
                    }
                }
            });
        },

        handleAddInterviewStage: function () {
            var ltb_stage = $('#ltb_stage').val();
            var ltb_root = $('#ltb_root').val();
            var ltb_stage_name = $('#ltb_stage_name').val();
            var ltb_class = $('#ltb_class').val();
            var token = $('meta[name="csrf-token"]').attr('content');

            $.ajax({
                type: 'POST',
                url: '/service_company/account/addIntStage',
                data: {
                    '_token': token,
                    'ltb_stage': ltb_stage,
                    'ltb_stage_name': ltb_stage_name,
                    'ltb_class': ltb_class
                },
                beforeSend: function () {
                    $('#add_interview_sub_stage_form').hide();
                    $('#add_interview_sub_stage_form_loader').show().html('<div class="spinner-border text-primary" role="status"></div>');
                },
                success: function (response) {
                    if (response.status === 'success') {
                        if (ltb_root) {
                            $('#' + ltb_root).append(response.stageHtml);
                        }
                        $('#add_interview_sub_stage_form_loader').html(response.message || 'Stage added successfully.');
                    }
                }
            });
        }
    };

    $(document).ready(function () {
        AccountV2.init();
    });

})(jQuery);
