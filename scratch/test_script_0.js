
    /**
     * Tab Manager: Scoped per jobId to prevent memory leaks and singleton state bugs.
     */
    var AgencyMarketplaceTabManager = {
        initializedTabs: {},
        activeRequests: {},
        searchTimers: {},

        init: function() {
            var jobId = $('#current_job_id').val();
            if (!jobId) return;
            this.initializedTabs[jobId] = this.initializedTabs[jobId] || {};
        },

        switchTab: function(tabKey) {
            var jobId = $('#current_job_id').val();
            if (!jobId) return;

            this.cancelAllRequests();
            this.initializedTabs[jobId] = this.initializedTabs[jobId] || {};

            if (tabKey === 'registered') {
                staffingPartnersRegister(1, '');
            } else if (tabKey === 'assigned') {
                recommendedAssignList(1, '');
            } else if (tabKey === 'recommended') {
                tableLoad(1);
            } else if (tabKey === 'all_agencies') {
                loadAllAgenciesTab(1);
            }
            this.initializedTabs[jobId][tabKey] = true;
        },

        cancelRequest: function(tabKey) {
            if (this.activeRequests[tabKey] && typeof this.activeRequests[tabKey].abort === 'function') {
                try { this.activeRequests[tabKey].abort(); } catch(e) {}
                delete this.activeRequests[tabKey];
            }
        },

        cancelAllRequests: function() {
            for (var key in this.activeRequests) {
                if (this.activeRequests.hasOwnProperty(key)) {
                    this.cancelRequest(key);
                }
            }
        },

        registerRequest: function(tabKey, jqXHR) {
            this.cancelRequest(tabKey);
            this.activeRequests[tabKey] = jqXHR;
        },

        clearRequest: function(tabKey) {
            delete this.activeRequests[tabKey];
        }
    };

    /**
     * Recommended Agencies Table Loader:
     * Two-Tier Safe Representation: Client RAM cache + _force=1 HTTP 304 Recovery
     */
    function tableLoad(page) {
        if (page === 'load' || !page) { page = 1; }

        $('#recommended_loader').show();

        var currentJobId = $('#current_job_id').val();
        var cusSearchVal = $('#agency_unified_search').val() || $('#kt_tagify_users').val();

        var jqXHR = $.ajax({
            url: `http://localhost/service_company/jobs/recommendedData?page=${page}`,
            type: 'GET',
            data: {
                jobid: currentJobId,
                hiringtype: $('#workhiringtype').val(),
                country: $('#country').val(),
                cus_search: cusSearchVal,
                min_exp: $('#min_exp').val(),
                max_exp: $('#max_exp').val(),
                app_int_outsource: $('#hiring_type').val(),
                experience: $('#experience').val(),
                industry: $('#industry').val(),
                min_sal: $('#min_sal').val(),
                max_sal: $('#max_sal').val(),
                min_salary_currency: $('#min_salary_currency').val(),
                sal_mode: $('#sal_mode').val()
            },
            success: function(response) {
                $('#recommended_loader').hide();

                var ramCacheKey = 'recom_table_' + currentJobId + '_' + page;
                window.agencyTableCache = window.agencyTableCache || {};

                if (response && response.html) {
                    window.agencyTableCache[ramCacheKey] = response;
                    $('#recommendedlistBody').html(response.html);
                    if (response.total !== undefined) {
                        $('#recommendedTotal').html('(' + response.total + ')');
                    }
                } else if (window.agencyTableCache[ramCacheKey]) {
                    var cached = window.agencyTableCache[ramCacheKey];
                    $('#recommendedlistBody').html(cached.html);
                    if (cached.total !== undefined) {
                        $('#recommendedTotal').html('(' + cached.total + ')');
                    }
                } else {
                    // Safe representation recovery: _force=1 guarantees fresh HTML from Redis
                    $.getJSON(`http://localhost/service_company/jobs/recommendedData?page=${page}&_force=1`, { jobid: currentJobId }, function(fresh) {
                        if (fresh && fresh.html) {
                            window.agencyTableCache[ramCacheKey] = fresh;
                            $('#recommendedlistBody').html(fresh.html);
                            if (fresh.total !== undefined) {
                                $('#recommendedTotal').html('(' + fresh.total + ')');
                            }
                        }
                    });
                }
            },
            error: function() {
                $('#recommended_loader').hide();
            },
            complete: function() {
                AgencyMarketplaceTabManager.clearRequest('recommended');
            }
        });
        AgencyMarketplaceTabManager.registerRequest('recommended', jqXHR);
        return jqXHR;
    }

    /**
     * All Agencies Loader
     */
    function loadAllAgenciesTab(page) {
        if (!page) { page = 1; }
        var currentJobId = $('#current_job_id').val();

        var jqXHR = $.ajax({
            url: `http://localhost/service_company/jobs/recommendedData?page=${page}`,
            type: 'GET',
            data: {
                jobid: currentJobId,
                cus_search: '' // Clear skills filter to display all verified agencies
            },
            success: function(response) {
                if (response && response.html) {
                    $('#allAgenciesListBody').html(response.html);
                    if (response.total !== undefined) {
                        $('#allAgenciesTotal').html('(' + response.total + ')');
                    }
                }
            },
            complete: function() {
                AgencyMarketplaceTabManager.clearRequest('all_agencies');
            }
        });
        AgencyMarketplaceTabManager.registerRequest('all_agencies', jqXHR);
        return jqXHR;
    }

    /**
     * My Agencies (Registered) Loader
     */
    function staffingPartnersRegister(page = 1, search = '') {
        if (page === 'load' || !page) { page = 1; }
        var jobid = $('#current_job_id').val();
        var cus_search = $('#agency_unified_search').val() || $('#kt_tagify_4').val();

        var jqXHR = $.ajax({
            url: `http://localhost/service_company/jobs/staffingPartnersRegister?page=${page}`,
            type: 'GET',
            data: {
                page: page,
                jobid: jobid,
                hiringtype: $('#hiringtype4').val(),
                country: $('#country4').val(),
                cus_search: cus_search
            },
            success: function(response) {
                if (response && response.html) {
                    $('#allregisteragency').html(response.html);
                    if (response.total !== undefined) {
                        $('#registeredTotal').html('(' + response.total + ')');
                    }
                    if (response.pagination) {
                        $('#paginationregisteragency').html(response.pagination);
                    }
                }
            },
            complete: function() {
                AgencyMarketplaceTabManager.clearRequest('registered');
            }
        });
        AgencyMarketplaceTabManager.registerRequest('registered', jqXHR);
        return jqXHR;
    }

    /**
     * Assigned Agencies Loader
     */
    function recommendedAssignList(page = 1, search = '') {
        if (page === 'load' || !page) { page = 1; }
        var jobid = $('#current_job_id').val();
        var cus_search = $('#agency_unified_search').val() || $('#kt_tagify_5').val();

        var jqXHR = $.ajax({
            url: `http://localhost/service_company/jobs/recommendedAssignList?page=${page}`,
            type: 'GET',
            data: {
                page: page,
                jobid: jobid,
                cus_search: cus_search
            },
            success: function(response) {
                if (response && response.html) {
                    $('#allAssignListagency').html(response.html);
                    if (response.total !== undefined) {
                        $('#assignedTotal').html('(' + response.total + ')');
                    }
                    if (response.pagination) {
                        $('#paginationAssignListagency').html(response.pagination);
                    }
                }
            },
            complete: function() {
                AgencyMarketplaceTabManager.clearRequest('assigned');
            }
        });
        AgencyMarketplaceTabManager.registerRequest('assigned', jqXHR);
        return jqXHR;
    }

    /**
     * Filter Modal: Apply filters callback
     */
    function newUpdateJobstatus() {
        $('#industry').val($('#industry_filter').val());
        $('#experience').val($('#experience_filter').val());
        $('#country').val($('#country_filter').val());

        var filterSkill = $('#kt_tagify_users').val();
        if (filterSkill) {
            $('#agency_unified_search').val(filterSkill);
        }

        $('#apptrackerfilter').modal('hide');
        tableLoad(1);
    }

    /**
     * IMMEDIATE EXECUTION: Binds all events and initializes state immediately upon drawer injection.
     */
    (function() {
        // Initialize Tab Manager and Load Initial Data
        AgencyMarketplaceTabManager.init();
        tableLoad(1);

        // Tab Switching Click Listeners
        $(document).off('click.agencyTabs', '.agency-tab-link').on('click.agencyTabs', '.agency-tab-link', function(e) {
            e.preventDefault();
            $('.agency-tab-link').removeClass('active');
            $(this).addClass('active');

            var tabKey = $(this).data('tab');
            var targetPane = $(this).attr('href');

            $('.tab-pane').removeClass('active show').hide();
            $(targetPane).addClass('active show').show();

            AgencyMarketplaceTabManager.switchTab(tabKey);
        });

        // Unified Search Input with Debounce (300ms)
        var searchTimeout = null;
        $(document).off('input.unifiedSearch', '#agency_unified_search').on('input.unifiedSearch', '#agency_unified_search', function() {
            clearTimeout(searchTimeout);
            var val = $(this).val();
            searchTimeout = setTimeout(function() {
                var activeTab = $('.agency-tab-link.active').data('tab') || 'recommended';
                if (activeTab === 'registered') {
                    $('#kt_tagify_4').val(val);
                    staffingPartnersRegister(1, val);
                } else if (activeTab === 'assigned') {
                    $('#kt_tagify_5').val(val);
                    recommendedAssignList(1, val);
                } else if (activeTab === 'all_agencies') {
                    loadAllAgenciesTab(1);
                } else {
                    tableLoad(1);
                }
            }, 300);
        });

        // Drawer Close Handlers (Back button & Close icon)
        $(document).off('click.drawerCloseTrigger', '.drawer-close-trigger').on('click.drawerCloseTrigger', '.drawer-close-trigger', function(e) {
            e.preventDefault();
            e.stopPropagation();

            var drawerEl = document.getElementById('kt_activitiesRecruiter');
            if (drawerEl && typeof KTDrawer !== 'undefined') {
                var drawerInstance = KTDrawer.getInstance(drawerEl);
                if (drawerInstance) {
                    drawerInstance.hide();
                    return;
                }
            }
            $('#kt_activitiesRecruiter_close').trigger('click');
        });

        // Filter modal triggers
        $(document).off('click.filterTrigger', '.btn-filter-trigger').on('click.filterTrigger', '.btn-filter-trigger', function(e) {
            e.preventDefault();
            $('#apptrackerfilter').modal('show');
        });
        $(document).off('click.filterClose', '#apptrackerfilter_close, [data-dismiss="modal"]').on('click.filterClose', '#apptrackerfilter_close, [data-dismiss="modal"]', function(e) {
            e.preventDefault();
            $('#apptrackerfilter').modal('hide');
        });

        // Filter modal hiring type toggle
        $(document).off('click.filterHiring', '.filter-hiring-box').on('click.filterHiring', '.filter-hiring-box', function() {
            $('.filter-hiring-box').removeClass('active').css({ 'border-color': '#e2e8f0', 'background': '#ffffff' });
            $(this).addClass('active').css({ 'border-color': '#2563eb', 'background': '#eff6ff' });
            $('#hiring_type').val($(this).data('type'));
        });

        // ── PUBLISH / UNPUBLISH TOGGLE HANDLER ───────────────────────────────
        $('body').off('click.postToPublic', '.post_to_public').on('click.postToPublic', '.post_to_public', function(e) {
            e.preventDefault();
            var $this = $(this);
            if ($this.data('in-flight')) return false;

            var current_jobid = $this.data('row-jobid') || $('#current_job_id').val();
            var job_cancel = $this.data('c');
            var id = 0;
            var url = "http://localhost/service_company/auction/send_job_request/:id".replace(':id', id);

            Swal.fire({
                title: job_cancel == 1 ? 'Unpublish from Marketplace?' : 'Publish to Agency Marketplace?',
                text: job_cancel == 1 
                    ? 'This job will no longer be visible to agencies on Jero Agency Marketplace.'
                    : 'This job will become LIVE on Jero Agency Marketplace. Verified agencies can discover and submit candidates.',
                icon: job_cancel == 1 ? 'warning' : 'info',
                showCancelButton: true,
                confirmButtonText: job_cancel == 1 ? 'Yes, unpublish' : 'Yes, publish now',
                cancelButtonText: 'Cancel',
                confirmButtonColor: job_cancel == 1 ? '#ef4444' : '#2563eb',
                cancelButtonColor: '#64748b'
            }).then((result) => {
                if (!result.isConfirmed) return;

                $this.data('in-flight', true).prop('disabled', true);

                $.ajax({
                    type: 'GET',
                    url: url,
                    data: {
                        'current_jobid': current_jobid,
                        'job_cancel': job_cancel
                    },
                    success: function(response) {
                        if (job_cancel == 1) {
                            // Swapped to UNPUBLISHED state
                            $('#marketplace_live_box').attr('style', 'display: none !important;').addClass('d-none');
                            $('#marketplace_unpublished_box').attr('style', 'display: block;').removeClass('d-none');
                            $('#sp_list_two').attr('style', 'display: none !important;').addClass('d-none');
                            $('#sp_list_one').attr('style', 'display: flex;').removeClass('d-none');
                            Swal.fire("Unpublished", "This job requirement is no longer live on the marketplace.", "success");
                        } else {
                            // Swapped to LIVE state
                            $('#marketplace_unpublished_box').attr('style', 'display: none !important;').addClass('d-none');
                            $('#marketplace_live_box').attr('style', 'display: block;').removeClass('d-none');
                            $('#sp_list_one').attr('style', 'display: none !important;').addClass('d-none');
                            $('#sp_list_two').attr('style', 'display: flex;').removeClass('d-none');
                            Swal.fire("Published Live!", "This job is now live on Jero Agency Marketplace.", "success");
                        }

                        // Invalidate RAM cache so fresh states persist
                        window.agencyTableCache = {};
                        if (window.jobDrawerCache) {
                            delete window.jobDrawerCache['spartner_' + current_jobid];
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Something went wrong. Please try again.', 'error');
                    },
                    complete: function() {
                        $this.data('in-flight', false).prop('disabled', false);
                    }
                });
            });
        });

        // ── SEND REQUIREMENT (INVITATION) ACTIONS ────────────────────────────
        $('body').off('click.sendOnlyInvite', '.send_only_job_invitation').on('click.sendOnlyInvite', '.send_only_job_invitation', function(e) {
            e.preventDefault();
            var ele = $(this);
            if (ele.data('in-flight')) return false;
            ele.data('in-flight', true).prop('disabled', true).addClass('disabled');

            var id = ele.data('row-jobid') || $('#current_job_id').val();
            var fromcompany = ele.data('row-fromcompany');
            var tocompany = ele.data('row-tocompany');
            var url = "http://localhost/service_company/jobs/send_only_job_invitation/:id".replace(':id', id);

            $.ajax({
                type: 'GET',
                url: url,
                data: {
                    'jobid': id,
                    'fromcompany': fromcompany,
                    'tocompany': tocompany
                },
                success: function(response) {
                    if (response.status == "success") {
                        ele.replaceWith('<span class="badge py-2 px-3 fw-bold fs-7" style="background:#dcfce7; color:#15803d; border-radius:6px; display:inline-flex; align-items:center; gap:4px;"><svg width="12" height="12" viewBox="0 0 24 24" fill="#15803d"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg> Request Sent</span>');
                        Swal.fire("Success", response.message || "Requirement sent to agency successfully.", "success");
                    } else {
                        Swal.fire("Notice", response.message || "Could not send requirement.", "warning");
                    }
                },
                error: function() {
                    Swal.fire("Error", "Could not send requirement. Please try again.", "error");
                },
                complete: function() {
                    ele.data('in-flight', false).prop('disabled', false).removeClass('disabled');
                }
            });
        });

        $('body').off('click.sendJobRecom', '.send_job_recommanded_request').on('click.sendJobRecom', '.send_job_recommanded_request', function(e) {
            e.preventDefault();
            var $this = $(this);
            if ($this.data('in-flight')) return false;
            $this.data('in-flight', true).prop('disabled', true).addClass('disabled');

            var from_company_id = $this.data('row-id');
            var current_jobid = $this.data('row-jobid') || $('#current_job_id').val();
            var url = "http://localhost/service_company/jobs/send_job_recommanded_request/:id".replace(':id', from_company_id);

            $.ajax({
                type: 'GET',
                url: url,
                data: {
                    'from_company_id': from_company_id,
                    'current_jobid': current_jobid
                },
                success: function(response) {
                    if (response.status === "success") {
                        $this.replaceWith('<span class="badge py-2 px-3 fw-bold fs-7" style="background:#dcfce7; color:#15803d; border-radius:6px; display:inline-flex; align-items:center; gap:4px;"><svg width="12" height="12" viewBox="0 0 24 24" fill="#15803d"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg> Request Sent</span>');
                        Swal.fire("Success", response.message || "Requirement sent to agency successfully.", "success");
                    } else {
                        Swal.fire("Notice", response.message || "Could not send requirement.", "warning");
                    }
                },
                error: function() {
                    Swal.fire("Error", "Could not send requirement. Please try again.", "error");
                },
                complete: function() {
                    $this.data('in-flight', false).prop('disabled', false).removeClass('disabled');
                }
            });
        });

        $('body').off('click.resendReq', '.resend_job_recommanded_request').on('click.resendReq', '.resend_job_recommanded_request', function(e) {
            e.preventDefault();
            var $this = $(this);
            if ($this.data('in-flight')) return false;
            $this.data('in-flight', true).prop('disabled', true).addClass('disabled');

            var to_company_id = $this.data('row-id');
            var current_jobid = $this.data('row-jobid') || $('#current_job_id').val();
            var ename = $this.data('row-ename');
            var url = "http://localhost/service_company/jobs/resend_job_recommanded_request/:id".replace(':id', to_company_id);

            $.ajax({
                type: 'GET',
                url: url,
                data: {
                    'to_company_id': to_company_id,
                    'current_jobid': current_jobid,
                    'ename': ename
                },
                success: function(response) {
                    tableLoad('load');
                    if (response.status === "success") {
                        Swal.fire("Success", response.message || "Requirement resent successfully.", "success");
                    }
                },
                complete: function() {
                    $this.data('in-flight', false).prop('disabled', false).removeClass('disabled');
                }
            });
        });

        // ── REMOVE / IGNORE ACTIONS ──────────────────────────────────────────
        $('body').off('click.removeJob', '.remove_job').on('click.removeJob', '.remove_job', function(e) {
            e.preventDefault();
            var $this = $(this);
            if ($this.data('in-flight')) return false;

            var jobid = $this.data('row-jobid') || $('#current_job_id').val();
            var fromcompany = $this.data('row-fromcompany');
            var tocompany = $this.data('row-tocompany');

            Swal.fire({
                title: 'Remove Agency?',
                text: "This agency will be removed from this requirement.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, remove',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#ef4444'
            }).then((result) => {
                if (!result.isConfirmed) return;

                $this.data('in-flight', true).prop('disabled', true);
                var url = "http://localhost/service_company/jobs/removeJob";

                $.ajax({
                    type: 'GET',
                    url: url,
                    data: {
                        'jobid': jobid,
                        'fromcompany': fromcompany,
                        'tocompany': tocompany
                    },
                    success: function(response) {
                        if (response.status == 'success') {
                            tableLoad('load');
                            staffingPartnersRegister('load');
                            recommendedAssignList('load');
                            Swal.fire('Removed', 'Agency removed successfully.', 'success');
                        }
                    },
                    complete: function() {
                        $this.data('in-flight', false).prop('disabled', false);
                    }
                });
            });
        });

        $('body').off('click.removeAssignJob', '.remove_assign_job').on('click.removeAssignJob', '.remove_assign_job', function(e) {
            e.preventDefault();
            var $this = $(this);
            if ($this.data('in-flight')) return false;

            var id = $this.data('row-id');
            var jobid = $this.data('row-jobid') || $('#current_job_id').val();

            Swal.fire({
                title: 'Ignore Agency Request?',
                text: "Selected agency request will be ignored.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, ignore',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#ef4444'
            }).then((result) => {
                if (!result.isConfirmed) return;

                $this.data('in-flight', true).prop('disabled', true);
                var url = "http://localhost/service_company/jobs/assignRejected";

                $.ajax({
                    type: 'GET',
                    url: url,
                    data: { 'id': id, 'jobid': jobid },
                    success: function(response) {
                        if (response.status == 'success') {
                            recommendedAssignList('load');
                            Swal.fire('Ignored', 'Agency request ignored.', 'success');
                        }
                    },
                    complete: function() {
                        $this.data('in-flight', false).prop('disabled', false);
                    }
                });
            });
        });

        // Pagination handlers for Assigned tab
        $(document).off('click.pagiAssign', '#paginationAssignListagency .pagi-page, #paginationAssignListagency .pagi-arrow')
                   .on('click.pagiAssign', '#paginationAssignListagency .pagi-page, #paginationAssignListagency .pagi-arrow', function(e) {
            e.preventDefault();
            var page = $(this).attr('data-page') || $(this).data('page');
            if (!page || $(this).hasClass('disabled')) return;
            recommendedAssignList(page);
        });

        // Pagination handlers for Registered tab
        $(document).off('click.pagiRegister', '#paginationregisteragency .pagi-page, #paginationregisteragency .pagi-arrow')
                   .on('click.pagiRegister', '#paginationregisteragency .pagi-page, #paginationregisteragency .pagi-arrow', function(e) {
            e.preventDefault();
            var page = $(this).attr('data-page') || $(this).data('page');
            if (!page || $(this).hasClass('disabled')) return;
            staffingPartnersRegister(page);
        });
    })();
