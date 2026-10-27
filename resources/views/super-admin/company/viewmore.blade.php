@php
    use App\Helper\GeneralMethod;
    $userPermissions = [];
@endphp
<style>
    /* Scoped Super Admin Company Drawer (Consultancy Reference Parity) */
    .sa-company-drawer {
        font-family: 'Poppins', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        color: #181c32;
        background: #f5f8fa;
        overflow: visible !important;
        height: auto !important;
        max-height: none !important;
    }

    .sa-company-drawer#kt_activities_body,
    .sa-company-drawer.card-body {
        overflow: visible !important;
        height: auto !important;
        max-height: none !important;
    }

    .sa-company-drawer #kt_activities_scroll {
        padding: 12px 16px 24px 16px !important;
        background: #f5f8fa !important;
        overflow: visible !important;
        height: auto !important;
        max-height: none !important;
    }

    /* Zero top margin behavior */
    .sa-company-drawer .profile-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #eff2f5;
        padding: 16px 20px;
        margin-bottom: 14px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    }

    .sa-company-drawer .profile-header-wrap {
        display: flex;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }

    .sa-company-drawer .logo-circle {
        position: relative;
        width: 64px;
        height: 64px;
        border-radius: 12px;
        border: 1.5px solid #e4e6ef;
        background: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        overflow: visible;
    }

    .sa-company-drawer .logo-circle img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 10px;
    }

    .sa-company-drawer .logo-circle .verified-check {
        position: absolute;
        top: -4px;
        right: -4px;
        width: 20px;
        height: 20px;
        background: #50cd89;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #ffffff;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        color: #ffffff;
        font-size: 9px;
    }

    .sa-company-drawer .profile-info {
        flex: 1;
        min-width: 220px;
    }

    .sa-company-drawer .profile-name-row {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 4px;
        flex-wrap: wrap;
    }

    .sa-company-drawer .profile-name {
        font-size: 18px;
        font-weight: 700;
        color: #181c32;
        line-height: 1.25;
        margin: 0;
    }

    .sa-company-drawer .verified-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: #e8fff3;
        color: #47be7d;
        font-size: 11px;
        font-weight: 600;
        padding: 3px 8px;
        border-radius: 50px;
    }

    .sa-company-drawer .profile-meta {
        display: flex;
        align-items: center;
        gap: 6px 14px;
        flex-wrap: wrap;
        font-size: 12px;
        color: #7e8299;
        margin-bottom: 6px;
    }

    .sa-company-drawer .meta-item {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .sa-company-drawer .meta-item i {
        font-size: 11px;
        color: #a1a5b7;
    }

    .sa-company-drawer .meta-divider {
        color: #d1d5db;
        font-size: 10px;
    }

    .sa-company-drawer .service-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .sa-company-drawer .service-tags .tag {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 2px 8px;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 500;
        color: #475569;
    }

    .sa-company-drawer .profile-right {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 8px;
        flex-shrink: 0;
    }

    .sa-company-drawer .status-row {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .sa-company-drawer .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        font-weight: 600;
        padding: 4px 12px;
        border-radius: 6px;
    }

    .sa-company-drawer .status-badge-active {
        background: #e8fff3;
        color: #47be7d;
        border: 1px solid rgba(80, 205, 137, 0.2);
    }

    .sa-company-drawer .status-badge-suspend {
        background: #fff5f8;
        color: #f1416c;
        border: 1px solid rgba(241, 65, 108, 0.2);
    }

    .sa-company-drawer .status-dot {
        width: 8px;
        height: 8px;
        background: currentColor;
        border-radius: 50%;
        position: relative;
    }

    /* 5-Column KPI Row (Exact match to Consultancy reference) */
    .sa-company-drawer .kpi-row {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 12px;
        margin-bottom: 14px;
    }

    @media (max-width: 991.98px) {
        .sa-company-drawer .kpi-row {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 575.98px) {
        .sa-company-drawer .kpi-row {
            grid-template-columns: 1fr;
        }
    }

    .sa-company-drawer .kpi-box {
        background: #ffffff;
        border-radius: 10px;
        border: 1px solid #eff2f5;
        padding: 12px 14px;
        transition: all 0.2s ease;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    }

    .sa-company-drawer .kpi-box:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.04);
    }

    .sa-company-drawer .kpi-header {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
    }

    .sa-company-drawer .kpi-icon-box {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        flex-shrink: 0;
    }

    .sa-company-drawer .kpi-icon-blue { background: #f1faff; color: #009ef7; }
    .sa-company-drawer .kpi-icon-green { background: #e8fff3; color: #50cd89; }
    .sa-company-drawer .kpi-icon-purple { background: #f8f5ff; color: #7239ea; }
    .sa-company-drawer .kpi-icon-orange { background: #fff8dd; color: #f6a723; }
    .sa-company-drawer .kpi-icon-teal { background: #f0fdfa; color: #14b8a6; }

    .sa-company-drawer .kpi-label {
        font-size: 11px;
        font-weight: 600;
        color: #7e8299;
        line-height: 1.25;
    }

    .sa-company-drawer .kpi-number {
        font-size: 20px;
        font-weight: 700;
        color: #181c32;
        line-height: 1.2;
    }

    .sa-company-drawer .kpi-subtext {
        font-size: 11px;
        color: #a1a5b7;
        margin-top: 2px;
    }

    /* Tabs Bar Modernization matching Reference Design */
    .sa-company-drawer .sa-tabs-bar {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #eff2f5;
        margin-bottom: 14px;
        padding: 0 4px;
        overflow-x: auto;
        overflow-y: hidden;
        white-space: nowrap;
        scrollbar-width: none !important;
        -ms-overflow-style: none !important;
        -webkit-overflow-scrolling: touch;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    }

    .sa-company-drawer .sa-tabs-bar::-webkit-scrollbar {
        display: none !important;
        width: 0 !important;
        height: 0 !important;
    }

    .sa-company-drawer .nav-tabs.sa-nav-tabs {
        border-bottom: none !important;
        padding: 0;
        flex-wrap: nowrap;
        display: flex;
        align-items: center;
        gap: 0;
        margin: 0;
        min-height: 48px;
    }

    .sa-company-drawer .nav-tabs.sa-nav-tabs .nav-item {
        margin: 0;
        flex-shrink: 0;
    }

    .sa-company-drawer .nav-tabs.sa-nav-tabs .nav-link {
        position: relative;
        padding: 13px 18px;
        font-size: 13px;
        font-weight: 500;
        color: #7e8299;
        cursor: pointer;
        white-space: nowrap;
        border: none !important;
        background: transparent !important;
        font-family: 'Poppins', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        transition: color 0.15s ease, background-color 0.15s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        line-height: 1.2;
    }

    .sa-company-drawer .nav-tabs.sa-nav-tabs .nav-link:hover {
        color: #009ef7;
        background: transparent !important;
        text-decoration: none;
    }

    .sa-company-drawer .nav-tabs.sa-nav-tabs .nav-link:focus-visible {
        outline: none;
        box-shadow: inset 0 0 0 2px rgba(0, 158, 247, 0.25);
        border-radius: 6px;
    }

    .sa-company-drawer .nav-tabs.sa-nav-tabs .nav-link.active {
        color: #009ef7 !important;
        font-weight: 600;
        background: transparent !important;
    }

    .sa-company-drawer .nav-tabs.sa-nav-tabs .nav-link.active::after {
        content: "";
        position: absolute;
        bottom: 0;
        left: 16px;
        right: 16px;
        height: 2.5px;
        background: #009ef7;
        border-radius: 2px 2px 0 0;
    }

    .sa-company-drawer .tab-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 18px;
        height: 18px;
        padding: 0 6px;
        background: #f1f5f9;
        color: #475569;
        font-size: 10.5px;
        font-weight: 600;
        border-radius: 50px;
        line-height: 1;
        transition: background-color 0.15s ease, color 0.15s ease;
    }

    .sa-company-drawer .nav-tabs.sa-nav-tabs .nav-link.active .tab-count {
        background: #f1faff;
        color: #009ef7;
    }

    @media (max-width: 991.98px) {
        .sa-company-drawer .nav-tabs.sa-nav-tabs .nav-link {
            padding: 12px 14px;
            font-size: 12.5px;
        }

        .sa-company-drawer .nav-tabs.sa-nav-tabs .nav-link.active::after {
            left: 12px;
            right: 12px;
        }
    }

    /* Content Grid & Cards (Overview Tab matching Consultancy reference) */
    .sa-company-drawer .content-grid {
        display: grid;
        grid-template-columns: 1.2fr 0.8fr;
        gap: 16px;
        margin-bottom: 16px;
        align-items: start;
    }

    @media (max-width: 991.98px) {
        .sa-company-drawer .content-grid {
            grid-template-columns: 1fr;
        }
    }

    .sa-company-drawer .content-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #eff2f5;
        padding: 24px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    }

    .sa-company-drawer .card-header-side {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 20px;
    }

    .sa-company-drawer .card-header-icon {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        background: #f5f8fa;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #a1a5b7;
        font-size: 13px;
        flex-shrink: 0;
    }

    .sa-company-drawer .card-header-title {
        font-size: 15px;
        font-weight: 700;
        color: #181c32;
        margin: 0;
    }

    .sa-company-drawer .about-text {
        font-size: 13px;
        color: #7e8299;
        line-height: 1.7;
    }

    .sa-company-drawer .about-text p {
        margin-bottom: 14px;
    }

    .sa-company-drawer .about-text p:last-child {
        margin-bottom: 0;
    }

    /* Key-Value Info List */
    .sa-company-drawer .info-list {
        display: flex;
        flex-direction: column;
    }

    .sa-company-drawer .info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px dashed #eff2f5;
        gap: 12px;
    }

    .sa-company-drawer .info-row:last-child {
        border-bottom: none;
    }

    .sa-company-drawer .info-label {
        font-size: 12px;
        color: #a1a5b7;
        font-weight: 500;
        flex-shrink: 0;
    }

    .sa-company-drawer .info-value {
        font-size: 12px;
        color: #3f4254;
        font-weight: 600;
        text-align: right;
        display: flex;
        align-items: center;
        gap: 6px;
        justify-content: flex-end;
        flex-wrap: wrap;
    }

    .sa-company-drawer .info-value a {
        color: #009ef7;
        text-decoration: none;
    }

    .sa-company-drawer .info-value a:hover {
        text-decoration: underline;
    }

    .sa-company-drawer .info-value a i {
        font-size: 9px;
        margin-left: 2px;
    }

    /* Commercials Items */
    .sa-company-drawer .commercial-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 14px;
        background: #f5f8fa;
        border-radius: 8px;
        margin-bottom: 10px;
        transition: all 0.2s;
    }

    .sa-company-drawer .commercial-item:last-child {
        margin-bottom: 0;
    }

    .sa-company-drawer .commercial-item:hover {
        background: #f1faff;
    }

    .sa-company-drawer .commercial-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        flex-shrink: 0;
    }

    .sa-company-drawer .commercial-icon.purple {
        background: #f8f5ff;
        color: #7239ea;
    }

    .sa-company-drawer .commercial-icon.orange {
        background: #fff8dd;
        color: #f6a723;
    }

    .sa-company-drawer .commercial-title {
        font-size: 13px;
        font-weight: 600;
        color: #181c32;
    }

    .sa-company-drawer .commercial-desc {
        font-size: 11px;
        color: #a1a5b7;
        margin-top: 2px;
    }

    /* Key Contacts Card below Grid */
    .sa-company-drawer .contacts-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #eff2f5;
        padding: 24px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    }

    .sa-company-drawer .contacts-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .sa-company-drawer .contacts-title {
        font-size: 15px;
        font-weight: 700;
        color: #181c32;
        margin: 0;
    }

    .sa-company-drawer .view-all {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #009ef7;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
    }

    .sa-company-drawer .view-all:hover {
        text-decoration: underline;
    }

    .sa-company-drawer .view-all i {
        font-size: 10px;
    }

    .sa-company-drawer .contacts-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
    }

    @media (max-width: 1199.98px) {
        .sa-company-drawer .contacts-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 575.98px) {
        .sa-company-drawer .contacts-grid {
            grid-template-columns: 1fr;
        }
    }

    .sa-company-drawer .contact-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 16px;
        background: #f5f8fa;
        border-radius: 10px;
        border: 1px solid transparent;
        transition: all 0.2s;
    }

    .sa-company-drawer .contact-item:hover {
        background: #ffffff;
        border-color: #e4e6ef;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
    }

    .sa-company-drawer .contact-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #e4e6ef;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 13px;
        color: #7e8299;
        flex-shrink: 0;
    }

    .sa-company-drawer .contact-item:hover .contact-avatar {
        background: #f1faff;
        color: #009ef7;
    }

    .sa-company-drawer .contact-body {
        flex: 1;
        min-width: 0;
    }

    .sa-company-drawer .contact-name-row {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 2px;
        flex-wrap: wrap;
    }

    .sa-company-drawer .contact-name {
        font-size: 13px;
        font-weight: 600;
        color: #181c32;
    }

    .sa-company-drawer .contact-role {
        font-size: 11px;
        color: #a1a5b7;
        margin-bottom: 8px;
    }

    .sa-company-drawer .contact-email,
    .sa-company-drawer .contact-phone {
        font-size: 11px;
        color: #a1a5b7;
        margin-bottom: 2px;
        word-break: break-all;
    }

    .sa-company-drawer .contact-actions-row {
        display: flex;
        gap: 6px;
        margin-left: auto;
        flex-shrink: 0;
    }

    .sa-company-drawer .contact-btn {
        width: 28px;
        height: 28px;
        border-radius: 6px;
        border: 1px solid #e4e6ef;
        background: #ffffff;
        color: #b5b5c3;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 11px;
        text-decoration: none;
    }

    .sa-company-drawer .contact-btn:hover {
        border-color: #009ef7;
        color: #009ef7;
        background: #f1faff;
    }

    /* ── Contacts Tab Presentation (Reference Parity) ── */
    .sa-company-drawer .contacts-table-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #eff2f5;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        margin-bottom: 20px;
    }

    .sa-company-drawer .contacts-table-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 18px 24px;
        border-bottom: 1px solid #eff2f5;
        gap: 16px;
        flex-wrap: wrap;
    }

    .sa-company-drawer .contacts-table-title {
        font-size: 18px;
        font-weight: 700;
        color: #181c32;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .sa-company-drawer .contacts-table-title span {
        color: #a1a5b7;
        font-weight: 500;
        font-size: 15px;
    }

    .sa-company-drawer .contacts-table-subtitle {
        font-size: 12.5px;
        color: #a1a5b7;
        margin-top: 3px;
    }

    .sa-company-drawer .contacts-table-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .sa-company-drawer .contacts-table-actions .search-box {
        position: relative;
    }

    .sa-company-drawer .contacts-table-actions .search-box input {
        padding: 8px 14px 8px 36px;
        border: 1px solid #e4e6ef;
        border-radius: 8px;
        font-family: 'Poppins', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        font-size: 13px;
        color: #181c32;
        outline: none;
        width: 220px;
        height: 38px;
        background: #ffffff;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .sa-company-drawer .contacts-table-actions .search-box input:focus {
        border-color: #009ef7;
        box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.1);
    }

    .sa-company-drawer .contacts-table-actions .search-box i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #a1a5b7;
        font-size: 13px;
        pointer-events: none;
    }

    .sa-company-drawer .contacts-table-actions .btn-outline {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 0 16px;
        height: 38px;
        border: 1px solid #e4e6ef;
        border-radius: 8px;
        background: #ffffff;
        color: #5e6278;
        font-size: 13px;
        font-weight: 600;
        font-family: 'Poppins', sans-serif;
        transition: all 0.15s ease;
        cursor: pointer;
    }

    .sa-company-drawer .contacts-table-actions .btn-outline:hover {
        background: #f9fafb;
        border-color: #d1d5db;
        color: #181c32;
    }

    .sa-company-drawer .contacts-table-actions .btn-primary {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 0 18px;
        height: 38px;
        border: none;
        border-radius: 8px;
        background: #009ef7;
        color: #ffffff !important;
        font-size: 13px;
        font-weight: 600;
        font-family: 'Poppins', sans-serif;
        transition: background 0.15s ease;
        text-decoration: none;
    }

    .sa-company-drawer .contacts-table-actions .btn-primary:hover {
        background: #0086d4;
        color: #ffffff !important;
    }

    .sa-company-drawer .table-wrapper {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .sa-company-drawer .table-wrapper::-webkit-scrollbar {
        height: 6px;
    }

    .sa-company-drawer .table-wrapper::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 3px;
    }

    .sa-company-drawer .data-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 900px;
    }

    .sa-company-drawer .data-table thead tr {
        background: #f8f9fa;
        border-bottom: 1px solid #eff2f5;
    }

    .sa-company-drawer .data-table th {
        padding: 12px 16px;
        text-align: left;
        font-size: 11px;
        font-weight: 600;
        color: #7e8299;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        white-space: nowrap;
        vertical-align: middle;
    }

    .sa-company-drawer .data-table th i {
        font-size: 9px;
        margin-left: 4px;
        color: #b5b5c3;
    }

    .sa-company-drawer .data-table td {
        padding: 14px 16px;
        font-size: 13px;
        color: #3f4254;
        border-bottom: 1px solid #f5f8fa;
        vertical-align: middle;
    }

    .sa-company-drawer .data-table tr:hover td {
        background: #fafbfc;
    }

    .sa-company-drawer .data-table tr:last-child td {
        border-bottom: none;
    }

    .sa-company-drawer .data-table th:first-child,
    .sa-company-drawer .data-table td:first-child {
        padding-left: 20px;
    }

    .sa-company-drawer .data-table th:last-child,
    .sa-company-drawer .data-table td:last-child {
        padding-right: 20px;
    }

    .sa-company-drawer .table-checkbox {
        width: 16px;
        height: 16px;
        border: 1.5px solid #d1d5db;
        border-radius: 4px;
        cursor: pointer;
        accent-color: #009ef7;
    }

    .sa-company-drawer .contact-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .sa-company-drawer .contact-avatar-table {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 13.5px;
        color: #ffffff;
        flex-shrink: 0;
        overflow: hidden;
    }

    .sa-company-drawer .contact-info-table {
        line-height: 1.35;
    }

    .sa-company-drawer .contact-name-table {
        font-size: 13.5px;
        font-weight: 700;
        color: #181c32;
        cursor: pointer;
    }

    .sa-company-drawer .contact-name-table:hover {
        color: #009ef7;
    }

    .sa-company-drawer .contact-role-table {
        font-size: 12px;
        color: #7e8299;
        font-weight: 500;
        line-height: 1.2;
    }

    .sa-company-drawer .contact-primary-badge {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        background: #e8fff3;
        color: #47be7d;
        font-size: 10px;
        font-weight: 600;
        padding: 2px 7px;
        border-radius: 50px;
    }

    .sa-company-drawer .contact-primary-badge i {
        font-size: 8px;
    }

    .sa-company-drawer .dept-badge {
        display: inline-flex;
        align-items: center;
        background: #f5f8fa;
        color: #5e6278;
        font-size: 12px;
        font-weight: 500;
        padding: 4px 10px;
        border-radius: 6px;
    }

    .sa-company-drawer .contact-email-phone {
        line-height: 1.35;
    }

    .sa-company-drawer .email-cell {
        color: #3f4254;
        font-size: 13px;
        text-decoration: none;
        font-weight: 500;
    }

    .sa-company-drawer .email-cell:hover {
        color: #009ef7;
        text-decoration: underline;
    }

    .sa-company-drawer .phone-cell {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        color: #7e8299;
        margin-top: 3px;
    }

    .sa-company-drawer .phone-flag {
        width: 18px;
        height: 12px;
        border-radius: 2px;
        overflow: hidden;
        flex-shrink: 0;
        border: 1px solid rgba(0,0,0,0.06);
    }

    .sa-company-drawer .phone-flag svg {
        width: 100%;
        height: 100%;
        display: block;
    }

    .sa-company-drawer .phone-number {
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .sa-company-drawer .phone-prefix {
        font-size: 12px;
        color: #7e8299;
        font-weight: 500;
    }

    .sa-company-drawer .phone-digits {
        font-size: 12px;
        color: #5e6278;
        font-weight: 500;
    }

    .sa-company-drawer .status-badge-table {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        font-weight: 600;
        padding: 4px 12px;
        border-radius: 50px;
    }

    .sa-company-drawer .status-badge-table.active {
        background: #e8fff3;
        color: #47be7d;
    }

    .sa-company-drawer .status-badge-table.inactive {
        background: #f5f8fa;
        color: #a1a5b7;
    }

    .sa-company-drawer .status-badge-table i {
        font-size: 6px;
    }

    .sa-company-drawer .action-btns {
        display: flex;
        align-items: center;
        gap: 6px;
        justify-content: flex-end;
    }

    .sa-company-drawer .action-btn-table {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        border: 1px solid #e4e6ef;
        background: #ffffff;
        color: #7e8299;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.15s ease;
        text-decoration: none;
        padding: 0;
        font-size: 12px;
    }

    .sa-company-drawer .action-btn-table:hover {
        background: #f1faff;
        border-color: #009ef7;
        color: #009ef7;
    }

    .sa-company-drawer .action-btn-table.whatsapp:hover {
        border-color: #25d366 !important;
        color: #25d366 !important;
        background: #e8f5e9 !important;
    }

    .sa-company-drawer .action-btn-table.more {
        border: none !important;
        background: transparent !important;
        color: #b5b5c3 !important;
        width: 24px;
        height: 32px;
    }

    .sa-company-drawer .action-btn-table.more:hover {
        color: #5e6278 !important;
        background: transparent !important;
    }

    @media (max-width: 991.98px) {
        .sa-company-drawer .contacts-table-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
            padding: 16px;
        }

        .sa-company-drawer .contacts-table-actions {
            width: 100%;
        }

        .sa-company-drawer .contacts-table-actions .search-box {
            flex: 1;
        }

        .sa-company-drawer .contacts-table-actions .search-box input {
            width: 100%;
        }
    }

    /* ── Jobs Tab Presentation (Reference Parity) ── */
    .sa-company-drawer #requisitions {
        --indigo: #3b55e6;
        --orange: #f4682a;
        --border: #eff2f5;
        --text-dark: #181c32;
        --text-mid: #6d7284;
        --text-light: #a1a5b7;
        --white: #ffffff;
        --radius: 12px;
    }

    .sa-company-drawer #requisitions .table-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        border: 1px solid #eff2f5;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        margin-bottom: 20px;
        position: relative;
    }

    .sa-company-drawer #requisitions .table-header,
    .sa-company-drawer #requisitions .job-row {
        display: grid;
        grid-template-columns: 2.6fr 1.05fr 1.15fr 1.3fr 0.95fr 0.95fr 2.55fr;
        align-items: start;
        column-gap: 12px;
        min-width: 1040px;
    }

    .sa-company-drawer #requisitions .table-header {
        padding: 10px 14px;
        background: #f8f9fa;
        border-bottom: 1.5px solid #eff2f5;
        align-items: center;
    }

    .sa-company-drawer #requisitions .th {
        font-size: 10.5px;
        font-weight: 600;
        color: #7e8299;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .sa-company-drawer #requisitions .job-row {
        padding: 14px 14px 28px 14px;
        border-bottom: 1px solid #f5f8fa;
        transition: background 0.12s;
        position: relative;
        min-height: 112px;
    }

    .sa-company-drawer #requisitions .job-row:last-of-type {
        border-bottom: none;
    }

    .sa-company-drawer #requisitions .job-row:hover {
        background: #fafbff;
    }

    .sa-company-drawer #requisitions .col-job {
        display: flex;
        gap: 10px;
        cursor: pointer;
    }

    .sa-company-drawer #requisitions .job-logo {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        font-weight: 800;
        overflow: hidden;
        align-self: flex-start;
    }

    .sa-company-drawer #requisitions .job-logo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .sa-company-drawer #requisitions .job-info {
        flex: 1;
        min-width: 0;
    }

    .sa-company-drawer #requisitions .job-title {
        font-size: 12.5px;
        font-weight: 700;
        color: #181c32;
        margin-bottom: 2px;
        line-height: 1.3;
    }

    .sa-company-drawer #requisitions .job-dept {
        font-size: 11px;
        color: #6d7284;
        margin-bottom: 2px;
    }

    .sa-company-drawer #requisitions .job-client {
        font-size: 11px;
        color: #7e8299;
        font-weight: 500;
        margin-bottom: 4px;
    }

    .sa-company-drawer #requisitions .job-meta-line {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 5px;
        font-size: 10.5px;
        color: #a1a5b7;
    }

    .sa-company-drawer #requisitions .skills-inline {
        font-size: 10.5px;
        color: #a1a5b7;
    }

    .sa-company-drawer #requisitions .skills-inline b {
        font-weight: 600;
        color: #6d7284;
    }

    .sa-company-drawer #requisitions .col-exp {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .sa-company-drawer #requisitions .exp-row {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 11.5px;
        font-weight: 500;
        color: #181c32;
    }

    .sa-company-drawer #requisitions .exp-row svg,
    .sa-company-drawer #requisitions .sal-row svg {
        width: 13px;
        height: 13px;
        color: #a1a5b7;
        flex-shrink: 0;
    }

    .sa-company-drawer #requisitions .sal-row {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 11.5px;
        width: 150px;
        font-weight: 500;
        color: #181c32;
    }

    .sa-company-drawer #requisitions .badge-type {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        padding: 2px 9px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 600;
        width: fit-content;
        margin-top: 2px;
    }

    .sa-company-drawer #requisitions .badge-internal {
        background: #eef2ff;
        color: #3b55e6;
    }

    .sa-company-drawer #requisitions .badge-client {
        background: #eefff6;
        color: #09b760;
    }

    .sa-company-drawer #requisitions .it-job-type {
        display: inline-flex;
        align-items: center;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 9px;
        font-weight: 600;
    }

    .sa-company-drawer #requisitions .it-job-id {
        font-size: 10px;
        color: #6a6b70;
        font-weight: 500;
        text-decoration: none;
    }

    .sa-company-drawer #requisitions .it-job-id:hover {
        text-decoration: underline;
    }

    .sa-company-drawer #requisitions .it-dot-separator {
        width: 3px;
        height: 3px;
        border-radius: 50%;
        background: #a1a5b7;
    }

    .sa-company-drawer #requisitions .col-hl {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .sa-company-drawer #requisitions .badge-hiring {
        padding: 2px 9px;
        border-radius: 20px;
        font-size: 10.5px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        width: fit-content;
        background: #e8fff3;
        color: #50cd89;
    }

    .sa-company-drawer #requisitions .badge-hiring::before {
        content: "";
        width: 6px;
        height: 6px;
        background: #50cd89;
        border-radius: 50%;
    }

    .sa-company-drawer #requisitions .badge-hiring.closed-bg,
    .sa-company-drawer #requisitions .badge-hiring.inactive-bg {
        background: #f5f8fa;
        color: #7e8299;
    }

    .sa-company-drawer #requisitions .badge-hiring.closed-bg::before,
    .sa-company-drawer #requisitions .badge-hiring.inactive-bg::before {
        background: #b5b5c3;
    }

    .sa-company-drawer #requisitions .badge-hiring.hold-bg {
        background: #fff8dd;
        color: #ffc700;
    }

    .sa-company-drawer #requisitions .badge-hiring.hold-bg::before {
        background: #ffc700;
    }

    .sa-company-drawer #requisitions .loc-row,
    .sa-company-drawer #requisitions .mode-row {
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: 11.5px;
        color: #6d7284;
        font-weight: 500;
    }

    .sa-company-drawer #requisitions .loc-row svg,
    .sa-company-drawer #requisitions .mode-row svg {
        width: 12px;
        height: 12px;
        color: #a1a5b7;
        flex-shrink: 0;
    }

    .sa-company-drawer #requisitions .col-hm {
        font-size: 11.5px;
    }

    .sa-company-drawer #requisitions .hm-name {
        font-size: 12px;
        font-weight: 600;
        color: #181c32;
        margin-bottom: 2px;
    }

    .sa-company-drawer #requisitions .hm-title {
        font-size: 11px;
        color: #6d7284;
        margin-bottom: 1px;
    }

    .sa-company-drawer #requisitions .hm-company {
        font-size: 11px;
        color: #a1a5b7;
    }

    .sa-company-drawer #requisitions .col-action {
        display: flex;
        flex-direction: column;
        gap: 5px;
        padding: 0 6px;
    }

    .sa-company-drawer #requisitions .action-count {
        font-size: 11px;
        color: #6d7284;
    }

    .sa-company-drawer #requisitions .col-pipeline {
        display: flex;
        flex-direction: column;
        gap: 4px;
        padding-left: 10px;
        border-left: 1px solid #eff2f5;
    }

    .sa-company-drawer #requisitions .pipeline-stages {
        display: flex;
        align-items: flex-start;
        gap: 2px;
        flex-wrap: nowrap;
        overflow: hidden;
        padding-bottom: 2px;
    }

    .sa-company-drawer #requisitions .stage {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 2px;
        flex: 1;
        min-width: 0;
    }

    .sa-company-drawer #requisitions .stage-label {
        font-size: 8.5px;
        font-weight: 600;
        color: #6d7284;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .sa-company-drawer #requisitions .stage-badge {
        min-width: 22px;
        width: 22px;
        height: 19px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 9.5px;
        font-weight: 700;
    }

    .sa-company-drawer #requisitions .stage-badge a {
        text-decoration: none;
    }

    .sa-company-drawer #requisitions .s-inw { background: #fffde6; color: #7c6d00; }
    .sa-company-drawer #requisitions .s-sou { background: #efefef; color: #181c32; }
    .sa-company-drawer #requisitions .s-scr { background: #fbf0ff; color: #8e14bc; }
    .sa-company-drawer #requisitions .s-eva { background: #f0f8ff; color: #0b72cb; }
    .sa-company-drawer #requisitions .s-neg { background: #fff0f7; color: #c01867; }
    .sa-company-drawer #requisitions .s-off { background: #fff9ed; color: #b37400; }
    .sa-company-drawer #requisitions .s-joi { background: #e8fff0; color: #058722; }
    .sa-company-drawer #requisitions .s-rej { background: #ffebee; color: #c62828; }

    .sa-company-drawer #requisitions .pipeline-total {
        font-size: 10px;
        font-weight: 600;
        color: #6d7284;
        text-align: right;
        display: flex;
        justify-content: flex-end;
        gap: 6px;
        flex-wrap: wrap;
    }

    .sa-company-drawer #requisitions .pipeline-total strong {
        color: #181c32;
    }

    .sa-company-drawer #requisitions .add-date {
        font-size: 10px;
        color: #a1a5b7;
    }

    .sa-company-drawer #requisitions .drag-col {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding-top: 2px;
        flex-shrink: 0;
    }

    .sa-company-drawer #requisitions .drag-handle {
        color: #a1a5b7;
        cursor: grab;
    }

    @media (max-width: 1600px) {
        .sa-company-drawer #requisitions .table-header,
        .sa-company-drawer #requisitions .job-row {
            grid-template-columns: 2.3fr 1fr 1fr 1.2fr 0.9fr 0.9fr 2.8fr;
        }
    }

    @media (max-width: 1366px) {
        .sa-company-drawer #requisitions .table-header,
        .sa-company-drawer #requisitions .job-row {
            grid-template-columns: 2.6fr .95fr .95fr 1fr .85fr .85fr 1.8fr;
        }
    }

    /* ==========================================================================
       Unified Tab Background & Card Design (All Tabs Same Background and Design)
       ========================================================================== */

    /* Ensure legacy tabs have clean white card container, while Contacts and Jobs
       use their dedicated zero-nesting table cards directly */
    .sa-company-drawer .tab-content > .tab-pane:not(#overview):not(#contacts):not(#requisitions) {
        background: #ffffff !important;
        border-radius: 12px !important;
        border: 1px solid #eff2f5 !important;
        padding: 24px !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02) !important;
        margin-bottom: 20px !important;
    }

    .sa-company-drawer .tab-content > .tab-pane#contacts,
    .sa-company-drawer .tab-content > .tab-pane#requisitions {
        background: transparent !important;
        border: none !important;
        padding: 0 !important;
        box-shadow: none !important;
        margin-bottom: 20px !important;
    }

    @media (max-width: 767.98px) {
        .sa-company-drawer .tab-content > .tab-pane:not(#overview):not(#contacts):not(#requisitions) {
            padding: 16px !important;
        }
    }

    /* Card Header Styling across all tabs */
    .sa-company-drawer .tab-pane .card-header {
        background: transparent !important;
        border-bottom: 1px solid #eff2f5 !important;
        padding: 0 0 16px 0 !important;
        margin-bottom: 20px !important;
        min-height: auto !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
    }

    .sa-company-drawer .tab-pane .card-title,
    .sa-company-drawer .tab-pane .card-title h3,
    .sa-company-drawer .tab-pane .card-header h3 {
        font-size: 16px !important;
        font-weight: 700 !important;
        color: #181c32 !important;
        margin: 0 !important;
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
    }

    .sa-company-drawer .tab-pane .card-title {
        position: relative;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
    }

    .sa-company-drawer .tab-pane .card-title a.btn {
        position: static !important;
    }

    /* Card Body Reset inside tab panes (prevent nested paddings/shadows) */
    .sa-company-drawer .tab-pane:not(#overview) .card-body {
        padding: 0 !important;
        background: transparent !important;
    }

    .sa-company-drawer .tab-pane:not(#overview) .card {
        border: none !important;
        box-shadow: none !important;
        background: transparent !important;
        margin: 0 !important;
    }

    /* Sub-tabs inside Tab Panes (#requisitions, #timeline) */
    .sa-company-drawer .tab-pane#requisitions .tab-pane,
    .sa-company-drawer .tab-pane#timeline .tab-pane {
        background: transparent !important;
        border: none !important;
        padding: 0 !important;
        box-shadow: none !important;
        margin-bottom: 0 !important;
    }

    .sa-company-drawer .tab-pane .nav-tabs {
        border-bottom: 1px solid #eff2f5 !important;
        gap: 4px !important;
        margin-bottom: 16px !important;
        padding: 0 !important;
    }

    .sa-company-drawer .tab-pane .nav-tabs .nav-link {
        font-size: 12.5px !important;
        font-weight: 600 !important;
        color: #7e8299 !important;
        padding: 10px 16px !important;
        border: none !important;
        border-bottom: 2px solid transparent !important;
        background: transparent !important;
        transition: all 0.2s ease !important;
    }

    .sa-company-drawer .tab-pane .nav-tabs .nav-link:hover {
        color: #009ef7 !important;
    }

    .sa-company-drawer .tab-pane .nav-tabs .nav-link.active {
        color: #009ef7 !important;
        border-bottom-color: #009ef7 !important;
        background: transparent !important;
    }

    /* Modern Table Design across ALL Tabs (Matching Consultancy Standard) */
    .sa-company-drawer .tab-pane .table {
        margin-bottom: 0 !important;
        width: 100% !important;
        border-collapse: collapse !important;
    }

    .sa-company-drawer .tab-pane .table thead tr {
        background: #f8f9fa !important;
        border-bottom: 1px solid #eff2f5 !important;
    }

    .sa-company-drawer .tab-pane .table th {
        padding: 12px 16px !important;
        font-size: 11px !important;
        font-weight: 600 !important;
        color: #7e8299 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.3px !important;
        border-bottom: 1px solid #eff2f5 !important;
        vertical-align: middle !important;
    }

    .sa-company-drawer .tab-pane .table td {
        padding: 13px 16px !important;
        font-size: 12.5px !important;
        color: #3f4254 !important;
        border-bottom: 1px solid #f5f8fa !important;
        vertical-align: middle !important;
    }

    .sa-company-drawer .tab-pane .table tbody tr:hover td {
        background: #fafbfc !important;
    }

    .sa-company-drawer .tab-pane .table tbody tr:last-child td {
        border-bottom: none !important;
    }

    /* Inputs, Selects & Textareas Across Tabs */
    .sa-company-drawer .tab-pane .form-control {
        border: 1px solid #e4e6ef !important;
        border-radius: 8px !important;
        padding: 8px 14px !important;
        font-size: 13px !important;
        background-color: #f8fafc !important;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out !important;
    }

    .sa-company-drawer .tab-pane .form-control:focus {
        background-color: #ffffff !important;
        border-color: #009ef7 !important;
        box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.1) !important;
        outline: none !important;
    }

    .sa-company-drawer .tab-pane .form-label {
        font-size: 12px !important;
        font-weight: 600 !important;
        color: #181c32 !important;
        margin-bottom: 6px !important;
    }

    /* Search Input in Jobs tab */
    .sa-company-drawer .tab-pane #inputJobSearch {
        border: 1px solid #e4e6ef !important;
        border-radius: 8px !important;
        background: #f8fafc !important;
        font-size: 12.5px !important;
        height: 38px !important;
    }

    .sa-company-drawer .tab-pane #inputJobSearch:focus {
        background: #ffffff !important;
        border-color: #009ef7 !important;
    }

    /* Migration Tab Buttons & Previews */
    .sa-company-drawer .tab-pane#migration .copy-button {
        background: #f1f5f9;
        color: #009ef7;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 5px 12px;
        font-size: 11px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        margin-left: 8px;
    }

    .sa-company-drawer .tab-pane#migration .copy-button:hover {
        background: #009ef7;
        color: #ffffff;
        border-color: #009ef7;
    }

    .sa-company-drawer .tab-pane#migration p {
        font-size: 13px;
        color: #3f4254;
        margin-bottom: 16px;
        line-height: 1.6;
    }

    .sa-company-drawer .tab-pane#migration img {
        max-width: 100%;
        border-radius: 8px;
        border: 1px solid #eff2f5;
        margin-top: 8px;
    }

    /* Top-up Coins Tab Reset */
    .sa-company-drawer .tab-pane#topupcoins .modal-body {
        padding: 0 !important;
        margin: 0 !important;
        max-height: none !important;
    }

    /* Timeline Tab Modern Alignment */
    .sa-company-drawer .tab-pane#timeline .timeline {
        padding: 8px 0;
    }

    .sa-company-drawer .tagify__input {
        height: 18px !important;
    }

    .sa-company-drawer .payment-method {
        display: none;
        margin-bottom: 20px;
    }

    .sa-company-drawer .payment-method.active {
        display: block;
    }

    /* Accessible Focus Indicators for Keyboard Navigation */
    .sa-company-drawer .sa-nav-tabs .nav-link:focus-visible,
    .sa-company-drawer .contacts-table-card a:focus-visible,
    .sa-company-drawer .contacts-table-card button:focus-visible,
    .sa-company-drawer .contacts-table-card input:focus-visible,
    .sa-company-drawer .job-row .col-job:focus-visible,
    .sa-company-drawer .table-row .show-detail-team:focus-visible {
        outline: 2px solid #009ef7 !important;
        outline-offset: 2px !important;
    }

    /* ── High-End Operational & Sales Package Architecture (Card + Sub-Card Enterprise Standard) ── */
    .sa-company-drawer .pkg-toolbar-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 14px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 14px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
        margin-bottom: 16px;
    }

    .sa-company-drawer .pkg-toolbar-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .sa-company-drawer .pkg-toolbar-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 17px;
        flex-shrink: 0;
    }

    .sa-company-drawer .pkg-icon-blue {
        background: #f0f7ff;
        color: #009ef7;
        border: 1px solid #dbeafe;
    }

    .sa-company-drawer .pkg-icon-purple {
        background: #f8f5ff;
        color: #7239ea;
        border: 1px solid #ede9fe;
    }

    .sa-company-drawer .pkg-icon-amber {
        background: #fff8dd;
        color: #f6a723;
        border: 1px solid #fef3c7;
    }

    .sa-company-drawer .pkg-toolbar-title {
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.3;
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .sa-company-drawer .pkg-toolbar-subtitle {
        font-size: 11.5px;
        color: #64748b;
        margin-top: 2px;
    }

    .sa-company-drawer .pkg-badge-pill {
        display: inline-flex;
        align-items: center;
        padding: 2px 8px;
        background: #f1f5f9;
        color: #475569;
        font-size: 11px;
        font-weight: 600;
        border-radius: 20px;
    }

    .sa-company-drawer .pkg-action-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        font-size: 12px;
        font-weight: 600;
        border-radius: 8px;
        background: #009ef7;
        color: #ffffff !important;
        border: none;
        transition: all 0.2s ease;
        box-shadow: 0 2px 4px rgba(0, 158, 247, 0.2);
        text-decoration: none !important;
    }

    .sa-company-drawer .pkg-action-btn:hover {
        background: #0086d4;
        color: #ffffff !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 158, 247, 0.28);
    }

    .sa-company-drawer .pkg-overview-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.35fr) minmax(0, 1fr);
        gap: 16px;
        align-items: stretch;
        margin-bottom: 16px;
    }

    @media (max-width: 991.98px) {
        .sa-company-drawer .pkg-overview-grid {
            grid-template-columns: 1fr;
        }
    }

    .sa-company-drawer .pkg-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
        overflow: hidden;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
        margin-bottom: 16px;
    }

    .sa-company-drawer .pkg-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05);
    }

    .sa-company-drawer .pkg-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 13px 18px;
        border-bottom: 1px solid #f1f5f9;
        gap: 10px;
        flex-wrap: wrap;
        background: #ffffff;
    }

    .sa-company-drawer .pkg-tier-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 14px;
        font-weight: 700;
        color: #0f172a;
    }

    .sa-company-drawer .pkg-type-tag {
        display: inline-block;
        font-size: 10.5px;
        font-weight: 600;
        padding: 2px 8px;
        background: #eff6ff;
        color: #009ef7;
        border-radius: 4px;
        border: 1px solid #dbeafe;
    }

    .sa-company-drawer .pkg-type-tag-purple {
        background: #f8f5ff;
        color: #7239ea;
        border: 1px solid #ede9fe;
    }

    .sa-company-drawer .pkg-card-body {
        padding: 16px 18px;
    }

    .sa-company-drawer .pkg-stat-boxes {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 10px;
    }

    @media (max-width: 575.98px) {
        .sa-company-drawer .pkg-stat-boxes {
            grid-template-columns: 1fr;
        }
    }

    .sa-company-drawer .pkg-stat-box {
        background: #f8fafc;
        border: 1px solid #eff2f5;
        border-radius: 8px;
        padding: 10px 12px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        min-width: 0;
    }

    .sa-company-drawer .pkg-stat-primary {
        background: #f0f7ff;
        border-color: #dbeafe;
    }

    .sa-company-drawer .pkg-stat-label {
        font-size: 9.5px;
        font-weight: 700;
        color: #64748b;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        margin-bottom: 4px;
    }

    .sa-company-drawer .pkg-stat-val {
        font-size: 15px;
        font-weight: 700;
        line-height: 1.25;
        display: flex;
        align-items: center;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .sa-company-drawer .pkg-stat-sub {
        font-size: 11px;
        color: #94a3b8;
        margin-top: 3px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .sa-company-drawer .pkg-status-dot {
        display: inline-block;
        width: 6px;
        height: 6px;
        border-radius: 50%;
    }

    .sa-company-drawer .pkg-info-banner {
        display: flex;
        align-items: flex-start;
        padding: 10px 12px;
        background: #f8fafc;
        border: 1px dashed #e2e8f0;
        border-radius: 8px;
        font-size: 11px;
        color: #64748b;
        line-height: 1.45;
    }

    .sa-company-drawer .pkg-table-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 18px;
        border-bottom: 1px solid #f1f5f9;
        gap: 12px;
        flex-wrap: wrap;
    }

    .sa-company-drawer .pkg-search-box {
        display: inline-flex;
        align-items: center;
        position: relative;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 0 10px;
        height: 34px;
        width: 250px;
        max-width: 100%;
        transition: all 0.2s ease;
    }

    .sa-company-drawer .pkg-search-box:focus-within {
        background: #ffffff;
        border-color: #009ef7;
        box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12);
    }

    .sa-company-drawer .pkg-search-box i.fa-search {
        color: #94a3b8;
        font-size: 11px;
        margin-right: 6px;
    }

    .sa-company-drawer .pkg-search-input {
        border: none;
        outline: none;
        background: transparent;
        font-size: 11.5px;
        font-family: 'Poppins', sans-serif;
        width: 100%;
        color: #1e293b;
    }

    .sa-company-drawer .pkg-search-clear {
        background: none;
        border: none;
        color: #94a3b8;
        font-size: 11px;
        cursor: pointer;
        padding: 0 4px;
    }

    .sa-company-drawer .pkg-search-clear:hover {
        color: #475569;
    }

    .sa-company-drawer .pkg-rules-table thead th {
        background: #f8fafc;
        font-size: 10.5px;
        font-weight: 700;
        letter-spacing: 0.5px;
        color: #64748b;
        text-transform: uppercase;
        border-bottom: 1px solid #e2e8f0;
        padding: 11px 14px;
        white-space: nowrap;
    }

    .sa-company-drawer .pkg-rules-table tbody td {
        padding: 11px 14px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        font-size: 12px;
        color: #334155;
    }

    .sa-company-drawer .pkg-rules-table tbody tr:hover {
        background-color: #fbfcfe;
    }

    .sa-company-drawer .pkg-event-icon-box {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        flex-shrink: 0;
    }

    .sa-company-drawer .pkg-rules-scroll-wrap {
        max-height: 480px;
        overflow-y: auto;
        scrollbar-width: thin;
    }

    /* ── Top-Up Coins & Financial Engine Sub-System ── */
    .sa-company-drawer .pkg-topup-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.25fr) minmax(0, 1fr);
        gap: 16px;
        align-items: stretch;
        margin-bottom: 16px;
    }

    @media (max-width: 991.98px) {
        .sa-company-drawer .pkg-topup-grid {
            grid-template-columns: 1fr;
        }
    }

    .sa-company-drawer .pkg-section-title {
        font-size: 13px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .sa-company-drawer .pkg-calc-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px dashed #eff2f5;
        font-size: 12px;
    }

    .sa-company-drawer .pkg-calc-row:last-child {
        border-bottom: none;
    }

    .sa-company-drawer .pkg-calc-label {
        color: #64748b;
        font-weight: 500;
    }

    .sa-company-drawer .pkg-calc-val {
        color: #0f172a;
        font-weight: 700;
    }

    .sa-company-drawer .pkg-calc-total {
        background: #f8fafc;
        border-radius: 8px;
        padding: 10px 14px;
        margin-top: 10px;
        border: 1px solid #e2e8f0;
    }

    /* ── Migration 3-Step Flow Strip ── */
    .sa-company-drawer .pkg-step-strip {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 16px 20px;
        margin-bottom: 16px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
    }

    .sa-company-drawer .pkg-step-flow {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
    }

    @media (max-width: 991.98px) {
        .sa-company-drawer .pkg-step-flow {
            grid-template-columns: 1fr;
        }
    }

    .sa-company-drawer .pkg-step-box {
        background: #f8fafc;
        border: 1px solid #eff2f5;
        border-radius: 8px;
        padding: 14px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 10px;
    }

    .sa-company-drawer .pkg-step-head {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .sa-company-drawer .pkg-step-num {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: #009ef7;
        color: #ffffff;
        font-size: 11px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .sa-company-drawer .pkg-step-title {
        font-size: 12px;
        font-weight: 700;
        color: #0f172a;
    }

    .sa-company-drawer .pkg-step-desc {
        font-size: 11px;
        color: #64748b;
        line-height: 1.45;
    }

    .sa-company-drawer .pkg-copy-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 10px;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
        color: #2563eb;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none !important;
    }

    .sa-company-drawer .pkg-copy-pill:hover {
        background: #eff6ff;
        border-color: #009ef7;
        color: #009ef7;
    }

    .sa-company-drawer .pkg-template-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 10px;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
        color: #0f172a;
        cursor: pointer;
        text-decoration: none !important;
        transition: all 0.2s ease;
    }

    .sa-company-drawer .pkg-template-btn:hover {
        background: #f1f5f9;
        border-color: #94a3b8;
        color: #009ef7;
    }

    .sa-company-drawer .pkg-migration-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
        align-items: stretch;
    }

    @media (max-width: 1199.98px) {
        .sa-company-drawer .pkg-migration-grid {
            grid-template-columns: 1fr;
        }
    }

    /* ── Settings Sub-Cards Grid ── */
    .sa-company-drawer .pkg-setting-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
        align-items: stretch;
    }

    @media (max-width: 991.98px) {
        .sa-company-drawer .pkg-setting-grid {
            grid-template-columns: 1fr;
        }
    }

    .sa-company-drawer .pkg-setting-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 18px 20px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 14px;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .sa-company-drawer .pkg-setting-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05);
    }

    .sa-company-drawer .pkg-setting-top {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .sa-company-drawer .pkg-setting-icon {
        width: 38px;
        height: 38px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        flex-shrink: 0;
    }

    /* ── Boost Keyword Suggestions ── */
    .sa-company-drawer .pkg-suggest-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 10px;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 500;
        color: #475569;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .sa-company-drawer .pkg-suggest-pill:hover {
        background: #e0f2fe;
        border-color: #7dd3fc;
        color: #0369a1;
        transform: translateY(-1px);
    }
</style>
<div class="card-body position-relative px-0 py-0 sa-company-drawer" id="kt_activities_body">
    <input type="hidden" name="hid_client_id" id="hid_client_id" value="{{ $client->id }}" />
    <input type="hidden" name="hid_compant_id" id="hid_compant_id" value="{{ $client->company_id }}" />
    <!--begin::Content-->
    <div id="kt_activities_scroll" class="position-relative scroll-y pt-0" data-kt-scroll="true"
        data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_activities_body"
        data-kt-scroll-dependencies="#kt_activities_header, #kt_activities_footer" data-kt-scroll-offset="5px">

        @php
            $bgTextDecore = $strVal = '';
            $str = trim(@$client->company_name);
            $str = $strVal = strtolower($str[0] ?? 'c');
            $strAscii = ord($str);
            if ($strAscii >= 97 && $strAscii <= 101) {
                $bgTextDecore = 'bg-light-danger text-danger';
            } elseif ($strAscii >= 102 && $strAscii <= 106) {
                $bgTextDecore = 'bg-light-warning text-warning';
            } elseif ($strAscii >= 107 && $strAscii <= 111) {
                $bgTextDecore = 'bg-light-primary text-primary';
            } elseif ($strAscii >= 112 && $strAscii <= 116) {
                $bgTextDecore = 'bg-light-success text-success';
            } elseif ($strAscii >= 117 && $strAscii <= 121) {
                $bgTextDecore = 'bg-dark text-secondary';
            } else {
                $bgTextDecore = 'bg-light-secondary text-dark';
            }

            $background_image = '';
            if (!empty(@$client->logo) && strlen(@$client->logo) > 10) {
                $background_image = @$client->logo;
            }

            $industries_name = '';
            if (trim($client->industries) != '') {
                $industries_name = @$client->industries_data[0]->ind_name;
            }

            $tags = [];
            $tags_type = @$client->clients()->where('self_company', 1)->get();
            if (@$tags_type && count($tags_type) > 0) {
                $tags = explode(',', @$tags_type[0]->tags);
            }
        @endphp

        <!--begin::Profile Card (Consultancy Reference Parity)-->
        <div class="profile-card">
            <div class="profile-header-wrap">
                <!-- Logo Avatar -->
                <div class="logo-circle">
                    @if (!empty($background_image))
                        <img src="{{ str_replace('index.php', '', @$background_image) }}" alt="{{ @$client->company_name }}" />
                    @else
                        <div class="d-flex align-items-center justify-content-center w-100 h-100 fw-bolder fs-3 {{ $bgTextDecore }} rounded-2">
                            {{ strtoupper(@$strVal) }}
                        </div>
                    @endif
                    @if (@$account->accountVerify)
                        <div class="verified-check" title="Verified Organization" data-bs-toggle="tooltip">
                            <i class="fa-solid fa-check"></i>
                        </div>
                    @endif
                </div>

                <!-- Info & Metadata -->
                <div class="profile-info">
                    <div class="profile-name-row">
                        <h2 class="profile-name">{{ ucwords(@$client->company_name) }}</h2>
                        @if (@$account->accountVerify)
                            <span class="verified-badge"><i class="fa-solid fa-circle-check"></i> Verified</span>
                        @else
                            <span class="badge badge-light-warning fs-8 py-1 px-2">Verification Pending</span>
                        @endif
                    </div>

                    <div class="profile-meta">
                        @if (!empty($industries_name))
                            <div class="meta-item">
                                <i class="fa-solid fa-industry"></i>
                                <span>{{ $industries_name }}</span>
                            </div>
                            <span class="meta-divider">•</span>
                        @endif
                        <div class="meta-item">
                            <i class="fa-solid fa-location-dot"></i>
                            <span>{{ ucwords($city_name) }}, {{ ucwords($country_name) }}</span>
                        </div>
                        @if (!empty(@$client->website))
                            <span class="meta-divider">•</span>
                            <div class="meta-item">
                                <i class="fa-solid fa-globe"></i>
                                <a href="{{ (strpos(@$client->website, 'http') === 0) ? @$client->website : 'https://'.@$client->website }}" target="_blank" class="text-primary text-hover-underline">{{ @$client->website }}</a>
                            </div>
                        @endif
                    </div>

                    @if (!empty($tags))
                        <div class="service-tags">
                            @foreach ($tags as $val)
                                @if (trim($val) != '')
                                    <span class="tag"><i class="fa-solid fa-tag"></i> {{ ucwords(trim($val)) }}</span>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Right Actions & Status Badges -->
                <div class="profile-right">
                    <div class="status-row">
                        @if (@$users->status === 'active')
                            <span class="status-badge status-badge-active">
                                <span class="status-dot"></span> Active
                            </span>
                        @else
                            <span class="status-badge status-badge-suspend">
                                <span class="status-dot"></span> Suspend
                            </span>
                        @endif

                        <a href="javascript:void(0);" id="app_meeting_schedule"
                            class="btn btn-sm btn-light btn-active-primary fw-bolder d-inline-flex align-items-center gap-2"
                            data-toggle="modal" data-target="#interview_schedule" rel="{{ @$client->id }}">
                            <i class="fa-solid fa-calendar-check text-primary fs-7"></i>
                            <span>Meeting</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Profile Card-->
        @php
            $currentYear = date('Y');
            $foundedYear = @$client->founded;
            $foundedYearsAgo = ($foundedYear && is_numeric($foundedYear) && $foundedYear > 1900) ? ($currentYear - $foundedYear) : null;

            $createdYear = date('Y', strtotime(@$client->created_at));
            $joinedYearsAgo = $currentYear - $createdYear;
        @endphp

        <!--begin::KPI Metric Strip (Consultancy Reference Parity)-->
        <div class="kpi-row">
            <!-- Box 1: Active Requisitions -->
            <div class="kpi-box">
                <div class="kpi-header">
                    <div class="kpi-icon-box kpi-icon-blue">
                        <i class="fa-solid fa-briefcase"></i>
                    </div>
                    <div class="kpi-label">Active Jobs</div>
                </div>
                <div class="kpi-number">{{ number_format(@$active_requisition) }}</div>
                <div class="kpi-subtext">Open requisitions</div>
            </div>

            <!-- Box 2: Total Employees -->
            <div class="kpi-box">
                <div class="kpi-header">
                    <div class="kpi-icon-box kpi-icon-green">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <div class="kpi-label">Employees</div>
                </div>
                <div class="kpi-number">{{ number_format(@$client->company_size) }}</div>
                <div class="kpi-subtext">Team headcount</div>
            </div>

            <!-- Box 3: Company Type -->
            <div class="kpi-box">
                <div class="kpi-header">
                    <div class="kpi-icon-box kpi-icon-purple">
                        <i class="fa-solid fa-building"></i>
                    </div>
                    <div class="kpi-label">Company Type</div>
                </div>
                <div class="kpi-number" style="font-size: 15px; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ ($client->company_type > 0 || $client->company_type == 0) ? ($companyType[@$client->company_type] ?? 'N/A') : 'N/A' }}">
                    {{ ($client->company_type > 0 || $client->company_type == 0) ? ($companyType[@$client->company_type] ?? 'N/A') : 'N/A' }}
                </div>
                <div class="kpi-subtext">
                    @if ($foundedYearsAgo !== null)
                        Founded {{ $foundedYearsAgo }}y ago
                    @else
                        Org Classification
                    @endif
                </div>
            </div>

            <!-- Box 4: Tenure / Joined -->
            <div class="kpi-box">
                <div class="kpi-header">
                    <div class="kpi-icon-box kpi-icon-orange">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>
                    <div class="kpi-label">Member Since</div>
                </div>
                <div class="kpi-number">{{ $joinedYearsAgo }} <span class="fs-7 text-muted fw-normal">yr{{ $joinedYearsAgo > 1 ? 's' : '' }}</span></div>
                <div class="kpi-subtext">{{ date('M Y', strtotime(@$client->created_at)) }}</div>
            </div>

            <!-- Box 5: Contact Persons -->
            <div class="kpi-box">
                <div class="kpi-header">
                    <div class="kpi-icon-box kpi-icon-teal">
                        <i class="fa-solid fa-address-book"></i>
                    </div>
                    <div class="kpi-label">Contacts</div>
                </div>
                <div class="kpi-number">{{ count($client_contact_info ?? []) }}</div>
                <div class="symbol-group symbol-hover mt-1">
                    @if ($client_contact_info)
                        @foreach ($client_contact_info as $cci)
                            @php
                                $contact_avatar = str_replace('index.php', '', url('public/avatar.png'));
                            @endphp
                            <div class="symbol symbol-25px symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ @$cci->contact_person_name }}">
                                <img alt="{{ @$cci->contact_person_name }}" src="{{ $contact_avatar }}">
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
        <!--end::KPI Metric Strip-->

        <!--begin::Tabs Container-->
        <div class="card card-custom border-0 bg-transparent shadow-none">
            <!--Begin::Tabs Header-->
            <div class="sa-tabs-bar">
                <ul class="nav nav-tabs sa-nav-tabs" role="tablist" aria-label="Company Drawer Tabs">
                    <li class="nav-item" role="presentation"> <a class="nav-link active" data-toggle="tab" role="tab" id="tab-overview" aria-controls="overview" aria-selected="true" tabindex="0" href="#overview">Overview</a> </li>
                    <li class="nav-item TeamListOpen" role="presentation"> <a class="nav-link" data-toggle="tab" role="tab" id="tab-contacts" aria-controls="contacts" aria-selected="false" tabindex="-1" href="#contacts">Contacts <span class="tab-count">{{ count($client_contact_info ?? []) }}</span></a> </li>
                    <li class="nav-item JobListOpen" role="presentation"> <a class="nav-link" data-toggle="tab" role="tab" id="tab-requisitions" aria-controls="requisitions" aria-selected="false" tabindex="-1" href="#requisitions">Jobs <span class="tab-count">{{ $active_requisition ?? 0 }}</span></a> </li>
                    <li class="nav-item" role="presentation"> <a class="nav-link" data-toggle="tab" role="tab" id="tab-timeline" aria-controls="timeline" aria-selected="false" tabindex="-1" href="#timeline">Timeline</a> </li>
                    <li class="nav-item" role="presentation"> <a class="nav-link" data-toggle="tab" role="tab" id="tab-packages" aria-controls="packages" aria-selected="false" tabindex="-1" href="#packages">Op. Package <span class="tab-count">{{ count($company_coins ?? []) }}</span></a> </li>
                    <li class="nav-item" role="presentation"> <a class="nav-link" data-toggle="tab" role="tab" id="tab-invoices" aria-controls="invoices" aria-selected="false" tabindex="-1" href="#invoices">Sales Package <span class="tab-count">{{ count($company_coins_sales ?? []) }}</span></a> </li>
                    <li class="nav-item" role="presentation"> <a class="nav-link" data-toggle="tab" role="tab" id="tab-topupcoins" aria-controls="topupcoins" aria-selected="false" tabindex="-1" href="#topupcoins">Top Up Coins</a> </li>
                    <li class="nav-item" role="presentation"> <a class="nav-link" data-toggle="tab" role="tab" id="tab-transactionhistory" aria-controls="transactionhistory" aria-selected="false" tabindex="-1" href="#transactionhistory">History</a> </li>
                    <li class="nav-item" role="presentation"> <a class="nav-link" data-toggle="tab" role="tab" id="tab-migration" aria-controls="migration" aria-selected="false" tabindex="-1" href="#migration">Migration</a> </li>
                    <li class="nav-item" role="presentation"> <a class="nav-link" data-toggle="tab" role="tab" id="tab-codeSetting" aria-controls="codeSetting" aria-selected="false" tabindex="-1" href="#codeSetting">Setting</a> </li>
                    <li class="nav-item" role="presentation"> <a class="nav-link" data-toggle="tab" role="tab" id="tab-boostkeyword" aria-controls="boostkeyword" aria-selected="false" tabindex="-1" href="#boostkeyword">Boost Keywords</a> </li>            
                </ul>
            </div>
            <!--end::Tabs Header-->
            <!--Begin::Body-->
            <div class="card-body px-0 py-0">
                <div class="tab-content pt-0">
                    <!--begin::Overview Content-->
                    <div class="tab-pane active" id="overview" role="tabpanel" aria-labelledby="tab-overview">
                        <div class="content-grid">
                            <!-- Left Column: About the Company -->
                            <div style="display: flex; flex-direction: column; gap: 16px;">
                                <div class="content-card">
                                    <div class="card-header-side" style="justify-content: space-between;">
                                        <div style="display: flex; align-items: center; gap: 10px;">
                                            <div class="card-header-icon">
                                                <i class="fas fa-info-circle"></i>
                                            </div>
                                            <h3 class="card-header-title">About the Company</h3>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('superadmin.company.edit_company', \Crypt::encryptString($client->id)) }}"
                                                class="btn btn-sm btn-primary" style="padding: 4px 10px; font-size: 11px;">Edit Profile</a>
                                            @if (in_array('edit_clients', $userPermissions))
                                                <a href="{{ route('service_company.clients.edit', $client->id) }}"
                                                    class="btn btn-sm btn-light-primary" style="padding: 4px 10px; font-size: 11px;">Edit Client</a>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="about-text" id="aboutText">
                                        @php
                                            $description = trim(@$client->description);
                                        @endphp
                                        @if (!empty($description))
                                            {!! $description !!}
                                        @else
                                            <p class="text-muted">No description provided yet. Use the Edit Profile action to specify company details, offerings, and organizational background.</p>
                                        @endif
                                    </div>
                                    @php
                                        $tags = @$client->tags;
                                        $tags = !empty($tags) ? explode(',', $tags) : [];
                                    @endphp
                                    @if (!empty($tags))
                                        <div class="mt-4 pt-3 border-top d-flex flex-wrap gap-2 align-items-center">
                                            <span class="fs-8 fw-bolder text-muted text-uppercase">Specialties:</span>
                                            @foreach ($tags as $tag)
                                                @if (trim($tag))
                                                    <span class="badge bg-light-primary text-primary fs-8 px-2 py-1">{{ ucwords(trim($tag)) }}</span>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Right Column: Company Information & Commercials -->
                            <div style="display: flex; flex-direction: column; gap: 16px;">
                                <!-- Card 1: Company Information -->
                                <div class="content-card">
                                    <div class="card-header-side">
                                        <div class="card-header-icon">
                                            <i class="fas fa-building"></i>
                                        </div>
                                        <h3 class="card-header-title">Company Information</h3>
                                    </div>
                                    <div class="info-list">
                                        <div class="info-row">
                                            <span class="info-label">Headquarters</span>
                                            <span class="info-value">{{ ucwords($city_name) }}{{ !empty($country_name) ? ', ' . ucwords($country_name) : '' }}</span>
                                        </div>
                                        <div class="info-row">
                                            <span class="info-label">Website</span>
                                            <span class="info-value">
                                                @if (!empty($client->website))
                                                    <a href="{{ (strpos($client->website, 'http') === 0) ? $client->website : 'https://' . $client->website }}" target="_blank">
                                                        {{ $client->website }} <i class="fas fa-external-link-alt"></i>
                                                    </a>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </span>
                                        </div>
                                        <div class="info-row">
                                            <span class="info-label">Email</span>
                                            <span class="info-value">
                                                @if (!empty($client->email))
                                                    <a href="mailto:{{ $client->email }}">{{ $client->email }}</a>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </span>
                                        </div>
                                        <div class="info-row">
                                            <span class="info-label">Company ID</span>
                                            <span class="info-value">CMP-{{ @$client->id }}</span>
                                        </div>
                                        <div class="info-row">
                                            <span class="info-label">Founded</span>
                                            <span class="info-value">{{ @$client->founded ?: 'N/A' }}</span>
                                        </div>
                                        <div class="info-row">
                                            <span class="info-label">Number of Employees</span>
                                            <span class="info-value">{{ @$client->company_sizes ?: (@$client->company_size ?: '0') }} + employees</span>
                                        </div>
                                        <div class="info-row">
                                            <span class="info-label">Connected On</span>
                                            <span class="info-value">{{ date('d M, Y', strtotime(@$client->created_at)) }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card 2: Commercials -->
                                <div class="content-card">
                                    <div class="card-header-side">
                                        <div class="card-header-icon">
                                            <i class="fas fa-dollar-sign"></i>
                                        </div>
                                        <h3 class="card-header-title">Commercials</h3>
                                    </div>
                                    <div class="commercial-item">
                                        <div class="commercial-icon purple">
                                            <i class="fas fa-user-tie"></i>
                                        </div>
                                        <div>
                                            <div class="commercial-title">Fulltime Hiring Services</div>
                                            <div class="commercial-desc">
                                                {{ @$package_active_details->name ?? 'Standard Package' }} &bull; Operational Plan
                                            </div>
                                        </div>
                                    </div>
                                    <div class="commercial-item">
                                        <div class="commercial-icon orange">
                                            <i class="fas fa-clock"></i>
                                        </div>
                                        <div>
                                            <div class="commercial-title">Contract Hiring Services</div>
                                            <div class="commercial-desc">
                                                {{ count($company_coins_sales ?? []) }} Sales Orders &bull; Top-up Balance Available
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Key Contacts Card below Grid -->
                        <div class="contacts-card">
                            <div class="contacts-header">
                                <h3 class="contacts-title">Key Contacts ({{ count($client_contact_info ?? []) }})</h3>
                                <a href="#contacts" class="view-all TeamListOpen" data-toggle="tab">
                                    View All Contacts <i class="fas fa-chevron-right"></i>
                                </a>
                            </div>
                            <div class="contacts-grid">
                                @if (!empty($client_contact_info) && count($client_contact_info) > 0)
                                    @foreach ($client_contact_info as $cci)
                                        @php
                                            $initials = '';
                                            $words = explode(' ', trim(@$cci->contact_person_name));
                                            foreach ($words as $w) {
                                                if (!empty($w)) $initials .= strtoupper($w[0]);
                                            }
                                            $initials = substr($initials ?: 'CP', 0, 2);
                                        @endphp
                                        <div class="contact-item">
                                            <div class="contact-avatar">{{ $initials }}</div>
                                            <div class="contact-body">
                                                <div class="contact-name-row">
                                                    <span class="contact-name">{{ @$cci->contact_person_name }}</span>
                                                    <span class="badge badge-purple" style="padding: 2px 8px; font-size: 10px; background: #f8f5ff; color: #7239ea;">Primary</span>
                                                </div>
                                                <div class="contact-role">{{ @$cci->designation ?: 'Contact Person' }}</div>
                                                <div class="contact-email">{{ @$cci->email }}</div>
                                                <div class="contact-phone">{{ @$cci->phone }}</div>
                                            </div>
                                            <div class="contact-actions-row">
                                                @if (!empty($cci->email))
                                                    <a class="contact-btn" href="mailto:{{ $cci->email }}" title="Email"><i class="fas fa-envelope"></i></a>
                                                @endif
                                                @if (!empty($cci->phone))
                                                    <a class="contact-btn" href="tel:{{ $cci->phone }}" title="Call"><i class="fas fa-phone"></i></a>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-muted p-3">No contact persons registered yet.</div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <!--end::Overview Content-->
                    <!--begin::Contacts Content-->
                    <div class="tab-pane" id="contacts" role="tabpanel" aria-labelledby="tab-contacts">
                        <div class="contacts-table-card">
                            <div class="contacts-table-header">
                                <div>
                                    <div class="contacts-table-title">
                                        Contacts <span id="contactsHeadingCount">({{ count($client_contact_info ?? []) }})</span>
                                    </div>
                                    <div class="contacts-table-subtitle">
                                        Manage and view all contacts associated with this company.
                                    </div>
                                </div>
                                <div class="contacts-table-actions">
                                    <div class="search-box">
                                        <i class="fas fa-search" aria-hidden="true"></i>
                                        <input type="text" id="inputSearch" placeholder="Search contacts..." autocomplete="off" aria-label="Search contacts" />
                                    </div>
                                    <button type="button" class="btn btn-outline" id="contactsFilterBtn" aria-label="Filter contacts">
                                        <i class="fas fa-filter" aria-hidden="true"></i> Filters
                                    </button>
                                    <a href="{{ route('superadmin.team.create') }}" class="btn btn-primary" id="addContactBtn" aria-label="Add new contact">
                                        <i class="fas fa-plus" aria-hidden="true"></i> Add Contact
                                    </a>
                                </div>
                            </div>
                            <div class="table-wrapper position-relative">
                                <div class="loadingMessage loadingMessageteamsListajax" style="display: none; position: absolute; top: 14px; left: 50%; transform: translateX(-50%); z-index: 10; background: rgba(255,255,255,0.95); padding: 8px 18px; border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); font-size: 12px; font-weight: 600; color: #009ef7;">
                                    <i class="fa fa-spinner fa-spin me-2" aria-hidden="true"></i> Loading contacts...
                                </div>
                                <table class="data-table" id="contactListss">
                                    <thead>
                                        <tr>
                                            <th>Contact <i class="fas fa-sort" aria-hidden="true"></i></th>
                                            <th>Department <i class="fas fa-sort"></i></th>
                                            <th>Email <i class="fas fa-sort"></i></th>
                                            <th>Last Contacted <i class="fas fa-sort"></i></th>
                                            <th>Status <i class="fas fa-sort"></i></th>
                                            <th style="text-align: right;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="teamsListajax"> </tbody>
                                </table>
                            </div>
                            <div id="paginationTeams"></div>
                        </div>
                    </div>
                    <!--end::Contacts Content-->
                    <!--begin::invoices Content-->
                    <div class="tab-pane" id="invoices" role="tabpanel" aria-labelledby="tab-invoices">
                        @php
                            $salesFreePack = DB::table('coin_packages')
                                ->join('coin_package_types', 'coin_package_types.id', '=', 'coin_packages.coin_package_type_id')
                                ->where('coin_packages.custome_pck', 2)
                                ->where('coin_packages.event_type', '=', 'sales')
                                ->select('coin_packages.*', 'coin_package_types.title')
                                ->first();

                            $salesFreeStatus = DB::table('company_coins')
                                ->where('company_id', $client->id)
                                ->where('event_type', 'sales')
                                ->value('free_pack_status') ?? 'inactive';
                        @endphp

                        <!-- Tier 1: Section Header Toolbar Card -->
                        <div class="pkg-toolbar-card">
                            <div class="pkg-toolbar-left">
                                <div class="pkg-toolbar-icon pkg-icon-purple">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <div>
                                    <div class="pkg-toolbar-title">
                                        Sales Package & Credit Architecture
                                        <span class="pkg-badge-pill">{{ count($company_coins_sales ?? []) }} Active Assigned</span>
                                    </div>
                                    <div class="pkg-toolbar-subtitle">
                                        Manage sales quota allocation, business development wallet balances, trial tier onboarding, and lead deduction rules
                                    </div>
                                </div>
                            </div>
                            <div class="pkg-toolbar-right">
                                <a href="{{ url('super-admin/custompackages/create/' . @$client->id) }}?packageType={{ encrypt('sales') }}" class="pkg-action-btn" style="background: #7239ea; box-shadow: 0 2px 4px rgba(114, 57, 234, 0.2);">
                                    <i class="fas fa-plus"></i>
                                    <span>New Sales Package</span>
                                </a>
                            </div>
                        </div>

                        <!-- Tier 2: Overview Dual-Card Grid (Current Plan Hero + Onboarding Free Tier) -->
                        <div class="pkg-overview-grid">
                            <!-- Sub-Card A: Current Active Sales Package -->
                            @if (!empty($company_coins_sales) && count($company_coins_sales) > 0)
                                @foreach ($company_coins_sales as $package_sales)
                                    @php
                                        $pkgTitle = is_array($package_sales) ? ($package_sales['title'] ?? '') : ($package_sales->title ?? '');
                                        $pkgStatus = is_array($package_sales) ? ($package_sales['status'] ?? '') : ($package_sales->status ?? '');
                                        $pkgStartDate = is_array($package_sales) ? ($package_sales['start_date'] ?? '') : ($package_sales->start_date ?? '');
                                        $pkgEndDate = is_array($package_sales) ? ($package_sales['end_date'] ?? '') : ($package_sales->end_date ?? '');
                                        $pkgOrderCoins = (float)(is_array($package_sales) ? ($package_sales['order_coins'] ?? 0) : ($package_sales->order_coins ?? 0));
                                        $pkgCoins = (float)(is_array($package_sales) ? ($package_sales['coins'] ?? 0) : ($package_sales->coins ?? 0));
                                        $isActivePlan = strtotime($pkgEndDate) >= strtotime(date('Y-m-d')) && $pkgStatus == 'valid';

                                        $daysLeft = 0;
                                        if (!empty($pkgEndDate)) {
                                            $diff = strtotime($pkgEndDate) - time();
                                            $daysLeft = (int) ceil($diff / 86400);
                                        }

                                        $pct = $pkgOrderCoins > 0 ? min(100, max(0, round(($pkgCoins / $pkgOrderCoins) * 100))) : 0;
                                    @endphp
                                    <div class="pkg-card mb-0">
                                        <div class="pkg-card-header">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="pkg-tier-badge">
                                                    <i class="fas fa-gem text-primary"></i>
                                                    <span class="fw-bold">{{ $pkgTitle ?: 'Custom Sales Plan' }}</span>
                                                </div>
                                                <span class="pkg-type-tag pkg-type-tag-purple">Sales Engine</span>
                                            </div>
                                            <div>
                                                @if ($isActivePlan)
                                                    <span class="badge bg-light-success text-success px-3 py-2 fw-bold fs-8">
                                                        <span class="pkg-status-dot bg-success me-1"></span> Active Plan
                                                    </span>
                                                @else
                                                    <span class="badge bg-light-danger text-danger px-3 py-2 fw-bold fs-8">
                                                        <span class="pkg-status-dot bg-danger me-1"></span> Expired
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="pkg-card-body">
                                            <div class="pkg-stat-boxes">
                                                <!-- Stat 1: Remaining Coins -->
                                                <div class="pkg-stat-box" style="background: #f8f5ff; border-color: #ede9fe;">
                                                    <div class="pkg-stat-label">REMAINING COINS</div>
                                                    <div class="pkg-stat-val" style="color: #7239ea;">
                                                        <i class="fas fa-coins me-1 fs-6 text-warning"></i>
                                                        {{ number_format($pkgCoins, 1) }}
                                                    </div>
                                                    <div class="pkg-stat-sub">Live sales balance</div>
                                                </div>
                                                <!-- Stat 2: Total Granted -->
                                                <div class="pkg-stat-box">
                                                    <div class="pkg-stat-label">TOTAL ALLOCATED</div>
                                                    <div class="pkg-stat-val text-dark">
                                                        {{ number_format($pkgOrderCoins) }}
                                                    </div>
                                                    <div class="pkg-stat-sub">Purchased capacity</div>
                                                </div>
                                                <!-- Stat 3: Validity Period -->
                                                <div class="pkg-stat-box">
                                                    <div class="pkg-stat-label">VALIDITY WINDOW</div>
                                                    <div class="pkg-stat-val text-dark fs-8 fw-bold">
                                                        {{ $pkgStartDate ? date('d M Y', strtotime($pkgStartDate)) : '—' }}
                                                        <span class="text-muted fw-normal px-1">to</span>
                                                        {{ $pkgEndDate ? date('d M Y', strtotime($pkgEndDate)) : '—' }}
                                                    </div>
                                                    <div class="pkg-stat-sub">
                                                        @if ($daysLeft > 0)
                                                            <span class="text-success fw-semibold"><i class="fas fa-check-circle text-success me-1 fs-9"></i>{{ $daysLeft }} days left</span>
                                                        @elseif ($daysLeft === 0)
                                                            <span class="text-warning fw-semibold"><i class="fas fa-exclamation-circle text-warning me-1 fs-9"></i>Expires today</span>
                                                        @else
                                                            <span class="text-danger fw-semibold"><i class="fas fa-times-circle text-danger me-1 fs-9"></i>Expired {{ abs($daysLeft) }}d ago</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="pkg-progress-wrap mt-3">
                                                <div class="d-flex justify-content-between align-items-center mb-1 fs-8">
                                                    <span class="text-muted fw-semibold">Quota Utilization</span>
                                                    <span class="fw-bold text-dark">{{ $pkgCoins > $pkgOrderCoins ? 'Top-up Balance Augmented' : ($pct . '% available') }}</span>
                                                </div>
                                                <div class="progress" style="height: 6px; border-radius: 4px; background: #e2e8f0;">
                                                    <div class="progress-bar" role="progressbar" style="width: {{ min(100, $pct) }}%; border-radius: 4px; background: #7239ea;"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="pkg-card d-flex flex-column align-items-center justify-content-center p-6 text-center mb-0" style="min-height: 180px;">
                                    <div class="pkg-empty-icon mb-2">
                                        <i class="fas fa-chart-pie fa-2x text-muted opacity-50"></i>
                                    </div>
                                    <div class="fw-bold fs-6 text-gray-800">No Sales Package Assigned</div>
                                    <div class="fs-8 text-muted mt-1 mb-3">Create and assign a custom sales package to allocate credits for sales outreach, lead discovery, and BD tools.</div>
                                    <a href="{{ url('super-admin/custompackages/create/' . @$client->id) }}?packageType={{ encrypt('sales') }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-plus"></i> Assign Sales Package
                                    </a>
                                </div>
                            @endif

                            <!-- Sub-Card B: Onboarding / Free Trial Sales Tier -->
                            <div class="pkg-card mb-0">
                                <div class="pkg-card-header">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="pkg-toolbar-icon pkg-icon-amber" style="width: 32px; height: 32px; font-size: 13px;">
                                            <i class="fas fa-gift"></i>
                                        </div>
                                        <div>
                                            <span class="fw-bold fs-7 text-dark">{{ $salesFreePack->title ?? 'Free Trial' }} Tier</span>
                                            <span class="pkg-type-tag ms-1">Onboarding Default</span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="form-check form-switch form-switch-sm form-check-custom form-check-solid m-0">
                                            <input type="checkbox"
                                                class="js-switch form-check-input checkStatusSales cursor-pointer"
                                                name="checkStatusSales"
                                                id="switchSalesFree"
                                                @if ($salesFreeStatus == 'active') checked @endif />
                                        </div>
                                        <span class="badge {{ $salesFreeStatus == 'active' ? 'bg-light-success text-success' : 'bg-light text-muted' }} px-2 py-1 fw-bold fs-8 checkStatusSales_text">
                                            {{ $salesFreeStatus == 'active' ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="pkg-card-body">
                                    <div class="pkg-stat-boxes" style="grid-template-columns: repeat(2, 1fr);">
                                        <div class="pkg-stat-box">
                                            <div class="pkg-stat-label">TRIAL DURATION</div>
                                            <div class="pkg-stat-val text-dark">
                                                <i class="fas fa-clock text-info me-1 fs-6"></i>
                                                {{ !empty($salesFreePack->days) ? $salesFreePack->days : 0 }} Days
                                            </div>
                                            <div class="pkg-stat-sub">Standard trial period</div>
                                        </div>
                                        <div class="pkg-stat-box">
                                            <div class="pkg-stat-label">FREE SALES COINS</div>
                                            <div class="pkg-stat-val text-dark">
                                                <i class="fas fa-coins text-warning me-1 fs-6"></i>
                                                {{ number_format((float)(@$salesFreePack->coins ?? 0)) }} Coins
                                            </div>
                                            <div class="pkg-stat-sub">Complimentary allowance</div>
                                        </div>
                                    </div>

                                    <div class="pkg-info-banner mt-3">
                                        <i class="fas fa-info-circle text-primary me-2"></i>
                                        <span>Controls whether this workspace is granted initial sales coins upon provisioning to explore BD and marketplace features.</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tier 3: Credit Usage Overview per Activity (High Density Table Sub-Card) -->
                        @if (!empty($company_coins_sales))
                            @foreach ($company_coins_sales as $package_sales)
                                @php
                                    $eventsSales = is_array($package_sales) ? ($package_sales['coin_order_events'] ?? []) : ($package_sales->coin_order_events ?? []);
                                @endphp
                                <div class="pkg-card mt-4">
                                    <div class="pkg-table-header">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="pkg-toolbar-icon pkg-icon-purple" style="width: 32px; height: 32px; font-size: 13px;">
                                                <i class="fas fa-sliders-h"></i>
                                            </div>
                                            <div>
                                                <h4 class="fw-bold text-dark m-0 fs-6">
                                                    Sales Credit Usage Rules by Activity
                                                    <span class="pkg-badge-pill ms-2" id="salesRulesCountBadge">
                                                        {{ count($eventsSales) }} Rules Configured
                                                    </span>
                                                </h4>
                                                <div class="text-muted fs-8">Authoritative deduction schedule governing sales events and outreach consumption</div>
                                            </div>
                                        </div>

                                        <!-- Quick filter search bar -->
                                        <div class="pkg-search-box">
                                            <i class="fas fa-search"></i>
                                            <input type="text" class="pkg-search-input" id="searchSalesActivityEvents" placeholder="Search sales rules..." autocomplete="off">
                                            <button type="button" class="pkg-search-clear" id="clearSalesActivityEvents" style="display: none;">✕</button>
                                        </div>
                                    </div>

                                    <div class="table-responsive pkg-rules-scroll-wrap">
                                        <table class="table align-middle pkg-rules-table mb-0" id="tableSalesActivityRules">
                                            <thead>
                                                <tr>
                                                    <th style="min-width: 230px;">EVENT / ACTIVITY</th>
                                                    <th style="min-width: 140px;">DEDUCTION RATE</th>
                                                    <th style="min-width: 140px;">BILLING FREQUENCY</th>
                                                    <th style="min-width: 170px;">MINIMUM REQUIRED COINS</th>
                                                    <th style="min-width: 100px; text-align: right;">STATUS</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($eventsSales as $package_value)
                                                    @php
                                                        $evTitle = is_array($package_value) ? ($package_value['title'] ?? '') : ($package_value->title ?? '');
                                                        $evDeductions = is_array($package_value) ? ($package_value['deductions'] ?? 0) : ($package_value->deductions ?? 0);
                                                        $evType = is_array($package_value) ? ($package_value['deduction_type'] ?? '') : ($package_value->deduction_type ?? '');
                                                        $evReqCoin = is_array($package_value) ? ($package_value['required_coin'] ?? 0) : ($package_value->required_coin ?? 0);

                                                        $lower = strtolower($evTitle);
                                                        $iconClass = 'fas fa-hand-holding-usd text-primary';
                                                        if (str_contains($lower, 'lead')) $iconClass = 'fas fa-funnel-dollar text-success';
                                                        elseif (str_contains($lower, 'client')) $iconClass = 'fas fa-building text-info';
                                                        elseif (str_contains($lower, 'call') || str_contains($lower, 'phone')) $iconClass = 'fas fa-phone-alt text-primary';
                                                        elseif (str_contains($lower, 'mail') || str_contains($lower, 'email')) $iconClass = 'fas fa-envelope text-warning';
                                                        elseif (str_contains($lower, 'proposal')) $iconClass = 'fas fa-file-invoice-dollar text-purple';
                                                        elseif (str_contains($lower, 'deal')) $iconClass = 'fas fa-handshake text-success';
                                                        elseif (str_contains($lower, 'meeting')) $iconClass = 'fas fa-calendar-check text-danger';
                                                        elseif (str_contains($lower, 'export')) $iconClass = 'fas fa-file-export text-danger';
                                                    @endphp
                                                    <tr class="pkg-rule-row" data-search="{{ strtolower($evTitle . ' ' . $evType . ' ' . $evDeductions) }}">
                                                        <td>
                                                            <div class="d-flex align-items-center gap-2">
                                                                <div class="pkg-event-icon-box">
                                                                    <i class="{{ $iconClass }}"></i>
                                                                </div>
                                                                <div>
                                                                    <span class="fw-bold text-dark fs-7 d-block">{{ $evTitle ?: 'System Event' }}</span>
                                                                    <span class="text-muted fs-8">Sales wallet action</span>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center gap-1">
                                                                <i class="fas fa-coins text-warning fs-8"></i>
                                                                <span class="fw-bold text-dark fs-7">{{ (float)$evDeductions > 0 ? (float)$evDeductions . ' Coins' : 'Free (0)' }}</span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            @if (strtolower($evType) == 'daily')
                                                                <span class="badge bg-light-warning text-warning px-2 py-1 fw-bold fs-8">
                                                                    <i class="fas fa-redo-alt fs-9 me-1"></i> DAILY
                                                                </span>
                                                            @else
                                                                <span class="badge bg-light-primary text-primary px-2 py-1 fw-bold fs-8">
                                                                    <i class="fas fa-bolt fs-9 me-1"></i> ONE-TIME
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ((float)$evReqCoin > 0)
                                                                <div class="d-flex align-items-center gap-1 text-danger fw-semibold fs-7">
                                                                    <i class="fas fa-shield-alt text-danger fs-8"></i>
                                                                    <span>Min. {{ number_format((float)$evReqCoin) }} Coins</span>
                                                                </div>
                                                            @else
                                                                <span class="text-muted fs-8">None (0 Coins)</span>
                                                            @endif
                                                        </td>
                                                        <td style="text-align: right;">
                                                            <span class="badge bg-light-success text-success px-2 py-1 fs-8 fw-semibold">
                                                                <i class="fas fa-check-circle text-success fs-9 me-1"></i> Active
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5" class="text-center py-6 text-muted fs-7">
                                                            <i class="fas fa-info-circle text-muted fs-3 mb-2 d-block"></i>
                                                            No event credit deductions configured for this sales package.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                        <div class="pkg-no-rules text-center py-6 text-muted fs-7" id="noSalesRulesMatch" style="display: none;">
                                            <i class="fas fa-search text-muted opacity-50 fs-2 mb-2 d-block"></i>
                                            No sales activity rules match your search query.
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="pkg-card mt-4 p-6 text-center text-muted">
                                <i class="fas fa-shield-alt fa-2x text-muted opacity-40 mb-2 d-block"></i>
                                <div class="fw-bold fs-7 text-dark">No Active Sales Rules</div>
                                <div class="fs-8 text-muted mt-1">Assign an active sales package above to view and configure activity deduction thresholds.</div>
                            </div>
                        @endif
                    </div>
                    <!--end::invoices Content-->
                    <!--begin::transactionhistory Content-->
                    <div class="tab-pane" id="transactionhistory" role="tabpanel" aria-labelledby="tab-transactionhistory">
                        <!-- Tier 1: Section Header Toolbar Card -->
                        <div class="pkg-toolbar-card">
                            <div class="pkg-toolbar-left">
                                <div class="pkg-toolbar-icon pkg-icon-blue">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                </div>
                                <div>
                                    <div class="pkg-toolbar-title">
                                        Transaction & Billing Ledger
                                        <span class="pkg-badge-pill" id="txnCountBadge">{{ count($cart_transaction_history ?? []) }} Invoices Recorded</span>
                                    </div>
                                    <div class="pkg-toolbar-subtitle">
                                        Authoritative financial history, coin order settlements, transaction references, and official tax receipts
                                    </div>
                                </div>
                            </div>
                            <div class="pkg-toolbar-right">
                                <div class="pkg-search-box">
                                    <i class="fas fa-search"></i>
                                    <input type="text" class="pkg-search-input" id="searchTransactionHistory" placeholder="Search orders or receipts..." autocomplete="off">
                                    <button type="button" class="pkg-search-clear" id="clearTransactionHistory" style="display: none;">✕</button>
                                </div>
                            </div>
                        </div>

                        <!-- Tier 2: Transaction Ledger Sub-Card Table -->
                        <div class="pkg-card">
                            <div class="table-responsive pkg-rules-scroll-wrap">
                                <table class="table align-middle pkg-rules-table mb-0" id="myTable">
                                    <thead>
                                        <tr>
                                            <th class="w-10px pe-0" style="width: 35px;">
                                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                    <input class="form-check-input datatable_chk chk_list" type="checkbox" data-kt-check="true" data-kt-check-target="#myTable .form-check-input" value="1">
                                                </div>
                                            </th>
                                            <th style="min-width: 140px;">ORDER ID & DATE</th>
                                            <th style="min-width: 180px;">PACKAGE ORDERED</th>
                                            <th style="min-width: 120px;">CREDITS</th>
                                            <th style="min-width: 130px;">TOTAL AMOUNT</th>
                                            <th style="min-width: 160px;">REFERENCE / ID</th>
                                            <th style="min-width: 130px;">STATUS</th>
                                            <th style="min-width: 120px; text-align: right;">TAX RECEIPT</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-700">
                                        @forelse ($cart_transaction_history as $val)
                                            @php
                                                $ordno = sprintf('%05d', $val->id);
                                                if ($val->payment_mode != 'Payment Link' && $val->status == 'created') {
                                                    $status_color = 'danger';
                                                    $status_text = 'Failed';
                                                    $status_bg = '#fff5f8';
                                                    $status_fg = '#f1416c';
                                                } else {
                                                    $status_get = GeneralMethod::paymentstatus($val->status, 'razorpay');
                                                    $status_color = @$status_get->status_color ?: 'success';
                                                    $status_text = @$status_get->status_text ?: 'Paid';
                                                    $status_bg = ($status_text === 'Paid' || $status_text === 'Success') ? '#e8fff3' : (($status_text === 'Initiated') ? '#fff8dd' : '#fff5f8');
                                                    $status_fg = ($status_text === 'Paid' || $status_text === 'Success') ? '#50cd89' : (($status_text === 'Initiated') ? '#f6a723' : '#f1416c');
                                                }

                                                $link_payments = DB::table('link_payments')->select('*')->where('payment_link_id', @$val->payment_link_id)->orderByDesc('id')->first();
                                                
                                                $refText = '—';
                                                if ($val->payment_mode === 'Cash') {
                                                    $refText = 'Cash Settlement';
                                                } elseif ($val->payment_mode === 'Cheque') {
                                                    $refText = $val->chequenumber ? 'Chq: ' . $val->chequenumber : 'Cheque';
                                                } elseif ($val->payment_mode === 'Bank Transfer') {
                                                    $refText = $val->transactionid ? 'UTR: ' . $val->transactionid : 'Bank Transfer';
                                                } else {
                                                    $refText = $val->payment_Id ?: ($val->payment_mode ?: 'Online');
                                                }
                                            @endphp
                                            <tr class="pkg-txn-row" data-search="{{ strtolower($ordno . ' ' . $val->product_name . ' ' . $val->payment_mode . ' ' . $status_text . ' ' . $refText) }}">
                                                <td>
                                                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                        <input class="form-check-input chk_list_item" name="chk_list_item[]" type="checkbox" value="{{ $val->id }}">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="pkg-event-icon-box">
                                                            <i class="fas fa-receipt text-primary"></i>
                                                        </div>
                                                        <div>
                                                            <span class="fw-bold text-dark fs-7 d-block">#{{ $ordno }}</span>
                                                            <span class="text-muted fs-8">{{ $val->updated_at ? date('d M Y, h:i A', strtotime($val->updated_at)) : '—' }}</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="fw-bold text-dark fs-7 d-block">{{ $val->product_name ?: 'Custom Coin Bundle' }}</span>
                                                    <span class="pkg-type-tag mt-1">Wallet Top-Up</span>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-1">
                                                        <i class="fas fa-coins text-warning fs-8"></i>
                                                        <span class="fw-bold text-dark fs-7">+{{ number_format((float)($val->credit ?? 0)) }}</span>
                                                    </div>
                                                    <span class="text-muted fs-8">Coins Credited</span>
                                                </td>
                                                <td>
                                                    <div class="fw-bold text-dark fs-7">
                                                        <i class="fa fa-{{ $val->currency == 'INR' ? 'inr' : 'usd' }} fs-8"></i>
                                                        {{ number_format((float)($val->total_price ?? 0), 2) }}
                                                    </div>
                                                    <span class="text-muted fs-8">{{ $val->payment_mode === 'Payment Link' || $val->payment_mode === 'Online' ? 'Gateway (Online)' : ($val->payment_mode ?: 'Standard') }}</span>
                                                </td>
                                                <td>
                                                    <span class="fw-semibold text-gray-800 fs-7 d-block font-monospace" style="word-break: break-all;">{{ $refText }}</span>
                                                    <span class="text-muted fs-8">Settlement Ref</span>
                                                </td>
                                                <td>
                                                    <span class="badge px-3 py-2 fs-8 fw-bold" style="background: {{ $status_bg }}; color: {{ $status_fg }};">
                                                        <span class="pkg-status-dot me-1" style="background: {{ $status_fg }};"></span>
                                                        {{ $status_text }}
                                                    </span>
                                                    @if($status_text === 'Initiated' && !empty($link_payments->payment_link))
                                                        <div class="mt-1 d-flex align-items-center gap-1">
                                                            <span class="paymentLinktextToCopy text-truncate fs-8 text-muted" style="max-width: 110px;">{{ $link_payments->payment_link }}</span>
                                                            <button type="button" class="btn btn-icon btn-xs btn-light-primary" onclick="paymentLinkcopyText('{{ $link_payments->payment_link }}')" title="Copy Payment Link">
                                                                <i class="fa fa-copy fs-9"></i>
                                                            </button>
                                                        </div>
                                                    @endif
                                                </td>
                                                <td style="text-align: right;">
                                                    <a href="{{ route('superadmin.company.receiptdownload', [encrypt($val->cart_id)]) }}?company_id={{ $val->company_id }}" class="btn btn-sm btn-light-primary px-3 py-2 fs-8 fw-semibold" target="_blank" title="Download Tax Invoice">
                                                        <i class="fas fa-file-pdf me-1"></i> Receipt
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center py-8 text-muted fs-7">
                                                    <i class="fas fa-receipt fa-2x text-muted opacity-40 mb-2 d-block"></i>
                                                    No financial transactions or order invoices recorded for this company yet.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                <div class="pkg-no-rules text-center py-6 text-muted fs-7" id="noTxnMatch" style="display: none;">
                                    <i class="fas fa-search text-muted opacity-50 fs-2 mb-2 d-block"></i>
                                    No transaction orders match your search query.
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::transactionhistory Content-->
                    <!--begin::codeSetting Content-->
                    <div class="tab-pane" id="codeSetting" role="tabpanel" aria-labelledby="tab-codeSetting">
                        <!-- Tier 1: Toolbar Card -->
                        <div class="pkg-toolbar-card">
                            <div class="pkg-toolbar-left">
                                <div class="pkg-toolbar-icon pkg-icon-blue">
                                    <i class="fas fa-sliders-h"></i>
                                </div>
                                <div>
                                    <div class="pkg-toolbar-title">
                                        Tenant Access & Account Preferences
                                        <span class="pkg-badge-pill">Security & Access</span>
                                    </div>
                                    <div class="pkg-toolbar-subtitle">
                                        Configure organization activation status, administrative login entry points, and emergency access tokens.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tier 2: 3-Sub-Card Grid -->
                        <div class="pkg-setting-grid">
                            <!-- Sub-Card 1: Company Activation -->
                            <div class="pkg-setting-card">
                                <div>
                                    <div class="pkg-setting-top mb-3">
                                        <div class="pkg-setting-icon" style="background: #e8fff3; color: #50cd89;">
                                            <i class="fas fa-power-off"></i>
                                        </div>
                                        <div>
                                            <h4 class="fs-6 fw-bolder text-gray-900 mb-0">Company Activation</h4>
                                            <span class="fs-8 text-muted">Organization account enforcement</span>
                                        </div>
                                    </div>
                                    <p class="fs-7 text-gray-600 mb-4" style="line-height: 1.5;">
                                        Toggle company tenancy state. Suspending an account locks all organizational members from accessing their workspace dashboard.
                                    </p>
                                </div>
                                <div class="d-flex align-items-center justify-content-between p-3 rounded" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="fs-7 fw-bold text-gray-700">Account State:</span>
                                        <span class="checkActivetext badge badge-light-{{ @$users->status === 'active' ? 'success' : 'danger' }} text-{{ @$users->status === 'active' ? 'success' : 'danger' }} fw-bold fs-7">
                                            {{ @$users->status === 'active' ? 'Active' : 'Suspend' }}
                                        </span>
                                    </div>
                                    <div class="switchery-demo form-check form-switch form-switch-sm form-check-custom form-check-solid m-0">
                                        <input type="checkbox" class="js-switch form-check-input checkActive"
                                            name="checkActive" @if (@$users->status === 'active') checked @endif
                                            data-color="#99d683" data-size="small" style="width: 44px; height: 24px; cursor: pointer;" />
                                    </div>
                                </div>
                            </div>

                            <!-- Sub-Card 2: Direct SuperAdmin Login URL -->
                            <div class="pkg-setting-card">
                                <div>
                                    <div class="pkg-setting-top mb-3">
                                        <div class="pkg-setting-icon" style="background: #f0f7ff; color: #009ef7;">
                                            <i class="fas fa-link"></i>
                                        </div>
                                        <div>
                                            <h4 class="fs-6 fw-bolder text-gray-900 mb-0">Direct Login URL</h4>
                                            <span class="fs-8 text-muted">Universal workspace portal URL</span>
                                        </div>
                                    </div>
                                    <p class="fs-7 text-gray-600 mb-4" style="line-height: 1.5;">
                                        Copy the administrative login entry URL to distribute to designated client stakeholders or for immediate portal redirection.
                                    </p>
                                </div>
                                <div class="d-flex align-items-center justify-content-between p-3 rounded" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                    <code class="text-gray-700 fs-8 text-truncate me-2" style="max-width: 170px;" title="{{ route('super.login.form') }}">
                                        {{ route('super.login.form') }}
                                    </code>
                                    <button type="button" class="btn btn-sm btn-light-primary fw-bold px-3 py-2 text-nowrap d-inline-flex align-items-center gap-1" id="copyText">
                                        <i class="fas fa-copy fs-8"></i>
                                        <span>Url Copy</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Sub-Card 3: Admin One-Time Access Code -->
                            <div class="pkg-setting-card">
                                <div>
                                    <div class="pkg-setting-top mb-3">
                                        <div class="pkg-setting-icon" style="background: #fff8dd; color: #f6a723;">
                                            <i class="fas fa-key"></i>
                                        </div>
                                        <div>
                                            <h4 class="fs-6 fw-bolder text-gray-900 mb-0">Access Code Generator</h4>
                                            <span class="fs-8 text-muted">Administrative passkey token</span>
                                        </div>
                                    </div>
                                    <p class="fs-7 text-gray-600 mb-4" style="line-height: 1.5;">
                                        Generate an instantaneous one-time administrative sign-in code assigned specifically to the registered admin account:
                                        <span class="d-block fw-bold text-gray-800 text-truncate mt-1" title="{{ @$users->email }}">
                                            <i class="fas fa-envelope fs-8 text-muted me-1"></i>{{ @$users->email ?? 'admin@domain.com' }}
                                        </span>
                                    </p>
                                </div>
                                <div class="d-flex align-items-center justify-content-between p-3 rounded" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                    <span class="fs-8 text-muted fw-semibold">Click to generate token:</span>
                                    <span class="btn btn-sm btn-light-success fw-bolder px-3 py-2 text-nowrap code_generator d-inline-flex align-items-center gap-1"
                                        id="row_{{ @$users->id }}" data-id="{{ @$users->id }}" style="cursor: pointer;">
                                        <i class="fas fa-sync-alt fs-8"></i>
                                        <span>Code Generate</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::codeSetting Content-->
                    <!--begin::migration Content-->
                    <div class="tab-pane" id="migration" role="tabpanel" aria-labelledby="tab-migration">
                        <!-- Tier 1: Toolbar Card -->
                        <div class="pkg-toolbar-card">
                            <div class="pkg-toolbar-left">
                                <div class="pkg-toolbar-icon pkg-icon-blue">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                </div>
                                <div>
                                    <div class="pkg-toolbar-title">
                                        Google Sheets Data Migration Hub
                                        <span class="pkg-badge-pill">Bulk ETL Ingestion</span>
                                    </div>
                                    <div class="pkg-toolbar-subtitle">
                                        Ingest legacy client accounts, job requisitions, and candidate talent directly from linked Google Spreadsheets into the ATS.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tier 2: 3-Step Guided Workflow Strip -->
                        <div class="pkg-step-strip mb-4">
                            <div class="pkg-step-flow">
                                <!-- Step 1 -->
                                <div class="pkg-step-box">
                                    <div class="pkg-step-head">
                                        <div class="pkg-step-num">1</div>
                                        <div class="pkg-step-title">Share Sheet with Service ID</div>
                                    </div>
                                    <div class="pkg-step-desc">
                                        Grant <strong>Viewer</strong> or <strong>Editor</strong> permission on your Google Sheet to the JERO migration account:
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between p-2 rounded border border-dashed border-gray-300 bg-white">
                                        <span class="fs-8 text-primary fw-bold text-truncate me-1" id="textToCopy" style="max-width: 170px;" title="get-google-api-data@get-goolge-sheet.iam.gserviceaccount.com">
                                            get-google-api-data@get-goolge-sheet.iam.gserviceaccount.com
                                        </span>
                                        <button type="button" class="copy-button btn btn-xs btn-light-primary fw-bold px-2 py-1" onclick="copyText()" title="Copy Email Address">
                                            <i class="fas fa-copy me-1"></i>Copy
                                        </button>
                                    </div>
                                </div>

                                <!-- Step 2 -->
                                <div class="pkg-step-box">
                                    <div class="pkg-step-head">
                                        <div class="pkg-step-num">2</div>
                                        <div class="pkg-step-title">Extract Google Sheet ID</div>
                                    </div>
                                    <div class="pkg-step-desc">
                                        Extract the alphanumeric hash located between <code>/d/</code> and <code>/edit</code> in your sheet browser URL:
                                    </div>
                                    <div class="text-center p-1 rounded bg-white border border-gray-200">
                                        <img alt="Google Sheet ID Location Reference" src="{{ str_replace('index.php', '', url('public/tracker-teams-image.png')) }}" style="max-height: 38px; width: auto; object-fit: contain;">
                                    </div>
                                </div>

                                <!-- Step 3 -->
                                <div class="pkg-step-box">
                                    <div class="pkg-step-head">
                                        <div class="pkg-step-num">3</div>
                                        <div class="pkg-step-title">Download Standard Templates</div>
                                    </div>
                                    <div class="pkg-step-desc">
                                        Ensure columns and headers strictly adhere to JERO schema specifications:
                                    </div>
                                    <div class="d-flex flex-wrap gap-1">
                                        <a href="{{ str_replace('index.php', '', url('public/Book1-client.xlsx')) }}" download class="pkg-template-btn">
                                            <i class="fas fa-file-excel text-success fs-8"></i>Client
                                        </a>
                                        <a href="{{ str_replace('index.php', '', url('public/Book1-jobs.xlsx')) }}" download class="pkg-template-btn">
                                            <i class="fas fa-file-excel text-primary fs-8"></i>Jobs
                                        </a>
                                        <a href="{{ str_replace('index.php', '', url('public/Book1-candidate.xlsx')) }}" download class="pkg-template-btn">
                                            <i class="fas fa-file-excel text-warning fs-8"></i>Candidate
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tier 3: 3 Migration Ingestion Hub Cards -->
                        <div class="pkg-migration-grid">
                            <!-- Ingestion Hub 1: Client Ingestion -->
                            <div class="pkg-card">
                                <div class="pkg-card-header">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="pkg-event-icon-box" style="background: #e0f2fe; color: #0284c7;">
                                            <i class="fas fa-user-tie"></i>
                                        </div>
                                        <div>
                                            <h4 class="fs-6 fw-bolder text-gray-900 mb-0">Client Migration</h4>
                                            <span class="fs-8 text-muted">Corporate account records</span>
                                        </div>
                                    </div>
                                    <a href="https://docs.google.com/spreadsheets/d/17oawy1WWLysg4M3IRLuSBArn2Rl_Igj18Jp2G9TztOg/edit?gid=0#gid=0" target="_blank" class="fs-8 text-primary fw-semibold d-inline-flex align-items-center gap-1">
                                        <i class="fas fa-external-link-alt fs-9"></i>Sample Sheet
                                    </a>
                                </div>
                                <div class="pkg-card-body">
                                    <form id="client_new_form" class="form" action="#">
                                        <input type="hidden" value="{{ @$client->id }}" name="company_id">
                                        
                                        <div class="mb-4">
                                            <label class="form-label required fs-7 fw-bold text-gray-700">Client Google Sheet ID</label>
                                            <input type="text" class="form-control form-control-solid" placeholder="Paste Sheet ID here..." name="googlesheetid" value="" required />
                                        </div>

                                        <div class="mb-5">
                                            <label class="form-label required fs-7 fw-bold text-gray-700">Assigned Team</label>
                                            <select name="teams" class="form-select form-select-solid" data-control="select2" data-hide-search="true" required>
                                                <option value="">Select Team</option>
                                                @if (@$teams)
                                                    @foreach (@$teams as $valuser)
                                                        <option value="{{ $valuser->id }}">{{ $valuser->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>

                                        <button id="successButtonclient" class="btn btn-success" style="display: none; width: 100%; margin-bottom: 10px;"></button>
                                        <button id="errorButtonclient" class="btn btn-danger" style="display: none; width: 100%; margin-bottom: 10px;"></button>

                                        <button type="submit" id="client_new_submit" class="btn btn-primary w-100 fw-bold">
                                            <span class="indicator-label indicator-label-client">
                                                <i class="fas fa-file-import me-1"></i>Import Clients
                                            </span>
                                            <span class="indicator-progress indicator-progress-client">
                                                Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                            </span>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Ingestion Hub 2: Jobs Ingestion -->
                            <div class="pkg-card">
                                <div class="pkg-card-header">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="pkg-event-icon-box" style="background: #eff6ff; color: #2563eb;">
                                            <i class="fas fa-briefcase"></i>
                                        </div>
                                        <div>
                                            <h4 class="fs-6 fw-bolder text-gray-900 mb-0">Jobs Migration</h4>
                                            <span class="fs-8 text-muted">Active open requisitions</span>
                                        </div>
                                    </div>
                                    <a href="https://docs.google.com/spreadsheets/d/1rQBpcFxDprxxr1wDG3fn7vhnUGgqZCqpe_gxK6fwgeo/edit?gid=0#gid=0" target="_blank" class="fs-8 text-primary fw-semibold d-inline-flex align-items-center gap-1">
                                        <i class="fas fa-external-link-alt fs-9"></i>Sample Sheet
                                    </a>
                                </div>
                                <div class="pkg-card-body">
                                    <form id="candidate_new_form_job" class="form" action="" method="POST">
                                        <input type="hidden" value="{{ @$client->id }}" name="company_id">

                                        <div class="mb-3">
                                            <label class="form-label required fs-7 fw-bold text-gray-700">Jobs Google Sheet ID</label>
                                            <input type="text" class="form-control form-control-solid" placeholder="Paste Sheet ID here..." name="googlesheetid" value="" required />
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label required fs-7 fw-bold text-gray-700">Client</label>
                                            <select name="client_id" id="clientID" class="form-select form-select-solid" data-control="select2" data-hide-search="true" required>
                                                <option value="">Select Client</option>
                                                @if(@$clientlist)
                                                    @foreach($clientlist as $val)  
                                                        <option value="{{ $val->id }}">{{ $val->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label required fs-7 fw-bold text-gray-700">Hiring Manager / HR</label>
                                            <select name="hiring_namager" id="hiring_namager" class="form-select form-select-solid" data-control="select2" data-hide-search="true" required>
                                                <option value="">Select Hiring Manager</option>
                                            </select>
                                        </div>

                                        <div class="mb-5">
                                            <label class="form-label required fs-7 fw-bold text-gray-700">Recruiter Team</label>
                                            <select class="form-select form-select-solid" name="recruiter" id="teamsId">
                                                <option value="">Select Team</option>
                                                @if (@$teams)
                                                    @foreach (@$teams as $valuser)
                                                        <option value="{{ $valuser->name }}">{{ $valuser->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>

                                        <button id="successButtoncanJob" class="btn btn-success" style="display: none; width: 100%; margin-bottom: 10px;"></button>
                                        <button id="errorButtoncanJob" class="btn btn-danger" style="display: none; width: 100%; margin-bottom: 10px;"></button>

                                        <button type="submit" id="candidate_new_submit_job" class="btn btn-primary w-100 fw-bold">
                                            <span class="indicator-label-can-job indicator-label">
                                                <i class="fas fa-file-import me-1"></i>Import Jobs
                                            </span>
                                            <span class="indicator-progress-can-job indicator-progress">
                                                Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                            </span>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Ingestion Hub 3: Candidate Ingestion -->
                            <div class="pkg-card">
                                <div class="pkg-card-header">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="pkg-event-icon-box" style="background: #e8fff3; color: #50cd89;">
                                            <i class="fas fa-user-graduate"></i>
                                        </div>
                                        <div>
                                            <h4 class="fs-6 fw-bolder text-gray-900 mb-0">Candidate Migration</h4>
                                            <span class="fs-8 text-muted">Talent profiles & applications</span>
                                        </div>
                                    </div>
                                    <a href="https://docs.google.com/spreadsheets/d/1oMkFyIZKX56IWGi0-4kB3om0RPib3NnwLyQcjaIUabs/edit?gid=0#gid=0" target="_blank" class="fs-8 text-primary fw-semibold d-inline-flex align-items-center gap-1">
                                        <i class="fas fa-external-link-alt fs-9"></i>Sample Sheet
                                    </a>
                                </div>
                                <div class="pkg-card-body">
                                    <form id="candidate_new_form" class="form" action="" method="POST">
                                        <input type="hidden" value="{{ @$client->id }}" name="company_id">

                                        <div class="mb-3">
                                            <label class="form-label required fs-7 fw-bold text-gray-700">Candidate Google Sheet ID</label>
                                            <input type="text" class="form-control form-control-solid" placeholder="Paste Sheet ID here..." name="googlesheetid" value="" required />
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label required fs-7 fw-bold text-gray-700">Target Job Requisition</label>
                                            <select name="job_id" class="form-select form-select-solid" data-control="select2" data-hide-search="true" required>
                                                <option value="">Select Job</option>
                                                @if (@$jobsforcandidate)
                                                    @foreach (@$jobsforcandidate as $val_job)
                                                        <option value="{{ $val_job->id }}">J{{ $val_job->id }}, {{ $val_job->title }} ({{ $val_job->clients_name }})</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label required fs-7 fw-bold text-gray-700">Assigned Team</label>
                                            <select name="teams" class="form-select form-select-solid" data-control="select2" data-hide-search="true" required>
                                                <option value="">Select Team</option>
                                                @if (@$teams)
                                                    @foreach (@$teams as $valuser)
                                                        <option value="{{ $valuser->id }}">{{ $valuser->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>

                                        <div class="row g-2 mb-5">
                                            <div class="col-6">
                                                <label class="form-label required fs-7 fw-bold text-gray-700">CTC Currency</label>
                                                <select class="form-select form-select-solid" id="current_ctc_cur" name="current_ctc_cur">
                                                    <option value="0" selected="selected">INR</option>
                                                    <option value="1">EUR</option>
                                                    <option value="2">JPY</option>
                                                    <option value="3">GBP</option>
                                                    <option value="4">USD</option>
                                                </select>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label required fs-7 fw-bold text-gray-700">CTC Frequency</label>
                                                <select class="form-select form-select-solid" name="current_ctc_mode" id="current_ctc_mode">
                                                    <option value="0" selected="selected">Annual</option>
                                                    <option value="1">Monthly</option>
                                                    <option value="2">Hourly</option>
                                                </select>
                                            </div>
                                        </div>

                                        <button id="successButtoncan" class="btn btn-success" style="display: none; width: 100%; margin-bottom: 10px;"></button>
                                        <button id="errorButtoncan" class="btn btn-danger" style="display: none; width: 100%; margin-bottom: 10px;"></button>

                                        <button type="submit" id="candidate_new_submit" class="btn btn-primary w-100 fw-bold">
                                            <span class="indicator-label-can indicator-label">
                                                <i class="fas fa-file-import me-1"></i>Import Candidates
                                            </span>
                                            <span class="indicator-progress-can indicator-progress">
                                                Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                            </span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::migration Content-->
                    <!--begin::topupcoins Content-->
                    <div class="tab-pane" id="topupcoins" role="tabpanel" aria-labelledby="tab-topupcoins">
                        <!-- Tier 1: Toolbar Card -->
                        <div class="pkg-toolbar-card">
                            <div class="pkg-toolbar-left">
                                <div class="pkg-toolbar-icon pkg-icon-amber">
                                    <i class="fas fa-coins"></i>
                                </div>
                                <div>
                                    <div class="pkg-toolbar-title">
                                        Wallet Recharge & Coin Top-Up
                                        <span class="pkg-badge-pill">Billing & Credits</span>
                                    </div>
                                    <div class="pkg-toolbar-subtitle">
                                        Allocate operational or sales coins to this company wallet, manage payment settlements, and issue official order tax receipts.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tier 2: 2-Column Grid inside Form -->
                        <form id="operational_top_up_new_form" class="form" action="#">
                            <input type="hidden" name="paymentLinkPaymentstatus" id="paymentLinkPaymentstatus" value="" />
                            <input type="hidden" name="paymentLinkpayment_orderId" id="paymentLinkpayment_orderId" value="" />
                            <input type="hidden" name="paymentLinkshort_url" id="paymentLinkshort_url" value="" />
                            <input type="hidden" value="{{ @$client->id }}" name="company_id" id="company_id_topup">
                            <input type="hidden" value="" class="gst_amount_hidden_value" name="gst_amount">

                            <div class="pkg-topup-grid">
                                <!-- Left Sub-Card: Allocation Parameters -->
                                <div class="pkg-card">
                                    <div class="pkg-card-header">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="pkg-event-icon-box" style="background: #fff8dd; color: #f6a723;">
                                                <i class="fas fa-sliders-h"></i>
                                            </div>
                                            <div>
                                                <h4 class="fs-6 fw-bolder text-gray-900 mb-0">Recharge Parameters</h4>
                                                <span class="fs-8 text-muted">Currency, tax scope, and coin volume</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="pkg-card-body">
                                        <div class="row g-3 mb-4">
                                            <div class="col-md-5">
                                                <label class="form-label required fs-7 fw-bold text-gray-700">Currency</label>
                                                <select name="currency" id="currency_get" class="form-select form-select-solid" data-control="select2" data-hide-search="true" required>
                                                    <option value="INR"><i class="fa fa-inr" aria-hidden="true"></i> INR</option>
                                                    <option value="USD"><i class="fa fa-usd" aria-hidden="true"></i> USD</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label required fs-7 fw-bold text-gray-700">GST Apply</label>
                                                <select name="gst_on" id="gst_on_get" class="form-select form-select-solid" data-control="select2" data-hide-search="true" required>
                                                    <option value="Yes">Yes</option>
                                                    <option value="No">No</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label required fs-7 fw-bold text-gray-700">Top-up Type</label>
                                                <select name="topUptype" id="topUptype" class="form-select form-select-solid" data-control="select2" data-hide-search="true" required>
                                                    <option value="">Select Type</option>
                                                    <option value="operational">Operational</option>
                                                    <option value="sales">Sales</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="mb-4">
                                            <label class="form-label required fs-7 fw-bold text-gray-700">Predefined Coin Bundles</label>
                                            <div class="row py-2 mb-1" id="currencyPrice">
                                                <!-- Dynamic bundle buttons loaded via AJAX -->
                                            </div>
                                        </div>

                                        <div class="row g-3">
                                            <div class="col-6">
                                                <label class="form-label required fs-7 fw-bold text-gray-700">Coins Amount</label>
                                                <input type="text" class="form-control form-control-solid coins_get" placeholder="e.g. 500" name="coins" value="" />
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label required fs-7 fw-bold text-gray-700">Base Price</label>
                                                <input type="text" class="form-control form-control-solid price_get" placeholder="0.00" name="price" value="" />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right Sub-Card: Calculation & Payment Mode -->
                                <div class="pkg-card">
                                    <div class="pkg-card-header">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="pkg-event-icon-box" style="background: #e8fff3; color: #50cd89;">
                                                <i class="fas fa-receipt"></i>
                                            </div>
                                            <div>
                                                <h4 class="fs-6 fw-bolder text-gray-900 mb-0">Cost Calculation & Settlement</h4>
                                                <span class="fs-8 text-muted">Discount, taxes, and payment instrument</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="pkg-card-body">
                                        <div class="p-4 rounded mb-4" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <label class="form-label fs-7 fw-bold text-gray-700 mb-0">Discount (%):</label>
                                                <input type="number" class="form-control form-control-solid" name="discount" id="discount" value="" placeholder="0.00" style="width: 140px; text-align: right;">
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <label class="form-label fs-7 fw-bold text-gray-700 mb-0">GST (%):</label>
                                                <input type="number" class="form-control form-control-solid gsttax_hidden_value" name="gst" id="gst" value="" placeholder="0.00" style="width: 140px; text-align: right;">
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between pt-3 border-top border-gray-300">
                                                <label class="form-label fs-6 fw-bolder text-gray-900 mb-0">Net Total:</label>
                                                <input type="text" value="" class="form-control form-control-solid total_price_hidden_value fw-bolder text-primary fs-6" name="total_price" style="width: 140px; text-align: right;" placeholder="0.00">
                                            </div>
                                        </div>

                                        <div class="row g-3 mb-3">
                                            <div class="col-6">
                                                <label class="form-label fs-7 fw-bold text-gray-700">Payment Mode</label>
                                                <select id="payment-mode-selector" name="paymentmodeselector" class="form-select form-select-solid">
                                                    <option value="">Select Payment Mode</option>
                                                    <option value="Cash">Cash</option>
                                                    <option value="Cheque">Cheque</option>
                                                    <option value="Bank Transfer">Bank Transfer</option>
                                                    <option value="Payment Link">Payment Link</option>
                                                </select>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label fs-7 fw-bold text-gray-700">Payment Method</label>
                                                <select id="payment-method-selector" name="paymentmethodselector" class="form-select form-select-solid">
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Cash Settlement -->
                                        <div id="cash" class="payment-method p-3 rounded bg-light border border-dashed border-gray-300 mb-3">
                                            <p class="fs-7 text-gray-700 mb-0">
                                                <i class="fas fa-hand-holding-usd text-success me-1"></i>Please collect the cash settlement directly through authorized corporate representative.
                                            </p>
                                        </div>

                                        <!-- Cheque Payment -->
                                        <div id="cheque" class="payment-method p-3 rounded bg-light border border-dashed border-gray-300 mb-3">
                                            <p class="fs-7 text-gray-700 mb-2">Issue cheque in favor of <strong>Company Name</strong>:</p>
                                            <div class="row g-2">
                                                <div class="col-md-4">
                                                    <label for="cheque-number" class="form-label fs-8 text-gray-600 mb-1">Cheque Number</label>
                                                    <input type="text" class="form-control form-control-solid form-control-sm" id="cheque-number" name="chequenumber">
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="cheque-date" class="form-label fs-8 text-gray-600 mb-1">Cheque Date</label>
                                                    <input type="date" class="form-control form-control-solid form-control-sm" id="cheque-date" name="chequedate">
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="bank-name" class="form-label fs-8 text-gray-600 mb-1">Bank Name</label>
                                                    <input type="text" class="form-control form-control-solid form-control-sm" id="bank-name" name="bankname">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Bank Transfer -->
                                        <div id="bank_transfer" class="payment-method p-3 rounded bg-light border border-dashed border-gray-300 mb-3">
                                            <p class="fs-7 text-gray-700 mb-2">Record wire transfer reconciliation reference details:</p>
                                            <div class="row g-2">
                                                <div class="col-6">
                                                    <label for="transaction-id" class="form-label fs-8 text-gray-600 mb-1">Transaction ID (UTR)</label>
                                                    <input type="text" class="form-control form-control-solid form-control-sm" id="transaction-id" name="transactionid">
                                                </div>
                                                <div class="col-6">
                                                    <label for="bank-reference" class="form-label fs-8 text-gray-600 mb-1">Bank Reference</label>
                                                    <input type="text" class="form-control form-control-solid form-control-sm" id="bank-reference" name="bankreference">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Online Payment Link -->
                                        <div id="online" class="payment-method p-3 rounded bg-light border border-dashed border-gray-300 mb-3">
                                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                                <p class="fs-7 text-gray-700 mb-0">Generate a secure payment gateway checkout link:</p>
                                                <button type="button" class="btn btn-sm btn-light-primary fw-bold paymentLinkGenerate" onclick="paymentLinkGenerate()">
                                                    <i class="fas fa-magic me-1"></i>Generate Payment Link
                                                </button>
                                            </div>
                                        </div>

                                        <div class="paymentLinkshow mb-3" style="display:none">
                                            <label class="form-label fs-8 text-gray-600 mb-1">Active Checkout Link</label>
                                            <input type="text" class="form-control form-control-solid" id="paymentLinkshow" value="" readonly/>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Bottom Sub-Card: Official Invoicing Entity -->
                            <div class="pkg-card mb-4">
                                <div class="pkg-card-header">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="pkg-event-icon-box" style="background: #f1f5f9; color: #475569;">
                                            <i class="fas fa-building"></i>
                                        </div>
                                        <div>
                                            <h4 class="fs-6 fw-bolder text-gray-900 mb-0">Official Invoicing Entity</h4>
                                            <span class="fs-8 text-muted">Billing address and organizational tax identifiers</span>
                                        </div>
                                    </div>
                                    <a href="javascript:;" data-id="{{ $client->company_id }}" class="btn btn-sm btn-light-primary fw-bold show-billing-address">
                                        <i class="fas fa-edit me-1"></i>Edit Billing Details
                                    </a>
                                </div>
                                <div class="pkg-card-body">
                                    <div class="row g-4 align-items-center">
                                        <div class="col-md-3">
                                            <span class="fs-8 text-muted d-block text-uppercase fw-bold">Billing Contact</span>
                                            <span id="usernamebill" class="fw-bold text-gray-900 fs-7">{{ @$user_name }}</span>
                                        </div>
                                        <div class="col-md-3">
                                            <span class="fs-8 text-muted d-block text-uppercase fw-bold">Company Legal Entity</span>
                                            <span class="fw-bold text-gray-900 fs-7">{{ @$company_name }}</span>
                                        </div>
                                        <div class="col-md-3">
                                            <span class="fs-8 text-muted d-block text-uppercase fw-bold">Tax Registration</span>
                                            <div class="d-flex align-items-center gap-2 mt-1">
                                                <span class="badge bg-light text-dark fw-bold fs-8">GST: <span id="billing_gst">{{ @$gstin ?: 'N/A' }}</span></span>
                                                <span class="badge bg-light text-dark fw-bold fs-8">PAN: <span id="billing_pan">{{ @$pan_no ?: 'N/A' }}</span></span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <span class="fs-8 text-muted d-block text-uppercase fw-bold">Billing Address</span>
                                            <span id="billing_address" class="text-gray-700 fs-8 text-truncate d-block" title="{{ @$locArr }}">{{ @$locArr ?: 'Not specified' }}</span>
                                        </div>
                                    </div>

                                    <button id="successButtonoperationaltopup" class="btn btn-success" style="display: none; width: 100%; margin: 12px 0;"></button>
                                    <button id="errorButtonoperationaltopup" class="btn btn-danger" style="display: none; width: 100%; margin: 12px 0;"></button>

                                    <div class="text-end pt-4 border-top border-gray-200 mt-4">
                                        <button type="submit" id="operational_top_up_new_submit" class="btn btn-primary px-8 fw-bold">
                                            <span class="indicator-label indicator-label-top-up">
                                                <i class="fas fa-check-circle me-1"></i>Confirm Top-Up Order
                                            </span>
                                            <span class="indicator-progress indicator-progress-top-up">
                                                Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        @if ($link_payments)
                            <div class="p-4 rounded border border-warning bg-light-warning d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fas fa-exclamation-circle text-warning fs-4"></i>
                                    <div>
                                        <span class="fw-bold text-gray-900 fs-7">Pending Checkout Payment:</span>
                                        <code class="text-primary fs-8 ms-2" id="paymentLinktextToCopy">{{ $link_payments->payment_link }}</code>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-xs btn-light-warning fw-bold" onclick="paymentLinkcopyText('{{ $link_payments->payment_link }}')">
                                    <i class="fas fa-copy me-1"></i>Click to Copy Link
                                </button>
                            </div>
                        @endif
                    </div>
                    <!--end::topupcoins Content-->
                    <!--begin::packages Content-->
                    <div class="tab-pane" id="packages" role="tabpanel" aria-labelledby="tab-packages">
                        @php
                            $opFreePack = DB::table('coin_packages')
                                ->join('coin_package_types', 'coin_package_types.id', '=', 'coin_packages.coin_package_type_id')
                                ->where('coin_packages.custome_pck', 2)
                                ->where('coin_packages.event_type', '=', 'operational')
                                ->select('coin_packages.*', 'coin_package_types.title')
                                ->first();

                            $opFreeStatus = DB::table('company_coins')
                                ->where('company_id', $client->id)
                                ->where('event_type', 'operational')
                                ->value('free_pack_status') ?? 'inactive';
                        @endphp

                        <!-- Tier 1: Section Header Toolbar Card -->
                        <div class="pkg-toolbar-card">
                            <div class="pkg-toolbar-left">
                                <div class="pkg-toolbar-icon pkg-icon-blue">
                                    <i class="fas fa-boxes"></i>
                                </div>
                                <div>
                                    <div class="pkg-toolbar-title">
                                        Operational Package & Credit Architecture
                                        <span class="pkg-badge-pill">{{ count($company_coins ?? []) }} Active Assigned</span>
                                    </div>
                                    <div class="pkg-toolbar-subtitle">
                                        Manage operational subscription, wallet balances, free tier onboarding, and per-event deduction rules
                                    </div>
                                </div>
                            </div>
                            <div class="pkg-toolbar-right">
                                <a href="{{ url('super-admin/custompackages/create/' . @$client->id) }}?packageType={{ encrypt('operational') }}" class="pkg-action-btn">
                                    <i class="fas fa-plus"></i>
                                    <span>New Operational Package</span>
                                </a>
                            </div>
                        </div>

                        <!-- Tier 2: Overview Dual-Card Grid (Current Plan Hero + Onboarding Free Tier) -->
                        <div class="pkg-overview-grid">
                            <!-- Sub-Card A: Current Active Operational Package -->
                            @if (!empty($company_coins) && count($company_coins) > 0)
                                @foreach ($company_coins as $package)
                                    @php
                                        $pkgTitle = is_array($package) ? ($package['title'] ?? '') : ($package->title ?? '');
                                        $pkgStatus = is_array($package) ? ($package['status'] ?? '') : ($package->status ?? '');
                                        $pkgStartDate = is_array($package) ? ($package['start_date'] ?? '') : ($package->start_date ?? '');
                                        $pkgEndDate = is_array($package) ? ($package['end_date'] ?? '') : ($package->end_date ?? '');
                                        $pkgOrderCoins = (float)(is_array($package) ? ($package['order_coins'] ?? 0) : ($package->order_coins ?? 0));
                                        $pkgCoins = (float)(is_array($package) ? ($package['coins'] ?? 0) : ($package->coins ?? 0));
                                        $isActivePlan = strtotime($pkgEndDate) >= strtotime(date('Y-m-d')) && $pkgStatus == 'valid';

                                        $daysLeft = 0;
                                        if (!empty($pkgEndDate)) {
                                            $diff = strtotime($pkgEndDate) - time();
                                            $daysLeft = (int) ceil($diff / 86400);
                                        }

                                        $pct = $pkgOrderCoins > 0 ? min(100, max(0, round(($pkgCoins / $pkgOrderCoins) * 100))) : 0;
                                    @endphp
                                    <div class="pkg-card mb-0">
                                        <div class="pkg-card-header">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="pkg-tier-badge">
                                                    <i class="fas fa-crown text-warning"></i>
                                                    <span class="fw-bold">{{ $pkgTitle ?: 'Custom Operational Plan' }}</span>
                                                </div>
                                                <span class="pkg-type-tag">Operational Engine</span>
                                            </div>
                                            <div>
                                                @if ($isActivePlan)
                                                    <span class="badge bg-light-success text-success px-3 py-2 fw-bold fs-8">
                                                        <span class="pkg-status-dot bg-success me-1"></span> Active Plan
                                                    </span>
                                                @else
                                                    <span class="badge bg-light-danger text-danger px-3 py-2 fw-bold fs-8">
                                                        <span class="pkg-status-dot bg-danger me-1"></span> Expired
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="pkg-card-body">
                                            <div class="pkg-stat-boxes">
                                                <!-- Stat 1: Remaining Coins -->
                                                <div class="pkg-stat-box pkg-stat-primary">
                                                    <div class="pkg-stat-label">REMAINING COINS</div>
                                                    <div class="pkg-stat-val text-primary">
                                                        <i class="fas fa-coins me-1 fs-6 text-warning"></i>
                                                        {{ number_format($pkgCoins, 1) }}
                                                    </div>
                                                    <div class="pkg-stat-sub">Live operational balance</div>
                                                </div>
                                                <!-- Stat 2: Total Granted -->
                                                <div class="pkg-stat-box">
                                                    <div class="pkg-stat-label">TOTAL ALLOCATED</div>
                                                    <div class="pkg-stat-val text-dark">
                                                        {{ number_format($pkgOrderCoins) }}
                                                    </div>
                                                    <div class="pkg-stat-sub">Purchased capacity</div>
                                                </div>
                                                <!-- Stat 3: Validity Period -->
                                                <div class="pkg-stat-box">
                                                    <div class="pkg-stat-label">VALIDITY WINDOW</div>
                                                    <div class="pkg-stat-val text-dark fs-8 fw-bold">
                                                        {{ $pkgStartDate ? date('d M Y', strtotime($pkgStartDate)) : '—' }}
                                                        <span class="text-muted fw-normal px-1">to</span>
                                                        {{ $pkgEndDate ? date('d M Y', strtotime($pkgEndDate)) : '—' }}
                                                    </div>
                                                    <div class="pkg-stat-sub">
                                                        @if ($daysLeft > 0)
                                                            <span class="text-success fw-semibold"><i class="fas fa-check-circle text-success me-1 fs-9"></i>{{ $daysLeft }} days left</span>
                                                        @elseif ($daysLeft === 0)
                                                            <span class="text-warning fw-semibold"><i class="fas fa-exclamation-circle text-warning me-1 fs-9"></i>Expires today</span>
                                                        @else
                                                            <span class="text-danger fw-semibold"><i class="fas fa-times-circle text-danger me-1 fs-9"></i>Expired {{ abs($daysLeft) }}d ago</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="pkg-progress-wrap mt-3">
                                                <div class="d-flex justify-content-between align-items-center mb-1 fs-8">
                                                    <span class="text-muted fw-semibold">Balance Utilization</span>
                                                    <span class="fw-bold text-dark">{{ $pkgCoins > $pkgOrderCoins ? 'Top-up Balance Augmented' : ($pct . '% available') }}</span>
                                                </div>
                                                <div class="progress" style="height: 6px; border-radius: 4px; background: #e2e8f0;">
                                                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ min(100, $pct) }}%; border-radius: 4px;"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="pkg-card d-flex flex-column align-items-center justify-content-center p-6 text-center mb-0" style="min-height: 180px;">
                                    <div class="pkg-empty-icon mb-2">
                                        <i class="fas fa-box-open fa-2x text-muted opacity-50"></i>
                                    </div>
                                    <div class="fw-bold fs-6 text-gray-800">No Operational Package Assigned</div>
                                    <div class="fs-8 text-muted mt-1 mb-3">Create and assign a custom operational package to fund candidate searches, jobs, and workflows.</div>
                                    <a href="{{ url('super-admin/custompackages/create/' . @$client->id) }}?packageType={{ encrypt('operational') }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-plus"></i> Assign Package
                                    </a>
                                </div>
                            @endif

                            <!-- Sub-Card B: Onboarding / Free Trial Operational Tier -->
                            <div class="pkg-card mb-0">
                                <div class="pkg-card-header">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="pkg-toolbar-icon pkg-icon-amber" style="width: 32px; height: 32px; font-size: 13px;">
                                            <i class="fas fa-gift"></i>
                                        </div>
                                        <div>
                                            <span class="fw-bold fs-7 text-dark">{{ $opFreePack->title ?? 'Free Trial' }} Tier</span>
                                            <span class="pkg-type-tag ms-1">Onboarding Default</span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="form-check form-switch form-switch-sm form-check-custom form-check-solid m-0">
                                            <input type="checkbox"
                                                class="js-switch form-check-input checkStatusOperational cursor-pointer"
                                                name="checkStatusOperational"
                                                id="switchOperationalFree"
                                                @if ($opFreeStatus == 'active') checked @endif />
                                        </div>
                                        <span class="badge {{ $opFreeStatus == 'active' ? 'bg-light-success text-success' : 'bg-light text-muted' }} px-2 py-1 fw-bold fs-8 checkStatusOperational_text">
                                            {{ $opFreeStatus == 'active' ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="pkg-card-body">
                                    <div class="pkg-stat-boxes" style="grid-template-columns: repeat(2, 1fr);">
                                        <div class="pkg-stat-box">
                                            <div class="pkg-stat-label">TRIAL DURATION</div>
                                            <div class="pkg-stat-val text-dark">
                                                <i class="fas fa-clock text-info me-1 fs-6"></i>
                                                {{ !empty($opFreePack->days) ? $opFreePack->days : 0 }} Days
                                            </div>
                                            <div class="pkg-stat-sub">Valid from onboarding</div>
                                        </div>
                                        <div class="pkg-stat-box">
                                            <div class="pkg-stat-label">FREE COIN GRANT</div>
                                            <div class="pkg-stat-val text-dark">
                                                <i class="fas fa-coins text-warning me-1 fs-6"></i>
                                                {{ number_format((float)(@$opFreePack->coins ?? 0)) }} Coins
                                            </div>
                                            <div class="pkg-stat-sub">Auto-credited allowance</div>
                                        </div>
                                    </div>

                                    <div class="pkg-info-banner mt-3">
                                        <i class="fas fa-info-circle text-primary me-2"></i>
                                        <span>Controls whether this workspace is granted initial operational coins upon provisioning to explore recruitment and candidate matching.</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tier 3: Credit Usage Overview per Activity (High Density Table Sub-Card) -->
                        @if (!empty($company_coins))
                            @foreach ($company_coins as $package)
                                @php
                                    $eventsOp = is_array($package) ? ($package['coin_order_events'] ?? []) : ($package->coin_order_events ?? []);
                                @endphp
                                <div class="pkg-card mt-4">
                                    <div class="pkg-table-header">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="pkg-toolbar-icon pkg-icon-blue" style="width: 32px; height: 32px; font-size: 13px;">
                                                <i class="fas fa-sliders-h"></i>
                                            </div>
                                            <div>
                                                <h4 class="fw-bold text-dark m-0 fs-6">
                                                    Operational Credit Usage Rules by Activity
                                                    <span class="pkg-badge-pill ms-2" id="opRulesCountBadge">
                                                        {{ count($eventsOp) }} Rules Configured
                                                    </span>
                                                </h4>
                                                <div class="text-muted fs-8">Authoritative deduction schedule per system action in this workspace</div>
                                            </div>
                                        </div>

                                        <!-- Quick filter search bar -->
                                        <div class="pkg-search-box">
                                            <i class="fas fa-search"></i>
                                            <input type="text" class="pkg-search-input" id="searchOpActivityEvents" placeholder="Search operational rules..." autocomplete="off">
                                            <button type="button" class="pkg-search-clear" id="clearOpActivityEvents" style="display: none;">✕</button>
                                        </div>
                                    </div>

                                    <div class="table-responsive pkg-rules-scroll-wrap">
                                        <table class="table align-middle pkg-rules-table mb-0" id="tableOpActivityRules">
                                            <thead>
                                                <tr>
                                                    <th style="min-width: 230px;">EVENT / ACTIVITY</th>
                                                    <th style="min-width: 140px;">DEDUCTION RATE</th>
                                                    <th style="min-width: 140px;">BILLING FREQUENCY</th>
                                                    <th style="min-width: 170px;">MINIMUM REQUIRED COINS</th>
                                                    <th style="min-width: 100px; text-align: right;">STATUS</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($eventsOp as $package_active)
                                                    @php
                                                        $evTitle = is_array($package_active) ? ($package_active['title'] ?? '') : ($package_active->title ?? '');
                                                        $evDeductions = is_array($package_active) ? ($package_active['deductions'] ?? 0) : ($package_active->deductions ?? 0);
                                                        $evType = is_array($package_active) ? ($package_active['deduction_type'] ?? '') : ($package_active->deduction_type ?? '');
                                                        $evReqCoin = is_array($package_active) ? ($package_active['required_coin'] ?? 0) : ($package_active->required_coin ?? 0);

                                                        $lower = strtolower($evTitle);
                                                        $iconClass = 'fas fa-cog text-primary';
                                                        if (str_contains($lower, 'team')) $iconClass = 'fas fa-users-cog text-primary';
                                                        elseif (str_contains($lower, 'job')) $iconClass = 'fas fa-briefcase text-success';
                                                        elseif (str_contains($lower, 'candidate')) $iconClass = 'fas fa-user-check text-info';
                                                        elseif (str_contains($lower, 'talent')) $iconClass = 'fas fa-layer-group text-warning';
                                                        elseif (str_contains($lower, 'partner')) $iconClass = 'fas fa-handshake text-primary';
                                                        elseif (str_contains($lower, 'mail') || str_contains($lower, 'email')) $iconClass = 'fas fa-envelope text-info';
                                                        elseif (str_contains($lower, 'bench')) $iconClass = 'fas fa-id-badge text-purple';
                                                        elseif (str_contains($lower, 'interview')) $iconClass = 'fas fa-video text-danger';
                                                        elseif (str_contains($lower, 'offer')) $iconClass = 'fas fa-file-signature text-success';
                                                        elseif (str_contains($lower, 'whatsapp')) $iconClass = 'fab fa-whatsapp text-success';
                                                        elseif (str_contains($lower, 'cv')) $iconClass = 'fas fa-file-alt text-info';
                                                        elseif (str_contains($lower, 'export')) $iconClass = 'fas fa-file-export text-danger';
                                                    @endphp
                                                    <tr class="pkg-rule-row" data-search="{{ strtolower($evTitle . ' ' . $evType . ' ' . $evDeductions) }}">
                                                        <td>
                                                            <div class="d-flex align-items-center gap-2">
                                                                <div class="pkg-event-icon-box">
                                                                    <i class="{{ $iconClass }}"></i>
                                                                </div>
                                                                <div>
                                                                    <span class="fw-bold text-dark fs-7 d-block">{{ $evTitle ?: 'System Event' }}</span>
                                                                    <span class="text-muted fs-8">Automated workflow action</span>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center gap-1">
                                                                <i class="fas fa-coins text-warning fs-8"></i>
                                                                <span class="fw-bold text-dark fs-7">{{ (float)$evDeductions > 0 ? (float)$evDeductions . ' Coins' : 'Free (0)' }}</span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            @if (strtolower($evType) == 'daily')
                                                                <span class="badge bg-light-warning text-warning px-2 py-1 fw-bold fs-8">
                                                                    <i class="fas fa-redo-alt fs-9 me-1"></i> DAILY
                                                                </span>
                                                            @else
                                                                <span class="badge bg-light-primary text-primary px-2 py-1 fw-bold fs-8">
                                                                    <i class="fas fa-bolt fs-9 me-1"></i> ONE-TIME
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ((float)$evReqCoin > 0)
                                                                <div class="d-flex align-items-center gap-1 text-danger fw-semibold fs-7">
                                                                    <i class="fas fa-shield-alt text-danger fs-8"></i>
                                                                    <span>Min. {{ number_format((float)$evReqCoin) }} Coins</span>
                                                                </div>
                                                            @else
                                                                <span class="text-muted fs-8">None (0 Coins)</span>
                                                            @endif
                                                        </td>
                                                        <td style="text-align: right;">
                                                            <span class="badge bg-light-success text-success px-2 py-1 fs-8 fw-semibold">
                                                                <i class="fas fa-check-circle text-success fs-9 me-1"></i> Active
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5" class="text-center py-6 text-muted fs-7">
                                                            <i class="fas fa-info-circle text-muted fs-3 mb-2 d-block"></i>
                                                            No event credit deductions configured for this operational package.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                        <div class="pkg-no-rules text-center py-6 text-muted fs-7" id="noOpRulesMatch" style="display: none;">
                                            <i class="fas fa-search text-muted opacity-50 fs-2 mb-2 d-block"></i>
                                            No operational activity rules match your search query.
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="pkg-card mt-4 p-6 text-center text-muted">
                                <i class="fas fa-shield-alt fa-2x text-muted opacity-40 mb-2 d-block"></i>
                                <div class="fw-bold fs-7 text-dark">No Active Operational Rules</div>
                                <div class="fs-8 text-muted mt-1">Assign an active operational package above to view and configure activity deduction thresholds.</div>
                            </div>
                        @endif
                    </div>
                    <!--end::packages Content-->
                    <!--begin::Requisitions Content-->
                    <div class="tab-pane" id="requisitions" role="tabpanel" aria-labelledby="tab-requisitions">
                        <div class="contacts-table-card">
                            <div class="contacts-table-header">
                                <div>
                                    <div class="contacts-table-title">
                                        Jobs <span id="jobsHeadingCount">({{ $active_requisition ?? 0 }})</span>
                                    </div>
                                    <div class="contacts-table-subtitle">
                                        View and manage all jobs and requisitions for this company.
                                    </div>
                                </div>
                                <div class="contacts-table-actions">
                                    <div class="search-box">
                                        <i class="fas fa-search"></i>
                                        <input type="text" id="inputJobSearch" name="inputJobSearch" placeholder="Search jobs..." autocomplete="off" />
                                    </div>
                                    @if (in_array('add_jobs', $userPermissions))
                                        <a href="{{ url('service_company/jobs/curstomcreate/' . $client->id) }}" class="btn btn-primary" id="addJobBtn">
                                            <i class="fas fa-plus"></i>
                                            Add Job
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <div class="table-card border-0 rounded-0 shadow-none mb-0">
                                <!-- Column header -->
                                <div class="table-header">
                                    <div class="th">Job Details</div>
                                    <div class="th">
                                        Experience
                                        <svg width="11" height="11" fill="none" viewBox="0 0 24 24">
                                            <rect x="3" y="4" width="18" height="16" rx="2" stroke="currentColor" stroke-width="1.8"/>
                                            <path d="M3 9h18M8 2v4M16 2v4" stroke="currentColor" stroke-width="1.8"/>
                                        </svg>
                                    </div>
                                    <div class="th">Hiring &amp; Location</div>
                                    <div class="th">Hiring Manager</div>
                                    <div class="th">Team</div>
                                    <div class="th">Agency</div>
                                    <div class="th">
                                        Hiring Pipeline
                                        <svg width="11" height="11" fill="none" viewBox="0 0 24 24">
                                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.8"/>
                                            <path d="M12 16v-4M12 8h.01" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                        </svg>
                                    </div>
                                </div>
                                <!-- Rows (AJAX) -->
                                <div class="loadingMessage loadingMessagejobListAjax" style="display: none; position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%); background: rgba(255,255,255,0.95); padding: 10px 24px; border-radius: 8px; box-shadow: 0 4px 16px rgba(0,0,0,0.08); z-index: 10; font-weight: 600; color: #009ef7;">Processing...</div>
                                <div id="jobListAjax"></div>
                            </div>
                            <div id="paginationJobs"></div>
                        </div>
                    </div>
                    <!--end::Requisitions Content-->
                    <!--begin::timeline Content-->
                    <div class="tab-pane" id="timeline" role="tabpanel" aria-labelledby="tab-timeline">
                        <!-- Tier 1: Toolbar Header Card -->
                        <div class="pkg-toolbar-card">
                            <div class="pkg-toolbar-left">
                                <div class="pkg-toolbar-icon pkg-icon-blue">
                                    <i class="fas fa-history"></i>
                                </div>
                                <div>
                                    <div class="pkg-toolbar-title">
                                        Coin Usage & Activity Timeline
                                        <span class="pkg-badge-pill">Audit Trail</span>
                                    </div>
                                    <div class="pkg-toolbar-subtitle">
                                        Chronological ledger of operational and sales coin deductions, balance top-ups, and activity settlements.
                                    </div>
                                </div>
                            </div>
                            <div class="pkg-search-box">
                                <i class="fas fa-search"></i>
                                <input type="text" id="searchTimelineEvents" class="pkg-search-input" placeholder="Search events, users, dates..." autocomplete="off">
                                <button type="button" class="pkg-search-clear" id="clearTimelineEvents" style="display: none;"><i class="fas fa-times"></i></button>
                            </div>
                        </div>

                        <!-- Tier 2: Sub-Tabs Card -->
                        <div class="pkg-card mb-4" style="padding: 6px 14px;">
                            <ul class="nav nav-tabs sa-nav-tabs border-0" role="tablist">
                                <li class="nav-item me-3" role="presentation">
                                    <a class="nav-link active" data-toggle="tab" role="tab" href="#salestab">
                                        <i class="fas fa-cubes me-1"></i>
                                        <span>Operational Package</span>
                                        <span class="tab-count ms-1">{{ count($coin_transection_logs ?? []) }}</span>
                                    </a>
                                </li>
                                <li class="nav-item me-3" role="presentation">
                                    <a class="nav-link" data-toggle="tab" role="tab" href="#operationaltab">
                                        <i class="fas fa-dollar-sign me-1"></i>
                                        <span>Sales Package</span>
                                        <span class="tab-count ms-1">{{ count($coin_transection_logs_sales ?? []) }}</span>
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <!-- Tier 3: Ledger Tables Stream -->
                        <div class="tab-content pt-0">
                            <!-- Tab 1: Operational Package Timeline -->
                            <div class="tab-pane active" id="salestab" role="tabpanel">
                                <div class="pkg-card mb-4">
                                    <div class="table-responsive pkg-rules-scroll-wrap">
                                        <table class="table align-middle pkg-rules-table mb-0">
                                            <thead>
                                                <tr>
                                                    <th style="min-width: 140px;">TIMESTAMP</th>
                                                    <th style="min-width: 220px;">ACTIVITY / EVENT</th>
                                                    <th style="min-width: 180px;">EXECUTED BY</th>
                                                    <th style="min-width: 150px;">TRANSACTION</th>
                                                    <th style="min-width: 110px; text-align: right;" class="pe-4">WALLET BALANCE</th>
                                                </tr>
                                            </thead>
                                            <tbody class="text-gray-700">
                                                @forelse ($coin_transection_logs as $transection_log)
                                                    @php
                                                        $searchStr = strtolower(
                                                            $transection_log->event . ' ' .
                                                            ($transection_log->name ?? '') . ' ' .
                                                            ($transection_log->company_name ?? '') . ' ' .
                                                            ($transection_log->transection_type ?? '') . ' ' .
                                                            date('d M Y', strtotime($transection_log->created_at))
                                                        );
                                                    @endphp
                                                    <tr class="timeline-log-row" data-search="{{ $searchStr }}">
                                                        <td>
                                                            <div class="d-flex flex-column">
                                                                <span class="fw-bold text-gray-900 fs-7">{{ date('d M Y', strtotime($transection_log->created_at)) }}</span>
                                                                <span class="fs-8 text-muted d-flex align-items-center gap-1 mt-1">
                                                                    <i class="far fa-clock fs-9"></i> {{ date('h:i A', strtotime($transection_log->created_at)) }}
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center gap-2">
                                                                <div class="pkg-event-icon-box" style="background: {{ $transection_log->transection_type === 'credit' ? '#e8fff3' : '#eff6ff' }}; color: {{ $transection_log->transection_type === 'credit' ? '#50cd89' : '#009ef7' }};">
                                                                    <i class="fas {{ $transection_log->transection_type === 'credit' ? 'fa-arrow-down' : 'fa-arrow-up' }}"></i>
                                                                </div>
                                                                <div>
                                                                    <div class="fw-bold text-gray-900 fs-7">{{ $transection_log->event }}</div>
                                                                    <span class="badge bg-light text-muted fs-8 py-0 px-2 mt-1">{{ ucwords($transection_log->event_type) }}</span>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center gap-2">
                                                                <div class="symbol symbol-30px symbol-circle bg-light-primary text-primary fw-bolder fs-8 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; border-radius: 50%; background: #f0f7ff; color: #009ef7; flex-shrink: 0;">
                                                                    {{ $transection_log->company_name ? strtoupper(substr($transection_log->name, 0, 1)) : 'S' }}
                                                                </div>
                                                                <div class="d-flex flex-column" style="min-width: 0;">
                                                                    <span class="fw-bold text-gray-800 fs-7 text-truncate" style="max-width: 150px;">{{ $transection_log->company_name ? $transection_log->name : 'System Default' }}</span>
                                                                    <span class="fs-8 text-muted text-truncate" style="max-width: 150px;">{{ $transection_log->company_name ?: 'System' }}</span>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            @if ($transection_log->settlement_coins <= 0)
                                                                @if ($transection_log->transection_type === 'credit')
                                                                    <span class="badge badge-light-success text-success fw-bold fs-7 d-inline-flex align-items-center gap-1">
                                                                        <i class="fas fa-plus fs-9"></i>{{ ($transection_log->actual_coins) ? $transection_log->actual_coins : $transection_log->coins }} Coins
                                                                    </span>
                                                                @else
                                                                    <span class="badge badge-light-danger text-danger fw-bold fs-7 d-inline-flex align-items-center gap-1">
                                                                        <i class="fas fa-minus fs-9"></i>{{ $transection_log->coins }} Coins
                                                                    </span>
                                                                @endif
                                                            @else
                                                                <span class="badge badge-light-info text-info fw-bold fs-7 d-inline-flex align-items-center gap-1">
                                                                    Settlement: {{ $transection_log->settlement_coins }} Coins
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td class="text-end pe-4">
                                                            <div class="d-inline-flex flex-column align-items-end">
                                                                <span class="fw-bolder text-gray-900 fs-7">{{ ($transection_log->remain_coins) ? $transection_log->remain_coins : $transection_log->coins }} Coins</span>
                                                                <span class="fs-8 text-muted">Balance</span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5" class="text-center py-6 text-muted">
                                                            <i class="fas fa-history fa-2x text-muted opacity-40 mb-2 d-block"></i>
                                                            No operational transaction logs recorded for this company yet.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                        <div class="no-timeline-match text-center py-6 text-muted fs-7" style="display: none;">
                                            <i class="fas fa-search text-muted opacity-50 fs-2 mb-2 d-block"></i>
                                            No operational activity logs match your search query.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab 2: Sales Package Timeline -->
                            <div class="tab-pane" id="operationaltab" role="tabpanel">
                                <div class="pkg-card mb-4">
                                    <div class="table-responsive pkg-rules-scroll-wrap">
                                        <table class="table align-middle pkg-rules-table mb-0">
                                            <thead>
                                                <tr>
                                                    <th style="min-width: 140px;">TIMESTAMP</th>
                                                    <th style="min-width: 220px;">ACTIVITY / EVENT</th>
                                                    <th style="min-width: 180px;">EXECUTED BY</th>
                                                    <th style="min-width: 150px;">TRANSACTION</th>
                                                    <th style="min-width: 110px; text-align: right;" class="pe-4">WALLET BALANCE</th>
                                                </tr>
                                            </thead>
                                            <tbody class="text-gray-700">
                                                @forelse ($coin_transection_logs_sales as $transection_log)
                                                    @php
                                                        $searchStr = strtolower(
                                                            $transection_log->event . ' ' .
                                                            ($transection_log->name ?? '') . ' ' .
                                                            ($transection_log->company_name ?? '') . ' ' .
                                                            ($transection_log->transection_type ?? '') . ' ' .
                                                            date('d M Y', strtotime($transection_log->created_at))
                                                        );
                                                    @endphp
                                                    <tr class="timeline-log-row" data-search="{{ $searchStr }}">
                                                        <td>
                                                            <div class="d-flex flex-column">
                                                                <span class="fw-bold text-gray-900 fs-7">{{ date('d M Y', strtotime($transection_log->created_at)) }}</span>
                                                                <span class="fs-8 text-muted d-flex align-items-center gap-1 mt-1">
                                                                    <i class="far fa-clock fs-9"></i> {{ date('h:i A', strtotime($transection_log->created_at)) }}
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center gap-2">
                                                                <div class="pkg-event-icon-box" style="background: {{ $transection_log->transection_type === 'credit' ? '#e8fff3' : '#eff6ff' }}; color: {{ $transection_log->transection_type === 'credit' ? '#50cd89' : '#009ef7' }};">
                                                                    <i class="fas {{ $transection_log->transection_type === 'credit' ? 'fa-arrow-down' : 'fa-arrow-up' }}"></i>
                                                                </div>
                                                                <div>
                                                                    <div class="fw-bold text-gray-900 fs-7">{{ $transection_log->event }}</div>
                                                                    <span class="badge bg-light text-muted fs-8 py-0 px-2 mt-1">{{ ucwords($transection_log->event_type) }}</span>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center gap-2">
                                                                <div class="symbol symbol-30px symbol-circle bg-light-primary text-primary fw-bolder fs-8 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; border-radius: 50%; background: #f0f7ff; color: #009ef7; flex-shrink: 0;">
                                                                    {{ $transection_log->company_name ? strtoupper(substr($transection_log->name, 0, 1)) : 'S' }}
                                                                </div>
                                                                <div class="d-flex flex-column" style="min-width: 0;">
                                                                    <span class="fw-bold text-gray-800 fs-7 text-truncate" style="max-width: 150px;">{{ $transection_log->company_name ? $transection_log->name : 'System Default' }}</span>
                                                                    <span class="fs-8 text-muted text-truncate" style="max-width: 150px;">{{ $transection_log->company_name ?: 'System' }}</span>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            @if ($transection_log->settlement_coins <= 0)
                                                                @if ($transection_log->transection_type === 'credit')
                                                                    <span class="badge badge-light-success text-success fw-bold fs-7 d-inline-flex align-items-center gap-1">
                                                                        <i class="fas fa-plus fs-9"></i>{{ ($transection_log->actual_coins) ? $transection_log->actual_coins : $transection_log->coins }} Coins
                                                                    </span>
                                                                @else
                                                                    <span class="badge badge-light-danger text-danger fw-bold fs-7 d-inline-flex align-items-center gap-1">
                                                                        <i class="fas fa-minus fs-9"></i>{{ $transection_log->coins }} Coins
                                                                    </span>
                                                                @endif
                                                            @else
                                                                <span class="badge badge-light-info text-info fw-bold fs-7 d-inline-flex align-items-center gap-1">
                                                                    Settlement: {{ $transection_log->settlement_coins }} Coins
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td class="text-end pe-4">
                                                            <div class="d-inline-flex flex-column align-items-end">
                                                                <span class="fw-bolder text-gray-900 fs-7">{{ ($transection_log->remain_coins) ? $transection_log->remain_coins : $transection_log->coins }} Coins</span>
                                                                <span class="fs-8 text-muted">Balance</span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5" class="text-center py-6 text-muted">
                                                            <i class="fas fa-history fa-2x text-muted opacity-40 mb-2 d-block"></i>
                                                            No sales transaction logs recorded for this company yet.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                        <div class="no-timeline-match text-center py-6 text-muted fs-7" style="display: none;">
                                            <i class="fas fa-search text-muted opacity-50 fs-2 mb-2 d-block"></i>
                                            No sales activity logs match your search query.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::timeline Content-->

                    <!--begin::boost keyword Content-->
                    <div class="tab-pane" id="boostkeyword" role="tabpanel" aria-labelledby="tab-boostkeyword">
                        <!-- Tier 1: Toolbar Card -->
                        <div class="pkg-toolbar-card">
                            <div class="pkg-toolbar-left">
                                <div class="pkg-toolbar-icon pkg-icon-purple">
                                    <i class="fas fa-rocket"></i>
                                </div>
                                <div>
                                    <div class="pkg-toolbar-title">
                                        Marketplace Search Boost & Discovery Keywords
                                        <span class="pkg-badge-pill">Discovery & Matching</span>
                                    </div>
                                    <div class="pkg-toolbar-subtitle">
                                        Assign strategic industry, domain, and role keywords to enhance candidate discovery algorithms and priority requisition matching.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tier 2: Enterprise Sub-Card -->
                        <div class="pkg-card">
                            <div class="pkg-card-header">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="pkg-event-icon-box" style="background: #f8f5ff; color: #7239ea;">
                                        <i class="fas fa-tags"></i>
                                    </div>
                                    <div>
                                        <h4 class="fs-6 fw-bolder text-gray-900 mb-0">Target Boost Keywords</h4>
                                        <span class="fs-8 text-muted">Elasticsearch talent marketplace indexing</span>
                                    </div>
                                </div>
                            </div>
                            <div class="pkg-card-body">
                                <form id="boost_keyword_form" class="form" action="#">
                                    <input type="hidden" name="keyword_compant_id" id="keyword_compant_id" value="{{ @$client->id }}" />

                                    <div class="mb-4">
                                        <label class="form-label required fs-7 fw-bold text-gray-700">Configured Keywords</label>
                                        <input class="form-control form-control-solid" name="tags" value="{{ $boostkeywords }}" placeholder="Type keywords and press Enter..." id="tags"/>
                                        <span class="fs-8 text-muted mt-1 d-block">Press Enter, Tab, or Comma after each tag to lock the keyword.</span>
                                    </div>

                                    <div class="mb-4">
                                        <label class="fs-8 fw-bold text-uppercase text-muted d-block mb-2">Recommended Suggestions (Click to Add):</label>
                                        <div class="d-flex flex-wrap gap-2" id="suggestedKeywordsStrip">
                                            <span class="pkg-suggest-pill" data-keyword="IT Services"><i class="fas fa-plus fs-9"></i> IT Services</span>
                                            <span class="pkg-suggest-pill" data-keyword="Staffing & Recruiting"><i class="fas fa-plus fs-9"></i> Staffing & Recruiting</span>
                                            <span class="pkg-suggest-pill" data-keyword="Fintech"><i class="fas fa-plus fs-9"></i> Fintech</span>
                                            <span class="pkg-suggest-pill" data-keyword="Healthcare"><i class="fas fa-plus fs-9"></i> Healthcare</span>
                                            <span class="pkg-suggest-pill" data-keyword="SaaS Enterprise"><i class="fas fa-plus fs-9"></i> SaaS Enterprise</span>
                                            <span class="pkg-suggest-pill" data-keyword="Artificial Intelligence"><i class="fas fa-plus fs-9"></i> Artificial Intelligence</span>
                                            <span class="pkg-suggest-pill" data-keyword="Cloud Computing"><i class="fas fa-plus fs-9"></i> Cloud Computing</span>
                                            <span class="pkg-suggest-pill" data-keyword="Executive Search"><i class="fas fa-plus fs-9"></i> Executive Search</span>
                                            <span class="pkg-suggest-pill" data-keyword="Product Development"><i class="fas fa-plus fs-9"></i> Product Development</span>
                                            <span class="pkg-suggest-pill" data-keyword="Remote Workforce"><i class="fas fa-plus fs-9"></i> Remote Workforce</span>
                                        </div>
                                    </div>

                                    <div class="p-3 rounded mb-4" style="background: #f8fafc; border: 1px dashed #cbd5e1;">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="fas fa-lightbulb text-warning fs-5"></i>
                                            <span class="fs-8 text-gray-600">Companies with 5+ strategic boost keywords receive up to <strong>40% higher candidate requisition engagement</strong> across the talent marketplace.</span>
                                        </div>
                                    </div>

                                    <div class="text-end pt-3 border-top border-gray-200">
                                        <button type="submit" id="boost_keyword_submit" class="btn btn-primary px-8 fw-bold">
                                            <span class="indicator-label indicator-label-top-up">
                                                <i class="fas fa-save me-1"></i>Save Boost Keywords
                                            </span>
                                            <span class="indicator-progress indicator-progress-top-up">
                                                Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                            </span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!--end::boost keyword Content-->

                </div>
            </div>
            <!--end::Body-->
        </div>
            <!--end:Tabs-->
    </div>
    <!--end::Content-->
</div>
<!--end::Body-->



<!--begin::Footer-->
<div class="card-footer py-5 text-center d-none" id="kt_activities_footer"> <a
        href="../../demo1/dist/pages/profile/activity.html" class="btn btn-bg-body text-primary">
        &nbsp;
        <span class="svg-icon svg-icon-3 svg-icon-primary"></span>
    </a> </div>
<!--end::Footer-->

</div>






<script>

var input = document.querySelector('input[name=tags]'); 
// initialize Tagify on the above input node reference
window.tagifyInstance = new Tagify(input);


        var country_id = "{{ $countrie_id }}";
        var state_id = "{{ $state_id }}";

        // alert(state_id);
        var city_id = "{{ $city_id }}";
        setTimeout(() => {
            $('#country').val(country_id).change();
        }, 200);


        $(document).on('change', '#country', function() {
            const country = $(this).val();
            const stateDropdown = $('#state');
            const cityDropdown = $('#city');

            var countrie_id = $('#country').val();
            if(countrie_id==101){
                
                $('#gstin_text').text('GSTIN')
            }else{
                $('#gstin_text').text('TAXIN')
            }


            // Reset State and City Dropdowns
            stateDropdown.empty().append('<option value="">Select State</option>').prop('disabled', true);
            cityDropdown.empty().append('<option value="">Select City</option>').prop('disabled', true);

            if (country) {
                // Fetch States via AJAX
                $.ajax({
                    url: '{{ route('get.states') }}',
                    method: 'GET',
                    data: {
                        country: country
                    },
                    success: function(response) {
                        if (response.length) {
                            response.forEach(function(state) {
                                stateDropdown.append(
                                    `<option value="${state.id}">${state.name}</option>`);
                            });
                            stateDropdown.prop('disabled', false);
                        }
                        $('#state').val(state_id).change();
                    },
                    error: function() {
                        alert('Failed to fetch states. Please try again.');
                    }
                });
            }
        });

        // Handle State Change
        $(document).on('change', '#state', function() {
            const state = $(this).val();
            const cityDropdown = $('#city');

            // Reset City Dropdown
            cityDropdown.empty().append('<option value="">Select City</option>').prop('disabled', true);

            if (state) {
                // Fetch Cities via AJAX
                $.ajax({
                    url: '{{ route('get.cities') }}',
                    method: 'GET',
                    data: {
                        state: state
                    },
                    success: function(response) {
                        if (response.length) {
                            response.forEach(function(city) {
                                cityDropdown.append(
                                    `<option value="${city.id}">${city.name}</option>`);
                            });
                            cityDropdown.prop('disabled', false);
                        }
                        $('#city').val(city_id).change();
                    },
                    error: function() {
                        alert('Failed to fetch cities. Please try again.');
                    }
                });
            }
        });




        $(document).on('submit', '#show-billing-form', function(e) {
            e.preventDefault(); // Prevent default form submission

            var formData = $(this).serialize(); // Serialize form data

            $.ajax({
                url: '{{ route('form.submit') }}', // Route to handle form submission
                method: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // CSRF Token header
                },
                success: function (response) {
                    // Handle success
                    Swal.fire("Success", "Billing Address updated successfully ", "success").then(
                                        function() {
                                            $('#usernamebill').text(response.usernamebill);
                                            $('#billing_address').text(response.street);
                                            $('#billing_gst').text(response.billing_gst);
                                            $('#billing_pan').text(response.billing_pan);
                                            $('#kt_activitiesApplication_close').click();
                                        });
                },
                error: function (response) {
                    // Handle error
                    console.log(response);
                    alert('Form submission failed');
                }
            });
        });




    $(document).ready(function() {


        // Payment Method Selector
        const paymentSelector = document.getElementById('payment-mode-selector');
        const paymentMethods = document.querySelectorAll('.payment-method');

        // Function to hide all payment sections
        function hideAllMethods() {
            paymentMethods.forEach(method => method.classList.remove('active'));
        }

        // Event listener for payment method selection
        paymentSelector.addEventListener('change', function() {

            hideAllMethods(); // Hide all sections
            const selectedMethod = this.value;
            if (selectedMethod) {
                if (selectedMethod == 'Cash') {
                    var activeSection = 'cash';
                } else if (selectedMethod == 'Cheque') {
                    var activeSection = 'cheque';
                } else if (selectedMethod == 'Bank Transfer') {
                    var activeSection = 'bank_transfer';
                } else if (selectedMethod == 'Payment Link') {
                    var activeSection = 'online';
                }
                document.getElementById(activeSection).classList.add('active');
            }
        });


    });


    function copyText() {
        // Get the text to be copied
        var textToCopy = document.getElementById("textToCopy").innerText;

        // Create a temporary input element
        var tempInput = document.createElement("input");
        tempInput.value = textToCopy;
        document.body.appendChild(tempInput);

        // Select the text
        tempInput.select();
        tempInput.setSelectionRange(0, 99999); // For mobile devices

        // Copy the text
        document.execCommand("copy");

        // Remove the temporary input element
        document.body.removeChild(tempInput);

        // Provide feedback to the user
        alert("Text copied to clipboard: " + textToCopy);
    }

        function paymentLinkcopyText(link) {
            // Get the text to be copied
            var textToCopy = link;

            // Create a temporary input element
            var tempInput = document.createElement("input");
            tempInput.value = textToCopy;
            document.body.appendChild(tempInput);

            // Select the text
            tempInput.select();
            tempInput.setSelectionRange(0, 99999); // For mobile devices

            // Copy the text
            document.execCommand("copy");

            // Remove the temporary input element
            document.body.removeChild(tempInput);

            // Provide feedback to the user
            alert("Link copied to clipboard: " + textToCopy);
        }

            $(document).on('shown.bs.tab', '.sa-company-drawer .sa-tabs-bar .nav-link', function (e) {
                $('.sa-company-drawer .sa-tabs-bar .nav-link').attr('aria-selected', 'false').attr('tabindex', '-1');
                $(e.target).attr('aria-selected', 'true').attr('tabindex', '0');
            });

            // W3C ARIA APG Keyboard Arrow Navigation for Tabs
            $(document).on('keydown', '.sa-company-drawer .sa-tabs-bar .nav-link', function (e) {
                var tabs = $('.sa-company-drawer .sa-tabs-bar .nav-link');
                var index = tabs.index(this);
                var targetIndex = -1;

                if (e.key === 'ArrowRight' || e.key === 'ArrowDown') {
                    e.preventDefault();
                    targetIndex = (index + 1) % tabs.length;
                } else if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') {
                    e.preventDefault();
                    targetIndex = (index - 1 + tabs.length) % tabs.length;
                } else if (e.key === 'Home') {
                    e.preventDefault();
                    targetIndex = 0;
                } else if (e.key === 'End') {
                    e.preventDefault();
                    targetIndex = tabs.length - 1;
                }

                if (targetIndex !== -1) {
                    var targetTab = tabs.eq(targetIndex);
                    targetTab.focus();
                    targetTab.trigger('click');
                }
            });

            // Keyboard accessibility for clickable custom rows (Enter/Space triggers click)
            $(document).on('keydown', '.sa-company-drawer [role="button"][tabindex="0"]', function (e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    $(this).trigger('click');
                }
            });

            $(document).on('click','.TeamListOpen',function(){
                fetchTeams();
            });

            $(document).ready(function() {
                    
                    $(document).on('click', '#paginationTeams .page-link', function(e) {
                            e.preventDefault();

                            const page = $(this).data('page');
                            if (!page || $(this).hasClass('disabled')) return;

                            // Disable the clicked pagination link
                            $(this).addClass('disabled');

                            // Fetch data
                            fetchTeams(page, $('#inputSearch').val()).then(() => {
                                // Re-enable the links after successful data fetch
                                $('#paginationTeams .page-link').removeClass('disabled');
                            }).catch(() => {
                                // Re-enable the links even if there's an error
                                $('#paginationTeams .page-link').removeClass('disabled');
                            });
                            
                    });
            });
            
            var page_no = localStorage.getItem('currentPageTeam');
            if (!page_no) {
                page_no = 1;
            }

			function fetchTeams(page = page_no, search = '') {
				$('.loadingMessageteamsListajax').css({ display: 'block' });
				return $.ajax({
					url: `{!! route('superadmin.company.contactdata') !!}?page=${page}`,
					type: 'GET',
					data: {
						search: $('#inputSearch').val(),
						cus_search: $('#kt_tagify_2').val(),
                        clientId: "{{ @$client->id }}",
						country: $('#country').val(),
						sortingfilter: $('#sortingfilter').val()
					},
					success: function(response) {
						$('.loadingMessageteamsListajax').css({ display: 'none' });
                        localStorage.setItem('currentPageTeam', page);
						$('#teamsListajax').html(response.html);
						$('#paginationTeams').html(response.pagination);
                        $('[data-bs-toggle="tooltip"], [data-toggle="tooltip"]').tooltip();
                        if (response.total !== undefined) {
                            $('#contactsHeadingCount').text('(' + response.total + ')');
                            $('.TeamListOpen .tab-count').text(response.total);
                        }
					},
                    error: function() {
                        $('.loadingMessageteamsListajax').css({ display: 'none' });
                    }
				});
			}

            var contactSearchTimer;
            $(document).on('keyup', '#inputSearch', function() {
                clearTimeout(contactSearchTimer);
                contactSearchTimer = setTimeout(function() {
                    fetchTeams(1);
                }, 300);
            });

            $(document).on('change', '#checkAllContacts', function() {
                var isChecked = $(this).is(':checked');
                $('#teamsListajax .chk_list_item:not(:disabled)').prop('checked', isChecked);
            });

            
</script>
<script>
    $(document).ready(function() {
        $('#successButtoncan').hide();
        $('#errorButtoncan').hide();

        $('#successButtoncanJob').hide();
        $('#errorButtoncanJob').hide();

        $('#successButtonclient').hide();
        $('#errorButtonclient').hide();

        $('#successButtonoperationaltopup').hide();
        $('#errorButtonoperationaltopup').hide();

        var jobSearchTimer;
        $('#inputJobSearch').on('keyup', function() {
            clearTimeout(jobSearchTimer);
            jobSearchTimer = setTimeout(function() {
                fetchCompanyJobs(1);
            }, 300);
        });

        var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
        $('.js-switch').each(function() {
            new Switchery($(this)[0], $(this).data());

        });


        $(document).on('click', '#editCompanyWallet', function() {


            $('#kt_activitiesWallet_toggle').trigger('click');
            var id = "{{ @$client->id }}";
            var url = "{{ route('superadmin.custompackages.editWallet', ':id') }}";
            url = url.replace(':id', id);
            var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
            $.ajax({
                type: 'GET',
                url: url,
                beforeSend: function() {
                    $('#kt_activitiesWallet_body').html('<img src="' + baseurl +
                        '" height="35px" width="100%">');
                },
                success: function(response) {
                    setTimeout(
                        () => {
                            if (response.status == "success") {
                                $('#kt_activitiesWallet_body').html(response.view);
                            }
                        }, 500);

                }
            });
        });




        $(document).on('click', '.comment_submit', function() {

            var id = $(this).attr('rel');
            var comment = $('#comment_' + id).val();
            if (comment == '') {
                return false;
            }



            var url = "{{ route('superadmin.custompackages.submitcomment', ':id') }}";
            url = url.replace(':id', id);
            var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
            var token = "{{ csrf_token() }}";
            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    '_token': token,
                    '_method': 'POST',
                    'id': id,
                    'comment': comment
                },
                cache: false,
                beforeSend: function() {
                    //$('#childStatus_'+current_application_id).html('<img src="'+baseurl+'" height="35px" width="100%">');
                },
                success: function(response) {
                    if (response.status == "success") {
                        Swal.fire("Success", 'Invoice comment submit successfully !',
                            "success")
                        // myPackageHistory._fnDraw();
                    } else {
                        Swal.fire("Warning", 'Opps something wrong, please try again !',
                            "info");
                        // myPackageHistory._fnDraw();
                    }
                }
            });


        });


    });

            $(document).on('click','.JobListOpen',function(){
                        fetchCompanyJobs();
            });
                    
            $(document).on('click', '#paginationJobs .page-link', function(e) {
                    e.preventDefault();

                    const page = $(this).data('page');
                    if (!page || $(this).hasClass('disabled')) return;

                    // Disable the clicked pagination link
                    $(this).addClass('disabled');

                    // Fetch data
                    fetchCompanyJobs(page, $('#inputJobSearch').val()).then(() => {
                        // Re-enable the links after successful data fetch
                        $('#paginationJobs .page-link').removeClass('disabled');
                    }).catch(() => {
                        // Re-enable the links even if there's an error
                        $('#paginationJobs .page-link').removeClass('disabled');
                    });
                    
            });
           
            
            var page_no = localStorage.getItem('currentPageJobs');
            if (!page_no) {
                page_no = 1;
            }

			function fetchCompanyJobs(page = page_no, search = '') {
				$('.loadingMessagejobListAjax').css({ display: 'block' });
				return $.ajax({
					url: `{!! route('superadmin.company.all_data') !!}?page=${page}`,
					type: 'GET',
					data: {
						search: $('#inputJobSearch').val(),
						cus_search: $('#kt_tagify_2').val(),
                        clientId: "{{ @$client->id }}",
						hid_compant_id: $('#hid_compant_id').val(),
						sortingfilter: $('#sortingfilter').val()
					},
					success: function(response) {
						$('.loadingMessagejobListAjax').css({ display: 'none' });
                        localStorage.setItem('currentPageJobs', page);
						$('#jobListAjax').html(response.html);
						$('#paginationJobs').html(response.pagination);
						if (response && response.total !== undefined) {
							$('#jobsHeadingCount').text('(' + response.total + ')');
							$('.JobListOpen .tab-count').text(response.total);
						}
					},
                    error: function() {
                        $('.loadingMessagejobListAjax').css({ display: 'none' });
                    }
				});
			}




/**
    var allreq = $('#allreq').dataTable({

        responsive: true,
        processing: true,
        serverSide: true,
        destroy: true,
        ordering: false, //disable ordering
        "bLengthChange": false, //thought this line could hide the LengthMenu
        sDom: 'lrtip',
        oLanguage: {
            sLengthMenu: "_MENU_",
        },
        "pageLength": 10,
        colReorder: true,
        ajax: {
            'url': '{!! route('superadmin.company.all_data') !!}',
            "data": function(d) {
                d.search = $('#inputJobSearch').val(), d.client_id = $('#hid_client_id').val(), d
                    .compant_id = $('#hid_compant_id').val()
            }
        },
        "drawCallback": function(settings) {
            $('[data-toggle="tooltip1"]').tooltip();
            $('[data-toggle="tooltip2"]').tooltip();
            $('[data-toggle="tooltip3"]').tooltip();
            $('[data-toggle="tooltip4"]').tooltip();
        },
        "columnDefs": [{
                "width": "50px",
                "targets": 0
            },
            {
                "width": "100px",
                "targets": 1,
                "class": "d-flex align-items-center border-0"
            },
            {
                "width": "50px",
                "targets": 2
            },
            {
                "width": "50px",
                "targets": 3
            },
            {
                "width": "200px",
                "targets": 4
            },
            {
                "width": "50px",
                "targets": 5
            },
            {
                "width": "200px",
                "targets": 6
            },
            {
                "width": "150px",
                "targets": 7
            },
            {
                "width": "150px",
                "targets": 8
            },

            {
                "width": "20px",
                "targets": 9
            },
            {
                "width": "20px",
                "targets": 10
            },
            {
                "width": "20px",
                "targets": 11
            },

            {
                "width": "20px",
                "targets": 12
            },
            {
                "width": "20px",
                "targets": 13
            },
            {
                "width": "20px",
                "targets": 14
            },
            {
                "width": "20px",
                "targets": 15
            },
            {
                "width": "20px",
                "targets": 16
            }
        ],
        columns: [{
                data: 'chkbox',
                name: 'chkbox',
                orderable: false,
                searchable: false
            },
            {
                data: 'Requisition',
                name: 'Requisition',
                orderable: false,
                searchable: false
            },
            {
                data: 'Exp',
                name: 'Exp',
                orderable: false,
                searchable: false
            },
            {
                data: 'Salary',
                name: 'Salary',
                orderable: false,
                searchable: false
            },
            {
                data: 'Location',
                name: 'Location',
                orderable: false,
                searchable: false
            },

            {
                data: 'Status',
                name: 'Status',
                orderable: false,
                searchable: false
            },
            {
                data: 'Date',
                name: 'Date',
                orderable: false,
                searchable: false
            },
            {
                data: 'HM',
                name: 'HM',
                orderable: false,
                searchable: false
            },

            {
                data: 'Partners',
                name: 'Partners',
                orderable: false,
                searchable: false
            },

            {
                data: 'TOT',
                name: 'TOT',
                orderable: false,
                searchable: false
            },
            {
                data: 'SOU',
                name: 'SOU',
                orderable: false,
                searchable: false
            },
            {
                data: 'SCR',
                name: 'SCR',
                orderable: false,
                searchable: false
            },

            {
                data: 'EVA',
                name: 'EVA',
                orderable: false,
                searchable: false
            },
            {
                data: 'NEO',
                name: 'NEO',
                orderable: false,
                searchable: false
            },
            {
                data: 'OFF',
                name: 'OFF',
                orderable: false,
                searchable: false
            },
            {
                data: 'JOI',
                name: 'JOI',
                orderable: false,
                searchable: false
            },
            {
                data: 'REJ',
                name: 'REJ',
                orderable: false,
                searchable: false
            }

        ]
    });
    new $.fn.dataTable.FixedHeader(allreq);

    **/




    var company_id = $('#hid_compant_id').val();
    var myPackageHistory = $('#myPackageHistory').dataTable({
        responsive: true,
        processing: true,
        ordering: false, //disable ordering       			
        destroy: true,
        stateSave: true,
        "bLengthChange": false,
        sDom: 'lrtip',
        //responsive		: true,
        // processing		: true,				
        searching: false,
        serverSide: true,
        ajax: '{!! route('superadmin.custompackages.myPackageHistory') !!}?company_id=' + company_id,
        language: {
            //"url": "<?php echo __('app.datatable'); ?>" 
        },
        "fnDrawCallback": function(oSettings) {
            $("body").tooltip({
                selector: '[data-toggle="tooltip"]'
            });
        },
        columns: [{
            data: 'Name',
            name: 'Name'
        }]
    });
    new $.fn.dataTable.FixedHeader(myPackageHistory);

    /* ── Client-Side Package Activity Rules Filter & Reactive Switch Badges (0% Server Strain) ── */
    $(document).on('keyup', '#searchOpActivityEvents', function() {
        var query = $(this).val().toLowerCase().trim();
        if (query.length > 0) {
            $('#clearOpActivityEvents').show();
        } else {
            $('#clearOpActivityEvents').hide();
        }
        var visibleCount = 0;
        $('#tableOpActivityRules tbody tr.pkg-rule-row').each(function() {
            var searchData = $(this).attr('data-search') || $(this).text().toLowerCase();
            if (searchData.indexOf(query) !== -1) {
                $(this).show();
                visibleCount++;
            } else {
                $(this).hide();
            }
        });
        if (visibleCount === 0) {
            $('#noOpRulesMatch').show();
        } else {
            $('#noOpRulesMatch').hide();
        }
    });

    $(document).on('click', '#clearOpActivityEvents', function() {
        $('#searchOpActivityEvents').val('').trigger('keyup');
    });

    $(document).on('keyup', '#searchSalesActivityEvents', function() {
        var query = $(this).val().toLowerCase().trim();
        if (query.length > 0) {
            $('#clearSalesActivityEvents').show();
        } else {
            $('#clearSalesActivityEvents').hide();
        }
        var visibleCount = 0;
        $('#tableSalesActivityRules tbody tr.pkg-rule-row').each(function() {
            var searchData = $(this).attr('data-search') || $(this).text().toLowerCase();
            if (searchData.indexOf(query) !== -1) {
                $(this).show();
                visibleCount++;
            } else {
                $(this).hide();
            }
        });
        if (visibleCount === 0) {
            $('#noSalesRulesMatch').show();
        } else {
            $('#noSalesRulesMatch').hide();
        }
    });

    $(document).on('click', '#clearSalesActivityEvents', function() {
        $('#searchSalesActivityEvents').val('').trigger('keyup');
    });

    // Reactive styling synchronization on toggle switch state change
    $(document).on('change', '.checkStatusOperational', function() {
        var isChecked = $(this).is(':checked');
        var $badge = $('.checkStatusOperational_text');
        if (isChecked) {
            $badge.removeClass('bg-light text-muted bg-light-danger text-danger').addClass('bg-light-success text-success').text('Active');
        } else {
            $badge.removeClass('bg-light-success text-success').addClass('bg-light text-muted').text('Inactive');
        }
    });

    $(document).on('change', '.checkStatusSales', function() {
        var isChecked = $(this).is(':checked');
        var $badge = $('.checkStatusSales_text');
        if (isChecked) {
            $badge.removeClass('bg-light text-muted bg-light-danger text-danger').addClass('bg-light-success text-success').text('Active');
        } else {
            $badge.removeClass('bg-light-success text-success').addClass('bg-light text-muted').text('Inactive');
        }
    });

    $(document).on('change', '.checkActive', function() {
        var isChecked = $(this).is(':checked');
        var $badge = $('.checkActivetext');
        if (isChecked) {
            $badge.removeClass('badge-light-danger text-danger').addClass('badge-light-success text-success').text('Active');
        } else {
            $badge.removeClass('badge-light-success text-success').addClass('badge-light-danger text-danger').text('Suspend');
        }
    });

    /* ── Client-Side Transaction History Filter (0% Server Strain) ── */
    $(document).on('keyup', '#searchTransactionHistory', function() {
        var query = $(this).val().toLowerCase().trim();
        if (query.length > 0) {
            $('#clearTransactionHistory').show();
        } else {
            $('#clearTransactionHistory').hide();
        }
        var visibleCount = 0;
        $('#myTable tbody tr.pkg-txn-row').each(function() {
            var searchData = $(this).attr('data-search') || $(this).text().toLowerCase();
            if (searchData.indexOf(query) !== -1) {
                $(this).show();
                visibleCount++;
            } else {
                $(this).hide();
            }
        });
        if (visibleCount === 0) {
            $('#noTxnMatch').show();
        } else {
            $('#noTxnMatch').hide();
        }
    });

    $(document).on('click', '#clearTransactionHistory', function() {
        $('#searchTransactionHistory').val('').trigger('keyup');
    });

    /* ── Click-to-add for Recommended Boost Keyword Pills ── */
    $(document).on('click', '.pkg-suggest-pill', function() {
        var kw = $(this).attr('data-keyword');
        if (window.tagifyInstance && kw) {
            window.tagifyInstance.addTags([kw]);
        }
    });

    /* ── Client-Side Timeline Activity Filter (0% Server Strain) ── */
    $(document).on('keyup', '#searchTimelineEvents', function() {
        var query = $(this).val().toLowerCase().trim();
        if (query.length > 0) {
            $('#clearTimelineEvents').show();
        } else {
            $('#clearTimelineEvents').hide();
        }
        var activeTab = $('#timeline .tab-pane.active');
        var visibleCount = 0;
        activeTab.find('tbody tr.timeline-log-row').each(function() {
            var searchData = $(this).attr('data-search') || $(this).text().toLowerCase();
            if (searchData.indexOf(query) !== -1) {
                $(this).show();
                visibleCount++;
            } else {
                $(this).hide();
            }
        });
        if (visibleCount === 0) {
            activeTab.find('.no-timeline-match').show();
        } else {
            activeTab.find('.no-timeline-match').hide();
        }
    });

    $(document).on('click', '#clearTimelineEvents', function() {
        $('#searchTimelineEvents').val('').trigger('keyup');
    });

    $(document).on('shown.bs.tab', '#timeline a[data-toggle="tab"]', function() {
        $('#searchTimelineEvents').trigger('keyup');
    });

</script>
