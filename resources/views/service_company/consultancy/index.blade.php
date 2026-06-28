@extends('layouts.service_company_layout')
@push('head-script')
    <link rel="stylesheet" href="//cdn.datatables.net/fixedheader/3.1.5/css/fixedHeader.bootstrap.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
    <style>
        body {
            overflow-x: hidden;
        }
        @media only screen and (min-width: 1024px) and (max-width: 1440px) {
            #kt_activitiesSP {
                width: 1025px;
            }
        }
        @media only screen and (min-width: 1440px) and (max-width: 2560px) {
            #kt_activitiesSP {
                width: 1200px;
            }
        }
        @media only screen and (min-width: 1024px) and (max-width: 1440px) {
            #kt_activitiesSP {
                width: 1025px;
            }
        }
        @media only screen and (min-width: 1440px) and (max-width: 2560px) {
            #kt_activitiesSP {
                width: 1200px;
            }
        }
        @media only screen and (min-width: 1024px) and (max-width: 1440px) {
            #kt_activitiesDirectClient {
                width: 1025px;
            }
        }
        @media only screen and (min-width: 1440px) and (max-width: 2560px) {
            #kt_activitiesClient {
                width: 1200px;
            }
        }
        @media only screen and (min-width: 1440px) and (max-width: 2560px) {
            #kt_activitiesDirectClient {
                width: 1200px;
            }
        }
    </style>
    
    
    <style>
      *,
      *::before,
      *::after {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
      }
      :root {
        --p: #5b4dff;
        --p-h: #4a3de6;
        --p-l: #f0eeff;
        --p-b: #e8e5ff;
        --s: #0b1120;
        --s-h: #1e293b;
        --s-a: #5b4dff;
        --t: #181c32;
        --t-m: #7e8299;
        --t-l: #a1a5b7;
        --b: #e4e6ef;
        --bg: #f5f8fa;
        --w: #fff;
        --g: #50cd89;
        --g-l: #e8fff3;
        --g-bg: #e8fff3;
        --r: #f1416c;
        --y: #ffc700;
        --y-e: #e4e6ef;
        --sh: 0 0 20px rgba(76, 87, 125, 0.08);
        --sh-card: 0 0 20px rgba(76, 87, 125, 0.04);
        --rad: 8px;
        --rad-sm: 6px;
      }
      html {
        font-size: 11px;
      }
      body {
        font-family:
          "Poppins",
          -apple-system,
          BlinkMacSystemFont,
          sans-serif;
        font-size: 1rem;
        color: var(--t);
        background: var(--bg);
        line-height: 1.5;
        overflow-x: hidden;
      }

      /* layout */
      .app {
        display: flex;
        min-height: 100vh;
      }

      /* sidebar */
      .sb {
        width: 210px;
        background: var(--s);
        color: var(--w);
        flex-shrink: 0;
        display: flex;
        flex-direction: column;
        position: fixed;
        height: 100vh;
        overflow-y: auto;
        z-index: 100;
      }
      .sb-h {
        padding: 16px 18px;
        display: flex;
        align-items: center;
        gap: 8px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
      }
      .sb-logo {
        width: 22px;
        height: 22px;
        background: var(--w);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
      }
      .sb-logo svg {
        width: 13px;
        height: 13px;
      }
      .sb-brand {
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--w);
        letter-spacing: -0.3px;
      }
      .nav {
        flex: 1;
        padding: 8px 0;
      }
      .nav-i {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 16px;
        color: #9899ac;
        text-decoration: none;
        transition: all 0.15s;
        cursor: pointer;
        position: relative;
        font-size: 1rem;
        font-weight: 500;
      }
      .nav-i:hover {
        background: var(--s-h);
        color: var(--w);
      }
      .nav-i.a {
        background: var(--s-a);
        color: var(--w);
      }
      .nav-i.a::before {
        content: "";
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 3px;
        background: var(--w);
        border-radius: 0 2px 2px 0;
      }
      .nav-ic {
        width: 18px;
        height: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
      }
      .nav-ic svg {
        width: 18px;
        height: 18px;
      }
      .nav-tx {
        font-size: 1.05rem;
        font-weight: 500;
      }
      .nav-bad {
        margin-left: auto;
        font-size: 0.85rem;
        opacity: 0.7;
      }
      .sub {
        padding-left: 44px;
      }
      .sub .nav-i {
        padding: 6px 16px;
      }
      .sub .nav-tx {
        font-size: 1rem;
      }
      .sb-f {
        padding: 12px 16px;
        border-top: 1px solid rgba(255, 255, 255, 0.06);
      }
      .sb-sup {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #9899ac;
        font-size: 1.05rem;
        cursor: pointer;
        padding: 5px 0;
      }
      .sb-sup:hover {
        color: var(--w);
      }
      .sb-up {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 16px;
        border-top: 1px solid rgba(255, 255, 255, 0.06);
        cursor: pointer;
      }
      .sb-av {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea, #764ba2);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--w);
        font-size: 1rem;
        font-weight: 600;
        flex-shrink: 0;
      }
      .sb-un {
        font-size: 1.05rem;
        font-weight: 600;
        color: var(--w);
        line-height: 1.3;
      }
      .sb-ur {
        font-size: 0.9rem;
        color: #9899ac;
        line-height: 1.3;
      }
      .sb-arr {
        color: #9899ac;
        font-size: 0.8rem;
        margin-left: auto;
      }

      /* main */
      .mc {
        flex: 1;
        margin-left: 210px;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
      }

      /* top bar */
      .tb {
        background: var(--w);
        padding: 12px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid var(--b);
        position: sticky;
        top: 0;
        z-index: 50;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
      }
      .tb-l {
        display: flex;
        align-items: center;
        gap: 12px;
      }
      .tb-s {
        display: flex;
        align-items: center;
        gap: 10px;
      }
      .tbi {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 7px 14px;
        background: var(--bg);
        border-radius: var(--rad-sm);
        font-size: 1rem;
        color: var(--t);
        border: 1px solid var(--b);
        font-weight: 500;
        cursor: pointer;
        transition: all 0.15s;
      }
      .tbi:hover {
        background: var(--p-l);
        border-color: var(--p);
        color: var(--p);
      }
      .tbi svg {
        width: 16px;
        height: 16px;
      }
      .tb-r {
        display: flex;
        align-items: center;
        gap: 10px;
      }
      .notif {
        position: relative;
        cursor: pointer;
        padding: 8px;
        border-radius: 50%;
        transition: all 0.15s;
      }
      .notif:hover {
        background: var(--bg);
      }
      .notif svg {
        width: 20px;
        height: 20px;
        color: var(--t-m);
      }
      .notif-dot {
        position: absolute;
        top: 6px;
        right: 6px;
        width: 8px;
        height: 8px;
        background: var(--r);
        border-radius: 50%;
        border: 2px solid var(--w);
      }
      .user-chip {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        padding: 4px 8px 4px 4px;
        border-radius: var(--rad-sm);
        transition: all 0.15s;
      }
      .user-chip:hover {
        background: var(--bg);
      }
      .uc-av {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea, #764ba2);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--w);
        font-size: 1rem;
        font-weight: 600;
      }
      .uc-info {
        display: flex;
        flex-direction: column;
      }
      .uc-n {
        font-size: 1rem;
        font-weight: 600;
        color: var(--t);
        line-height: 1.3;
      }
      .uc-r {
        font-size: 0.85rem;
        color: var(--t-m);
        line-height: 1.3;
      }
      .uc-arr {
        color: var(--t-m);
        font-size: 0.8rem;
      }

      /* page header */
      .ph {
        padding: 20px 24px 0;
      }
      .ph-h {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 4px;
      }
      .ph-t {
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--t);
        display: flex;
        align-items: center;
        gap: 8px;
      }
      .ph-t svg {
        width: 18px;
        height: 18px;
        color: var(--p);
      }
      .ph-s {
        font-size: 1.05rem;
        color: var(--t-m);
        font-weight: 400;
      }
      .ph-r {
        display: flex;
        align-items: center;
        gap: 10px;
      }
      .btn {
        padding: 8px 16px;
        border-radius: var(--rad-sm);
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.15s;
        white-space: nowrap;
        font-family: "Poppins", sans-serif;
      }
      .btn-o {
        background: var(--w);
        color: var(--t);
        border: 1px solid var(--b);
      }
      .btn-o:hover {
        background: var(--bg);
        border-color: var(--t-m);
      }
      .btn-p {
        background: var(--p);
        color: var(--w);
        box-shadow: 0 4px 12px rgba(91, 77, 255, 0.25);
      }
      .btn-p:hover {
        background: var(--p-h);
        box-shadow: 0 4px 16px rgba(91, 77, 255, 0.35);
      }
      .btn svg {
        width: 14px;
        height: 14px;
        flex-shrink: 0;
      }

      /* stats cards */
      .scards {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        padding: 16px 24px;
      }
      .scard {
        background: var(--w);
        border-radius: var(--rad);
        border: 1px solid var(--b);
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 14px;
        box-shadow: var(--sh-card);
        transition: all 0.2s;
      }
      .scard:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(76, 87, 125, 0.1);
      }
      .sc-ic {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
      }
      .sc-ic.b1 {
        background: #e8f4fd;
        color: #009ef7;
      }
      .sc-ic.b2 {
        background: #e8fff3;
        color: #50cd89;
      }
      .sc-ic.b3 {
        background: #f8f5ff;
        color: #7239ea;
      }
      .sc-ic.b4 {
        background: #fff8dd;
        color: #ffc700;
      }
      .sc-ic svg {
        width: 22px;
        height: 22px;
      }
      .sc-d {
        flex: 1;
      }
      .sc-l {
        font-size: 0.95rem;
        color: var(--t-m);
        font-weight: 500;
        margin-bottom: 2px;
      }
      .sc-v {
        font-size: 1.6rem;
        font-weight: 700;
        color: var(--t);
        margin-bottom: 2px;
      }
      .sc-s {
        font-size: 0.9rem;
        color: var(--t-l);
        font-weight: 400;
      }
      .sc-a {
        font-size: 0.95rem;
        color: var(--p);
        font-weight: 600;
        cursor: pointer;
        margin-top: 4px;
      }
      .sc-a:hover {
        text-decoration: underline;
      }

      /* search & filters */
      .sf {
        padding: 0 24px 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
      }
      .sf-s {
        flex: 1;
        min-width: 280px;
        position: relative;
      }
      .sf-s input {
        width: 100%;
        padding: 10px 14px 10px 40px;
        border: 1px solid var(--b);
        border-radius: var(--rad-sm);
        font-size: 1rem;
        color: var(--t);
        background: var(--w);
        font-family: "Poppins", sans-serif;
        transition: all 0.15s;
      }
      .sf-s input:focus {
        outline: none;
        border-color: var(--p);
        box-shadow: 0 0 0 3px rgba(91, 77, 255, 0.1);
      }
      .sf-s input::placeholder {
        color: var(--t-l);
      }
      .sf-s svg {
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        width: 18px;
        height: 18px;
        color: var(--t-l);
        pointer-events: none;
      }
      .sf-d {
        padding: 10px 14px;
        background: var(--w);
        border: 1px solid var(--b);
        border-radius: var(--rad-sm);
        font-size: 1rem;
        color: var(--t);
        font-family: "Poppins", sans-serif;
        cursor: pointer;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%237e8299' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        padding-right: 32px;
        min-width: 160px;
        transition: all 0.15s;
      }
      .sf-d:focus {
        outline: none;
        border-color: var(--p);
        box-shadow: 0 0 0 3px rgba(91, 77, 255, 0.1);
      }
      .sf-f {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 10px 14px;
        background: var(--w);
        border: 1px solid var(--b);
        border-radius: var(--rad-sm);
        font-size: 1rem;
        color: var(--t);
        cursor: pointer;
        font-family: "Poppins", sans-serif;
        transition: all 0.15s;
      }
      .sf-f:hover {
        border-color: var(--p);
        color: var(--p);
      }
      .sf-f svg {
        width: 16px;
        height: 16px;
      }

      /* table toolbar */
      .tt {
        padding: 0 24px 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
      }
      .tt-l {
        display: flex;
        align-items: center;
        gap: 12px;
      }
      .tt-cb {
        width: 16px;
        height: 16px;
        cursor: pointer;
        accent-color: var(--p);
      }
      .tt-s {
        font-size: 1rem;
        color: var(--p);
        font-weight: 600;
      }
      .tt-ba {
        padding: 6px 12px;
        background: var(--w);
        border: 1px solid var(--b);
        border-radius: var(--rad-sm);
        font-size: 1rem;
        color: var(--t);
        cursor: pointer;
        font-family: "Poppins", sans-serif;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%237e8299' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 10px center;
        padding-right: 28px;
      }
      .tt-r {
        display: flex;
        align-items: center;
        gap: 10px;
      }
      .tt-col {
        padding: 6px 12px;
        background: var(--w);
        border: 1px solid var(--b);
        border-radius: var(--rad-sm);
        font-size: 1rem;
        color: var(--t);
        cursor: pointer;
        font-family: "Poppins", sans-serif;
        display: flex;
        align-items: center;
        gap: 6px;
      }
      .tt-col svg {
        width: 14px;
        height: 14px;
      }
      .tt-so {
        padding: 6px 12px;
        background: var(--w);
        border: 1px solid var(--b);
        border-radius: var(--rad-sm);
        font-size: 1rem;
        color: var(--t);
        cursor: pointer;
        font-family: "Poppins", sans-serif;
        display: flex;
        align-items: center;
        gap: 6px;
      }
      .tt-so svg {
        width: 14px;
        height: 14px;
      }

      /* table */
      .tbl {
        padding: 0 0px;
      }
      .tbl-w {
        background: var(--w);
        border-radius: var(--rad);
        border: 1px solid var(--b);
        overflow: hidden;
        box-shadow: var(--sh-card);
      }
      .tbl-h {
        display: grid;
        grid-template-columns: 40px 1.8fr 1.3fr 0.9fr 2.6fr 1.1fr;
        padding: 10px 16px;
        background: var(--bg);
        border-bottom: 1px solid var(--b);
        align-items: center;
      }
      .tbl-h span {
        font-size: 1rem;
        font-weight: 600;
        color: var(--t-m);
        display: flex;
        align-items: center;
        gap: 4px;
      }
      .tbl-h span svg {
        width: 12px;
        height: 12px;
        color: var(--t-l);
      }
      .tbl-r {
        display: grid;
        grid-template-columns: 40px 1.8fr 1.3fr 0.9fr 2.6fr 1.1fr;
        padding: 14px 16px;
        border-bottom: 1px solid var(--b);
        align-items: center;
        transition: all 0.15s;
        cursor: pointer;
      }
      .tbl-r:hover {
        background: var(--bg);
      }
      .tbl-r:last-child {
        border-bottom: none;
      }
      .tbl-cb {
        width: 16px;
        height: 16px;
        cursor: pointer;
        accent-color: var(--p);
      }
      .tbl-a {
        display: flex;
        align-items: center;
        gap: 12px;
      }
      .tbl-av {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--w);
        font-size: 1.3rem;
        font-weight: 700;
        flex-shrink: 0;
        cursor: pointer;
        transition: all 0.15s;
      }
      .tbl-av:hover {
        transform: scale(1.08);
      }
      .tbl-ai {
        flex: 1;
        min-width: 0;
      }
      .tbl-n {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--t);
        margin-bottom: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
      }
      .tbl-t {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.95rem;
        color: var(--t-m);
        font-weight: 400;
      }
      .tbl-t svg {
        width: 14px;
        height: 14px;
        color: var(--g);
      }
      .tbl-l {
        display: flex;
        align-items: center;
        gap: 6px;
      }
      .tbl-l svg {
        width: 14px;
        height: 14px;
        color: var(--t-l);
        flex-shrink: 0;
      }
      .tbl-lc {
        font-size: 1rem;
        color: var(--t);
        font-weight: 600;
      }
      .tbl-ls {
        font-size: 0.9rem;
        color: var(--t-m);
        font-weight: 400;
      }
      .tbl-j {
        display: flex;
        flex-direction: column;
        gap: 3px;
      }
      .tbl-jr {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.95rem;
      }
      .tbl-jn {
        font-weight: 600;
        color: var(--t);
      }
      .tbl-js {
        font-weight: 500;
      }
      .tbl-js.a {
        color: var(--p);
      }
      .tbl-js.c {
        color: var(--t-m);
      }
      .tbl-js.d {
        color: var(--t-l);
      }
      .tbl-p {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
        flex-wrap: nowrap;
      }
      .tbl-pi {
        text-align: center;
        flex: 1;
        min-width: 0;
      }
      .tbl-pv {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--t);
      }
      .tbl-pl {
        font-size: 0.85rem;
        color: var(--t-m);
        font-weight: 400;
      }
      .tbl-co {
        font-size: 1rem;
        color: var(--t);
        font-weight: 500;
      }
      .tbl-cv {
        color: var(--p);
        font-weight: 600;
        cursor: pointer;
      }
      .tbl-cv:hover {
        text-decoration: underline;
      }

      /* pagination */
      .pg {
        padding: 14px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
      }
      .pg-l {
        font-size: 1rem;
        color: var(--t-m);
        font-weight: 400;
      }
      .pg-r {
        display: flex;
        align-items: center;
        gap: 6px;
      }
      .pg-s {
        padding: 6px 12px;
        background: var(--w);
        border: 1px solid var(--b);
        border-radius: var(--rad-sm);
        font-size: 1rem;
        color: var(--t);
        cursor: pointer;
        font-family: "Poppins", sans-serif;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%237e8299' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 8px center;
        padding-right: 26px;
      }
      .pg-b {
        padding: 6px 10px;
        background: var(--w);
        border: 1px solid var(--b);
        border-radius: var(--rad-sm);
        font-size: 1rem;
        color: var(--t);
        cursor: pointer;
        font-family: "Poppins", sans-serif;
        transition: all 0.15s;
        display: flex;
        align-items: center;
        justify-content: center;
      }
      .pg-b:hover {
        background: var(--bg);
        border-color: var(--p);
        color: var(--p);
      }
      .pg-b svg {
        width: 14px;
        height: 14px;
      }
      .pg-b.a {
        background: var(--p);
        color: var(--w);
        border-color: var(--p);
        font-weight: 600;
      }
      .pg-b.a:hover {
        background: var(--p-h);
      }
      .pg-d {
        padding: 6px 10px;
        font-size: 1rem;
        color: var(--t-m);
        font-weight: 500;
      }

      /* modal */
      .mdl {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 200;
        align-items: center;
        justify-content: center;
      }
      .mdl.a {
        display: flex;
      }
      .mdl-o {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
      }
      .mdl-c {
        background: var(--w);
        border-radius: var(--rad);
        width: 90%;
        max-width: 560px;
        max-height: 90vh;
        overflow-y: auto;
        position: relative;
        z-index: 1;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
      }
      .mdl-h {
        padding: 16px 20px;
        border-bottom: 1px solid var(--b);
        display: flex;
        align-items: center;
        justify-content: space-between;
      }
      .mdl-ht {
        font-size: 1.4rem;
        font-weight: 700;
        color: var(--t);
      }
      .mdl-x {
        padding: 6px;
        cursor: pointer;
        border-radius: 50%;
        transition: all 0.15s;
        background: none;
        border: none;
      }
      .mdl-x:hover {
        background: var(--bg);
      }
      .mdl-x svg {
        width: 18px;
        height: 18px;
        color: var(--t-m);
      }
      .mdl-b {
        padding: 20px;
      }
      .mdl-av {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--w);
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 16px;
      }
      .mdl-n {
        font-size: 1.4rem;
        font-weight: 700;
        color: var(--t);
        margin-bottom: 4px;
      }
      .mdl-t {
        font-size: 1rem;
        color: var(--t-m);
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 6px;
      }
      .mdl-t svg {
        width: 14px;
        height: 14px;
        color: var(--g);
      }
      .mdl-g {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-bottom: 16px;
      }
      .mdl-i {
        padding: 10px 12px;
        background: var(--bg);
        border-radius: var(--rad-sm);
      }
      .mdl-il {
        font-size: 0.9rem;
        color: var(--t-m);
        margin-bottom: 2px;
        font-weight: 400;
      }
      .mdl-iv {
        font-size: 1.1rem;
        color: var(--t);
        font-weight: 600;
      }
      .mdl-ct {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 16px;
      }
      .mdl-ci {
        padding: 8px;
        border-radius: 50%;
        background: var(--bg);
        cursor: pointer;
        transition: all 0.15s;
        position: relative;
      }
      .mdl-ci:hover {
        background: var(--p-l);
        color: var(--p);
      }
      .mdl-ci svg {
        width: 18px;
        height: 18px;
        color: var(--t-m);
      }
      .mdl-ci:hover svg {
        color: var(--p);
      }
      .mdl-p {
        display: flex;
        gap: 12px;
        margin-bottom: 16px;
      }
      .mdl-pb {
        flex: 1;
        padding: 12px;
        background: var(--bg);
        border-radius: var(--rad-sm);
        text-align: center;
      }
      .mdl-pv {
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--t);
        margin-bottom: 2px;
      }
      .mdl-pl {
        font-size: 0.9rem;
        color: var(--t-m);
        font-weight: 400;
      }
      .mdl-a {
        padding: 10px 16px;
        background: var(--p);
        color: var(--w);
        border-radius: var(--rad-sm);
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        border: none;
        font-family: "Poppins", sans-serif;
        width: 100%;
        text-align: center;
        transition: all 0.15s;
      }
      .mdl-a:hover {
        background: var(--p-h);
      }

      /* tooltip */
      .tip {
        position: absolute;
        bottom: calc(100% + 8px);
        left: 50%;
        transform: translateX(-50%);
        padding: 6px 12px;
        background: var(--t);
        color: var(--w);
        border-radius: var(--rad-sm);
        font-size: 0.9rem;
        font-weight: 500;
        white-space: nowrap;
        opacity: 0;
        pointer-events: none;
        transition: all 0.15s;
        z-index: 10;
      }
      .tip::after {
        content: "";
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%);
        border: 5px solid transparent;
        border-top-color: var(--t);
      }
      .mdl-ci:hover .tip {
        opacity: 1;
      }

      /* scrollbar */
      ::-webkit-scrollbar {
        width: 6px;
        height: 6px;
      }
      ::-webkit-scrollbar-track {
        background: var(--bg);
      }
      ::-webkit-scrollbar-thumb {
        background: var(--b);
        border-radius: 3px;
      }
      ::-webkit-scrollbar-thumb:hover {
        background: var(--t-l);
      }

      /* responsive */
      @media (max-width: 1280px) {
        .scards {
          grid-template-columns: repeat(2, 1fr);
        }
      }
      @media (max-width: 768px) {
        .sb {
          width: 60px;
        }
        .mc {
          margin-left: 60px;
        }
        .nav-tx,
        .sb-brand,
        .sb-un,
        .sb-ur,
        .sb-arr,
        .sb-sup span {
          display: none;
        }
        .scards {
          grid-template-columns: 1fr;
        }
        .sf {
          flex-direction: column;
        }
        .sf-s {
          width: 100%;
        }
        .tbl-h,
        .tbl-r {
          grid-template-columns: 40px 1fr;
          gap: 8px;
        }
        .tbl-h span:nth-child(3),
        .tbl-h span:nth-child(4),
        .tbl-h span:nth-child(5),
        .tbl-h span:nth-child(6),
        .tbl-r .tbl-l,
        .tbl-r .tbl-j,
        .tbl-r .tbl-p,
        .tbl-r .tbl-co {
          display: none;
        }
        .pg {
          flex-direction: column;
          gap: 10px;
        }
      }
      @media (max-width: 480px) {
        .sb {
          display: none;
        }
        .mc {
          margin-left: 0;
        }
      }
    </style>

    <link href="{{ asset('demo/css/filterStyle.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('demo/css/topCard.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />

@endpush
@section('content')
    @php

        $sp_url = route('service_company.consultancy.suggestedPartners', ':id');
        $sp_url = str_replace(':id', 2, $sp_url);

        $rp_url = route('service_company.consultancy.requestPartners', ':id');
        $rp_url = str_replace(':id', 2, $rp_url);

    @endphp
    <!--begin::Toolbar-->
    <div class="toolbar d-none" id="kt_toolbar">
        <!--begin::Container-->
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
            <div data-kt-swapper="true" data-kt-swapper-mode="prepend"
                data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
                class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                <div class="d-flex flex-column">
                    <h5 class="d-flex align-items-center text-dark fw-bolder my-1"> <a href="javascript:void(0);"
                            class="text-primary">{{__('messages.Agencies')}}</a> </h5>
                    <span class="text-gray-600" id="span_spartnerAll"></span>
                    {{-- {{ @$spartnerDataCnt['spartnerAll'] }} --}}
                </div>
                <span class="h-20px border-gray-200 border-start mx-4"></span>
    
            </div>
            <!--begin::Actions-->
            <div class="d-flex align-items-center py-1">
                <!--begin::Search-->
                <div class="d-flex align-items-center position-relative my-1 me-3">
                    <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->

                    <!--end::Svg Icon-->
                    <input type="hidden" data-kt-user-table-filter="search" name="inputSearch" id="inputSearch"
                        class="form-control form-control-solid w-250px ps-14" style="padding: .6rem 1rem;"
                        placeholder="Search..." />
                </div>
                <!--end::Search-->
                <div class="card-toolbar">
                    <!--begin::Toolbar-->
                    <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">

                        <!--begin::Menu 1-->
                        <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px" data-kt-menu="true">
                            <!--begin::Header-->
                            <div class="px-7 py-5">
                                <div class="fs-5 text-dark fw-bolder">Filter Options</div>
                            </div>
                            <!--end::Header-->
                            <!--begin::Separator-->
                            <div class="separator border-gray-200"></div>
                            <!--end::Separator-->
                            <!--begin::Content-->
                            <div class="px-7 py-5" data-kt-user-table-filter="form">
                                <!--begin::Input group-->
                                <div class="mb-10">
                                    <label class="form-label fs-6 fw-bold">Role:</label>
                                    <select class="form-select form-select-solid fw-bolder" data-kt-select2="true"
                                        data-placeholder="Select option" data-allow-clear="true"
                                        data-kt-user-table-filter="role" data-hide-search="true">
                                        <option></option>
                                        <option value="Administrator">Administrator</option>
                                        <option value="Analyst">Analyst</option>
                                        <option value="Developer">Developer</option>
                                        <option value="Support">Support</option>
                                        <option value="Trial">Trial</option>
                                    </select>
                                </div>
                                <!--end::Input group-->
                                <!--begin::Input group-->
                                <div class="mb-10">
                                    <label class="form-label fs-6 fw-bold">Two Step Verification:</label>
                                    <select class="form-select form-select-solid fw-bolder" data-kt-select2="true"
                                        data-placeholder="Select option" data-allow-clear="true"
                                        data-kt-user-table-filter="two-step" data-hide-search="true">
                                        <option></option>
                                        <option value="Enabled">Enabled</option>
                                    </select>
                                </div>
                                <!--end::Input group-->
                                <!--begin::Actions-->
                                <div class="d-flex justify-content-end">
                                    <button type="reset" class="btn btn-light btn-active-light-primary fw-bold me-2 px-6"
                                        data-kt-menu-dismiss="true" data-kt-user-table-filter="reset">Reset</button>
                                    <button type="submit" class="btn btn-primary fw-bold px-6" data-kt-menu-dismiss="true"
                                        data-kt-user-table-filter="filter">Apply</button>
                                </div>
                                <!--end::Actions-->
                            </div>
                            <!--end::Content-->
                        </div>
                        <!--end::Menu 1-->
                        <!--end::Filter-->
                    </div>
                    <!--end::Toolbar-->
                    <!--begin::Group actions-->
                    <div class="d-flex justify-content-end align-items-center d-none" data-kt-user-table-toolbar="selected">
                        <div class="fw-bolder me-5"> <span class="me-2"
                                data-kt-user-table-select="selected_count"></span>Selected</div>
                        <button type="button" class="btn btn-danger" data-kt-user-table-select="delete_selected">Delete
                            Selected</button>
                    </div>
                </div>
            </div>
            <!--end::Actions-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div class="content d-flex flex-column flex-column-fluid pt-4" id="kt_content" style="margin-top: -60px;">
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-fluid px-3">

                <main class="main-content">
                    <!-- Start Content Area -->
                    <div class="content-area">

                        

                        <!-- HERO -->
                        <div class="hero-section">
                            <div class="hero-left">
                                <div class="hero-title">
                                Agencies
                                <svg class="shield-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                    <path d="m9 12 2 2 4-4" stroke="#16A34A"/>
                                </svg>
                                </div>
                                <div class="hero-sub">Discover verified Agencies</div>
                                <div class="stats-row">
                                    <div class="stat-card">
                                        <div class="stat-icon blue">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                            <circle cx="9" cy="7" r="4"/>
                                            <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                                        </svg>
                                        </div>
                                        <div class="stat-info">
                                        <div class="stat-num">344</div>
                                        <div class="stat-label">Total Agencies</div>
                                        </div>
                                    </div>
                                    <div class="stat-card">
                                        <div class="stat-icon green">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                        </svg>
                                        </div>
                                        <div class="stat-info">
                                        <div class="stat-num">33</div>
                                        <div class="stat-label">Active Agencies</div>
                                        </div>
                                    </div>
                                    <div class="stat-card">
                                        <div class="stat-icon yellow">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                        </svg>
                                        </div>
                                        <div class="stat-info">
                                        <div class="stat-num">92</div>
                                        <div class="stat-label">New This Month</div>
                                        </div>
                                    </div>
                                    <div class="stat-card with-badge">
                                        <span class="new-badge">New</span>
                                        <div class="stat-icon purple">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                                        </svg>
                                        </div>
                                        <div class="stat-info">
                                        <div class="stat-num">24</div>
                                        <div class="stat-label">Top Performers</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Illustration -->
                            <div class="illus-svg-wrap">
                                <svg viewBox="0 0 220 160" xmlns="http://www.w3.org/2000/svg" width="220" height="160">
                                <!-- Background floating card (left) -->
                                <rect x="0" y="30" width="90" height="60" rx="10" fill="#EFF6FF" stroke="#BFDBFE" stroke-width="1"/>
                                <circle cx="16" cy="60" r="9" fill="#94A3B8" transform="translate(8,0)"/>
                                <rect x="34" y="52" width="46" height="6" rx="3" fill="#CBD5E1"/>
                                <rect x="34" y="62" width="32" height="4" rx="2" fill="#E2E8F0"/>
                                <rect x="34" y="70" width="22" height="4" rx="2" fill="#BFDBFE"/>
                                <!-- Stars -->
                                <text x="64" y="76" font-size="8" fill="#F59E0B">★★★★★</text>

                                <!-- Female figure -->
                                <ellipse cx="120" cy="148" rx="22" ry="6" fill="#E2E8F0" opacity=".5"/>
                                <!-- Body -->
                                <rect x="108" y="90" width="24" height="38" rx="4" fill="#2563EB"/>
                                <!-- Head -->
                                <circle cx="120" cy="82" r="12" fill="#FBBF24"/>
                                <!-- Hair -->
                                <path d="M108 80 Q120 68 132 80" fill="#1E293B"/>
                                <rect x="108" y="78" width="4" height="14" rx="2" fill="#1E293B"/>
                                <!-- Collar -->
                                <path d="M113 90 L120 98 L127 90" fill="white" opacity=".6"/>
                                <!-- Arms -->
                                <rect x="100" y="92" width="10" height="5" rx="2.5" fill="#2563EB"/>
                                <rect x="130" y="92" width="10" height="5" rx="2.5" fill="#2563EB"/>
                                <!-- Legs -->
                                <rect x="112" y="126" width="7" height="16" rx="3" fill="#1E293B"/>
                                <rect x="121" y="126" width="7" height="16" rx="3" fill="#1E293B"/>
                                <!-- Shoes -->
                                <ellipse cx="115" cy="143" rx="5" ry="3" fill="#111827"/>
                                <ellipse cx="124" cy="143" rx="5" ry="3" fill="#111827"/>

                                <!-- Male figure -->
                                <ellipse cx="175" cy="148" rx="22" ry="6" fill="#E2E8F0" opacity=".5"/>
                                <!-- Body -->
                                <rect x="163" y="90" width="24" height="38" rx="4" fill="#1E40AF"/>
                                <!-- Head -->
                                <circle cx="175" cy="82" r="12" fill="#D97706"/>
                                <!-- Hair -->
                                <path d="M163 78 Q175 68 187 78 Q185 72 175 70 Q165 72 163 78Z" fill="#1E293B"/>
                                <!-- Beard -->
                                <path d="M168 88 Q175 94 182 88 Q179 92 175 93 Q171 92 168 88Z" fill="#92400E" opacity=".7"/>
                                <!-- Collar -->
                                <path d="M168 90 L175 98 L182 90" fill="white" opacity=".6"/>
                                <!-- Tie -->
                                <path d="M175 94 L173 108 L175 110 L177 108 Z" fill="#EF4444"/>
                                <!-- Arms -->
                                <rect x="155" y="92" width="10" height="5" rx="2.5" fill="#1E40AF"/>
                                <rect x="185" y="92" width="10" height="5" rx="2.5" fill="#1E40AF"/>
                                <!-- Legs -->
                                <rect x="167" y="126" width="7" height="16" rx="3" fill="#1E293B"/>
                                <rect x="176" y="126" width="7" height="16" rx="3" fill="#1E293B"/>
                                <!-- Shoes -->
                                <ellipse cx="170" cy="143" rx="5" ry="3" fill="#111827"/>
                                <ellipse cx="179" cy="143" rx="5" ry="3" fill="#111827"/>

                                <!-- Handshake -->
                                <path d="M131 108 Q148 114 164 108" stroke="#F59E0B" stroke-width="3" stroke-linecap="round" fill="none"/>
                                <circle cx="131" cy="108" r="4" fill="#FBBF24"/>
                                <circle cx="164" cy="108" r="4" fill="#FBBF24"/>

                                <!-- Floating review card (right) -->
                                <rect x="150" y="10" width="68" height="38" rx="8" fill="white" stroke="#E5E7EB" stroke-width="1"/>
                                <circle cx="162" cy="22" r="5" fill="#94A3B8"/>
                                <rect x="171" y="16" width="36" height="5" rx="2.5" fill="#CBD5E1"/>
                                <text x="171" y="34" font-size="9" fill="#F59E0B">★★★★★</text>
                                <!-- shield check floating -->
                                <circle cx="205" cy="58" r="13" fill="#DCFCE7" stroke="#86EFAC" stroke-width="1.5"/>
                                <path d="M199 58l4 4 7-7" stroke="#16A34A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                                </svg>
                            </div>
                        </div>

                        <!-- FILTER BAR -->
                        <div class="filter-bar">
                            <div class="search-input-wrap">
                                <input type="text" class="search-input" name="inputJobSearch"
                                    value="" placeholder="Search....." id="kt_tagify_2" onchange="filtersdataget()" />
                            </div>
                            
                            <div class="col-lg-2 d-flex align-items-center px-2" style="width: 20%;">
                                <select class="form-select form-select-lg" data-hide-search="true"
                                    id="sortingfilter" data-dropdown-css-class="w-150px"
                                    data-placeholder="Select an option" onchange="filtersdataget()">
                                        <option value="Show All" selected="">Sort</option>
                                        <option value="1">Newest to Oldest (Upload Date)</option>
                                        <option value="2">Oldest to Newest (Upload Date)</option>
                                </select>
                            </div>
                            
                            <div class="col-lg-2 d-flex align-items-center px-2" style="width: 20%;">
                                <select class="form-select form-select-lg" data-hide-search="true"
                                    id="country" data-dropdown-css-class="w-150px"
                                    data-placeholder="Select an option" onchange="filtersdataget()">
                                    <option value="" selected="selected">Location</option>
                                    @if ($countries)
                                        @foreach ($countries as $country)
                                            <option value="{{ $country->id }}">{{ $country->country_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            
                            <div class="more-filters-btn" data-toggle="modal" data-target="#apptrackerfilter">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="4" y1="6" x2="20" y2="6"/><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="18" x2="20" y2="18"/>
                                </svg>
                                More Filters
                            </div>
                            @if (in_array('add_staffing_partners', $userPermissions))
                                <button class="ai-match-btn add_staffing_partners_frm" data-toggle="modal" data-target="#add_staffing_partners">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                                    </svg>
                                    Add {{__('messages.Agency')}}
                                </button>
                            @endif

                        </div>

                        <!-- Table -->
                        <div class="tbl">
                        <div class="tbl-w">
                            <div class="tbl-h">
                            <span></span>
                            <span
                                >Agency
                                <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="3"
                                >
                                <polyline points="6 9 12 15 18 9" /></svg
                            ></span>
                            <span>Location</span>
                            <span>Jobs</span>
                            <span
                                >Performance
                                <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                >
                                <circle cx="12" cy="12" r="10" />
                                <line x1="12" y1="16" x2="12" y2="12" />
                                <line x1="12" y1="8" x2="12.01" y2="8" /></svg
                            ></span>
                            <span>Connected On</span>
                            </div>
                              @if (in_array('view_staffing_partners', $userPermissions))
                                <div class="loadingMessage loadingMessagestaffingpartnerajax" id="" style="display: none; position: absolute;">Processing...</div>
                                <div id="staffingpartnerajax"></div>
                            @endif
                        </div>
                        </div>

                        <!-- Pagination -->
                        <div class="">
                            <div id="paginationstaffingpartner"></div>
                        </div>
                        


                    </div>     
                    <!-- End Content Area -->               
                </main>

            </div>
        </div>
    </div>
    <!--end::Content-->

    <!--begin::Add staffing partners - popup-->
    <!--end::Add staffing partners - popup-->
    <div id="kt_activitiesSP_toggle"></div>
    <div id="kt_activitiesSP" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities"
        data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width=""
        data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesSP_toggle"
        data-kt-drawer-close="#kt_activitiesSP_close">
        <div class="card shadow-none rounded-0 w-100">
            <div class="card-header px-5 py-0" id="kt_activities_header">
                <div class="d-flex align-items-center py-1">
                    <h3 class="card-title fw-bolder text-dark">{{__('messages.Agencies')}}</h3>
                    <span class="h-20px border-gray-200 border-start mx-4 d-none"></span>
                </div>
                <div class="card-toolbar">
                    <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5"
                        id="kt_activitiesSP_close"> <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)"
                                    fill="#000000">
                                    <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1" />
                                    <rect fill="#000000" opacity="0.5"
                                        transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)"
                                        x="0" y="7" width="16" height="2" rx="1" />
                                </g>
                            </svg>
                        </span> </button>
                </div>
            </div>
            <div class="card-body position-relative px-0 py-0" id="kt_activitiesSP_body"> &nbsp; </div>
        </div>
    </div>
    <!--begin::Add staffing partners - popup-->

    <div class="modal fade show" id="add_staffing_partners" tabindex="-1" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content">
                <div class="modal-header" id="add_staffing_partners_header">
                    <h2 class="fw-bolder">Invite {{__('messages.Agency')}} or Recruitment consultant</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary add_staffing_close" data-dismiss="modal">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                    transform="rotate(-45 6 17.3137)" fill="black"></rect>
                                <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                    transform="rotate(45 7.41422 6)" fill="black"></rect>
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="modal-body scroll-y mx-5 my-0">
                    <form id="add_staffing_partners_form" name="add_staffing_partners_form"
                        class="fv-row form fv-plugins-bootstrap5 fv-plugins-framework add_staffing_partners_form"
                        action="#">
                        <div class="d-flex flex-column scroll-y me-n7 pe-7" id="add_staffing_partners_scroll"
                            data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}"
                            data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#add_staffing_partners_header"
                            data-kt-scroll-wrappers="#add_staffing_partners_scroll" data-kt-scroll-offset="300px"
                            style="max-height: 10px;">
                            <div class="fv-row mb-7 fv-plugins-icon-container">
                                <label class="fw-bold fs-6 mb-2">Invite {{__('messages.Agency')}} or Consultant, by entering the email id</label>

                                <div class="d-flex">
                                    <input type="email" name="staffing_partners_email" id="staffing_partners_email"
                                    class="form-control form-control-solid me-3 flex-grow-1"
                                    placeholder="Enter email address" value="">
                                    <button type="button" class="btn btn-light fw-bold flex-shrink-0" id="submit_staffing_partners" style="width: 103px;"> <span
                                        class="indicator-label">Invite</span> <span class="indicator-progress">Wait...
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                </button>
                                </div>


                               
                                <div class="fv-plugins-message-container invalid-feedback" for="staffing_partners_email">
                                </div>
                            </div>
                        </div>
                        
                            @php
                            $encode				=	base64_encode(auth()->user()->company_id);
							$Url 				=	url('/company-registration-invitation/'.$encode);
                            @endphp
                            <div class="text-center pb-5">
                                <h4 class="fs-5 fw-semibold text-gray-500">OR</h4>
                            </div>
                            <!--begin::Input wrapper-->
                                <div class="w-100">
                                    <!--begin::Title-->
                                    <h4 class="fw-bold fs-6 mb-2">Share below referral link to invite {{__('messages.Agencies')}}  </h4>
                                    <!--end::Title-->

                                    <!--begin::Title-->
                                    <div class="d-flex">
                                        <input id="kt_share_earn_link_input" type="text" class="form-control form-control-solid me-3 flex-grow-1"
                                        name="search" value="{{$Url}}" readonly/>

                                        <button id="kt_share_earn_link_copy_button" class="btn btn-light fw-bold flex-shrink-0"
                                        data-clipboard-target="#kt_share_earn_link_input" style="width: 103px;">Copy Link</button>
                                    </div>
                                    <!--end::Title-->
                                </div>
                            <!--end::Input wrapper-->
                            <br>
                            <div class="text-center pt-5">
                            <button type="button" class="btn btn-light me-3" data-kt-users-modal-action="close"
                                data-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--end::Add staffing partners - popup-->

    <div id="kt_activitiesDirectClient_toggle"></div>
    <div id="kt_activitiesDirectClient" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities"
        data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-direction="end"
        data-kt-drawer-toggle="#kt_activitiesDirectClient_toggle" data-kt-drawer-close="#kt_activitiesDirectClient_close">
        <div class="card shadow-none rounded-0 w-100">
            <div class="card-header px-5 py-0" id="kt_activities_header">
                <div class="d-flex align-items-center py-1">
                    <h3 class="card-title fw-bolder text-dark">{{__('messages.Agency')}} Detail</h3>
                    <span class="h-20px border-gray-200 border-start mx-4 d-none"></span>
                </div>
                <div class="card-toolbar">
                    <button type="button" class="btn btn-sm btn-icon btn-active-light-danger me-n5"
                        id="kt_activitiesDirectClient_close"> <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)"
                                    fill="currentColor">
                                    <rect x="0" y="7" width="16" height="2" rx="1" />
                                    <rect opacity="0.5"
                                        transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)"
                                        x="0" y="7" width="16" height="2" rx="1" />
                                </g>
                            </svg>
                        </span> </button>
                </div>
            </div>
            <div class="card-body position-relative px-0 py-0" id="kt_activitiesDirectClient_body"> &nbsp; </div>
        </div>
    </div>

@endsection

@push('footer-script')
    <script src="{{ asset('demo/cdn/js/dataTables.fixedHeader.min.js') }}<?php echo TM_VERSION; ?>"></script>


    <script>

$(document).ready(function() {
    var $button = $('#kt_share_earn_link_copy_button');
    var $input = $('#kt_share_earn_link_input');
    var clipboard = new ClipboardJS('#kt_share_earn_link_copy_button');

    if (!clipboard) {
        return;
    }

    // Copy text to clipboard
    clipboard.on('success', function(e) {
        var buttonCaption = $button.html();
        
        // Add background color
        $input.addClass('bg-success text-inverse-success');
        $button.html('Copied!');

        setTimeout(function() {
            $button.html(buttonCaption);

            // Remove background color
            $input.removeClass('bg-success text-inverse-success');
        }, 3000); // 3 seconds

        e.clearSelection();
    });
});


        // var input2 = document.querySelector("#kt_tagify_2");
        // new Tagify(input2);
    </script>
    <script>
         $(document).on('click', '.add_staffing_partners_frm', function() {
            $('#staffing_partners_email').val('');
        });


        $(document).on('keyup', '#kt_tagify_2', function() {
            // $('#myTable').DataTable().draw();
            fetchstaffingpartner();
            //  table._fnDraw();
        });

        function filtersdataget() {
            // $('#myTable').DataTable().draw();
            fetchstaffingpartner();
        }
        // var table = $('#myTable').dataTable({

        //     responsive: true,
        //     processing: true,
        //     serverSide: true,
        //     destroy: true,
        //     ordering: false, //	disable ordering			
        //     "bLengthChange": false, //	thought this line could hide the LengthMenu
        //     sDom: 'lrtip',
        //     oLanguage: {
        //         sLengthMenu: "_MENU_",
        //     },
        //     ajax: {
        //         'url': '{!! route('service_company.consultancy.data') !!}',
        //         "data": function(d) {
        //             d.search = $('#inputSearch').val(), d.sortingfilter = $('#sortingfilter').val(), d.country =
        //                 $('#country').val(), d.cus_search = $('#kt_tagify_2').val()
        //         }
        //     },
        //     "fnDrawCallback": function(oSettings) {
        //         $("body").tooltip({
        //             selector: '[data-toggle="tooltip"]'
        //         });
        //     },
        //     columns: [{
        //             data: 'chkbox',
        //             name: 'chkbox',
        //             searchable: false,
        //             orderable: false
        //         },
        //         {
        //             data: 'Name',
        //             name: 'Name',
        //             searchable: false,
        //             orderable: false
        //         },
        //         {
        //             data: 'Location',
        //             name: 'Location',
        //             searchable: false,
        //             orderable: false
        //         },
        //         {
        //             data: 'Joining',
        //             name: 'Joining',
        //             searchable: false,
        //             orderable: false
        //         },
        //         {
        //             data: 'Requisitions',
        //             name: 'Requisitions',
        //             searchable: false,
        //             orderable: false
        //         },
        //         {
        //             data: 'ConnectedOn',
        //             name: 'ConnectedOn',
        //             searchable: false,
        //             orderable: false
        //         }
        //         // { data: 'ContactPerson', name: 'ContactPerson', searchable : false, orderable : false}    					
        //     ]
        // });
        // new $.fn.dataTable.FixedHeader(table);
    </script>

    <!--<script src="{{ asset('js/dataTables.fixedHeader.min.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('js/responsive.bootstrap.min.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('js/dataTables.colReorder.min.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('js/dataTables.buttons.min.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('js/buttons.colVis.min.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}<?php echo TM_VERSION; ?>"
        type="text/javascript"></script>
    <script src="{{ asset('assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}<?php echo TM_VERSION; ?>"
        type="text/javascript"></script>
    <script src="//cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
    <script src="{{ asset('assets/node_modules/moment/moment.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
    <script src="{{ asset('assets/node_modules/multiselect/js/jquery.multi-select.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('assets/plugins/iCheck/icheck.min.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('assets/node_modules/bootstrap-datepicker/bootstrap-datepicker.min.js') }}<?php echo TM_VERSION; ?>"
        type="text/javascript"></script>
    <script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}<?php echo TM_VERSION; ?>"
        type="text/javascript"></script>
    <script src="{{ asset('assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}<?php echo TM_VERSION; ?>"
        type="text/javascript"></script>
    <script src="{{ asset('assets/node_modules/moment/moment.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/calendar/dist/fullcalendar.min.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('assets/plugins/calendar/dist/jquery.fullcalendar.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('assets/plugins/calendar/dist/locale-all.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('js/schedule-calendar.js') }}<?php echo TM_VERSION; ?>"></script>-->


    <script>
        $(document).ready(function() {

            var KTUsersAddUser = function() {
                // Shared variables
                const element = document.getElementById('add_staffing_partners');
                const form = element.querySelector('#add_staffing_partners_form');
                const modal = new bootstrap.Modal(element);

                // Init add schedule modal
                var initAddUser = () => {

                    // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
                    var validator = FormValidation.formValidation(
                        form, {
                            fields: {
                                user_email: {
                                    validators: {
                                        notEmpty: {
                                            message: "Email address is required"
                                        },
                                        emailAddress: {
                                            message: "The value is not a valid email address"
                                        }
                                    }
                                }
                            },

                            plugins: {
                                trigger: new FormValidation.plugins.Trigger(),
                                bootstrap: new FormValidation.plugins.Bootstrap5({
                                    rowSelector: '.fv-row',
                                    eleInvalidClass: '',
                                    eleValidClass: ''
                                })
                            }
                        }
                    );

                    // Submit button handler
                    const submitButton = element.querySelector('[data-kt-users-modal-action="submit"]');
                    submitButton.addEventListener('click', e => {
                        e.preventDefault();

                        // Validate form before submit
                        if (validator) {
                            validator.validate().then(function(status) {
                                console.log('validated!');

                                if (status == 'Valid') {
                                    // Show loading indication
                                    submitButton.setAttribute('data-kt-indicator', 'on');

                                    // Disable button to avoid multiple click 
                                    submitButton.disabled = true;

                                    // Simulate form submission. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                                    setTimeout(function() {
                                        // Remove loading indication
                                        submitButton.removeAttribute(
                                            'data-kt-indicator');

                                        // Enable button
                                        submitButton.disabled = false;

                                        // Show popup confirmation 
                                        Swal.fire({
                                                text: "Invitation has been sent!",
                                                icon: "success",
                                                buttonsStyling: false,
                                                confirmButtonText: "Ok, got it!",
                                                customClass: {
                                                    confirmButton: "btn btn-primary"
                                                }
                                            })
                                            // .then(function (t)
                                            // {
                                            //    t.isConfirmed && e && e.hide();
                                            // });
                                            .then(function() {
                                                window.location = "";
                                            });

                                        //form.submit(); // Submit form
                                    }, 2e3);
                                } else {
                                    // Show popup warning. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                                    Swal.fire({
                                        text: "Sorry, looks like there are some errors detected, please try again.",
                                        icon: "error",
                                        buttonsStyling: false,
                                        confirmButtonText: "Ok, got it!",
                                        customClass: {
                                            confirmButton: "btn btn-primary"
                                        }
                                    });
                                }
                            });
                        }
                    });

                    // Cancel button handler
                    const cancelButton = element.querySelector('[data-kt-users-modal-action="cancel"]');
                    cancelButton.addEventListener('click', e => {
                        e.preventDefault();

                        Swal.fire({
                            text: "Are you sure you would like to cancel?",
                            icon: "warning",
                            showCancelButton: true,
                            buttonsStyling: false,
                            confirmButtonText: "Yes, cancel it!",
                            cancelButtonText: "No, return",
                            customClass: {
                                confirmButton: "btn btn-primary",
                                cancelButton: "btn btn-active-light"
                            }
                        }).then(function(result) {
                            if (result.value) {
                                form.reset(); // Reset form			
                                modal.hide();
                            } else if (result.dismiss === 'cancel') {
                                Swal.fire({
                                    text: "Your form has not been cancelled!.",
                                    icon: "error",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, got it!",
                                    customClass: {
                                        confirmButton: "btn btn-primary",
                                    }
                                });
                            }
                        });
                    });

                    // Close button handler
                    const closeButton = element.querySelector('[data-kt-users-modal-action="close"]');
                    closeButton.addEventListener('click', e => {
                        e.preventDefault();

                        Swal.fire({
                            text: "Are you sure you would like to cancel?",
                            icon: "warning",
                            showCancelButton: true,
                            buttonsStyling: false,
                            confirmButtonText: "Yes, cancel it!",
                            cancelButtonText: "No, return",
                            customClass: {
                                confirmButton: "btn btn-primary",
                                cancelButton: "btn btn-active-light"
                            }
                        }).then(function(result) {
                            if (result.value) {
                                form.reset(); // Reset form			
                                modal.hide();
                            } else if (result.dismiss === 'cancel') {
                                Swal.fire({
                                    text: "Your form has not been cancelled!.",
                                    icon: "error",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, got it!",
                                    customClass: {
                                        confirmButton: "btn btn-primary",
                                    }
                                });
                            }
                        });
                    });
                }

                return {
                    // Public functions
                    init: function() {
                        initAddUser();
                    }
                };
            }();

            KTUtil.onDOMContentLoaded(function() {
                KTUsersAddUser.init();
            });
        });


        $(document).on('click', '.sa-params', function() {
            var id = $(this).data('row-id');
            swal({
                title: "@lang('errors.areYouSure')",
                text: "@lang('errors.deleteWarning')",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "@lang('app.delete')",
                cancelButtonText: "@lang('app.cancel')",
                closeOnConfirm: true,
                closeOnCancel: true
            }, function(isConfirm) {
                if (isConfirm) {

                    var url = "{{ route('service_company.consultancy.destroy', ':id') }}";
                    url = url.replace(':id', id);

                    var token = "{{ csrf_token() }}";

                    $.easyAjax({
                        type: 'POST',
                        url: url,
                        data: {
                            '_token': token,
                            '_method': 'DELETE'
                        },
                        success: function(response) {
                            if (response.status == "success") {
                                $.unblockUI();
                                //                                    swal("Deleted!", response.message, "success");
                                table._fnDraw();
                            }
                        }
                    });
                }
            });
        });

        function exportExcel() {
            var table = $('#myTable').dataTable();
            var length = table.fnGetData().length;
            var oSettings = table.fnSettings();
            var start = oSettings._iDisplayStart;
            var url = '{{ route('service_company.consultancy.export', [':start', ':length']) }}';
            url = url.replace(':start', start);
            url = url.replace(':length', length);
            window.location.href = url;
        }
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"
        type="text/javascript"></script>
    <script>
        $().ready(function() {

            $("#add_staffing_partners_form").validate({
                rules: {
                    email: {
                        required: true,
                        email: true
                    }
                },
                messages: {

                    email: "Please enter a valid email address"
                }
            });

        });




        $(document).on('click', '#submit_staffing_partners', function() {

            $("#add_staffing_partners_form").valid();

            var staffing_partners_email = $('#staffing_partners_email').val();
            var id = 0;
            var url = "{{ route('service_company.consultancy.staffing_invitation', 'id') }}";
            url = url.replace(':id', id);
            $('#submit_staffing_partners').prop('disabled', true);
            $('.indicator-label').hide();
            $('.indicator-progress').show();
            $.ajax({

                type: 'GET',
                url: url,
                data: {
                    'staffing_partners_email': staffing_partners_email
                },
                success: function(response) {
                    $('#submit_staffing_partners').prop('disabled', false);
                    $('.indicator-label').show();
                    $('.indicator-progress').hide();
                    $('#staffing_partners_email').val('');
                    if (response.status == "warning") {
                        swal({
                            title: "Failed!",
                            text: response.message,
                            type: "warning",
                            timer: 1000,
                            showConfirmButton: true
                        }, function() {
                            //location.reload();
                        });

                    }

                    if (response.status == "success") {

                        var spartnerAll = response.spartnerDataCnt.spartnerAll;
                        var sentRequest = response.spartnerDataCnt.sentRequest;
                        $('#span_spartnerAll').html(spartnerAll);
                        $('#span_sentRequest').html(sentRequest);

                        swal({
                            title: "Success!",
                            text: response.message,
                            html: true,
                            type: "success",
                            timer: 1000,
                            showConfirmButton: true
                        }, function() {
                            $('.add_staffing_close').click();
                            //location.reload();
                        });

                    }
                }

            });


        });

        $(document).ready(function() {
            // Class definition
            var KTUsersAddUser = function() {
                // Shared variables
                const element = document.getElementById('add_staffing_partners');
                const form = element.querySelector('#add_staffing_partners_form');
                const modal = new bootstrap.Modal(element);

                // Init add schedule modal
                var initAddUser = () => {

                    // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
                    var validator = FormValidation.formValidation(
                        form, {
                            fields: {
                                user_email: {
                                    validators: {
                                        notEmpty: {
                                            message: "Email address is required"
                                        },
                                        emailAddress: {
                                            message: "The value is not a valid email address"
                                        }
                                    }
                                }
                            },

                            plugins: {
                                trigger: new FormValidation.plugins.Trigger(),
                                bootstrap: new FormValidation.plugins.Bootstrap5({
                                    rowSelector: '.fv-row',
                                    eleInvalidClass: '',
                                    eleValidClass: ''
                                })
                            }
                        }
                    );

                    // Submit button handler
                    const submitButton = element.querySelector('[data-kt-users-modal-action="submit"]');
                    submitButton.addEventListener('click', e => {
                        e.preventDefault();

                        // Validate form before submit
                        if (validator) {
                            validator.validate().then(function(status) {
                                console.log('validated!');

                                if (status == 'Valid') {
                                    // Show loading indication
                                    submitButton.setAttribute('data-kt-indicator', 'on');

                                    // Disable button to avoid multiple click 
                                    submitButton.disabled = true;

                                    // Simulate form submission. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                                    setTimeout(function() {
                                        // Remove loading indication
                                        submitButton.removeAttribute(
                                            'data-kt-indicator');

                                        // Enable button
                                        submitButton.disabled = false;

                                        // Show popup confirmation 
                                        Swal.fire({
                                                text: "Invitation has been sent!",
                                                icon: "success",
                                                buttonsStyling: false,
                                                confirmButtonText: "Ok, got it!",
                                                customClass: {
                                                    confirmButton: "btn btn-primary"
                                                }
                                            })
                                            // .then(function (t)
                                            // {
                                            //    t.isConfirmed && e && e.hide();
                                            // });
                                            .then(function() {
                                                window.location = "";
                                            });

                                        //form.submit(); // Submit form
                                    }, 2e3);
                                } else {
                                    // Show popup warning. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                                    Swal.fire({
                                        text: "Sorry, looks like there are some errors detected, please try again.",
                                        icon: "error",
                                        buttonsStyling: false,
                                        confirmButtonText: "Ok, got it!",
                                        customClass: {
                                            confirmButton: "btn btn-primary"
                                        }
                                    });
                                }
                            });
                        }
                    });

                    // Cancel button handler
                    const cancelButton = element.querySelector('[data-kt-users-modal-action="cancel"]');
                    cancelButton.addEventListener('click', e => {
                        e.preventDefault();

                        Swal.fire({
                            text: "Are you sure you would like to cancel?",
                            icon: "warning",
                            showCancelButton: true,
                            buttonsStyling: false,
                            confirmButtonText: "Yes, cancel it!",
                            cancelButtonText: "No, return",
                            customClass: {
                                confirmButton: "btn btn-primary",
                                cancelButton: "btn btn-active-light"
                            }
                        }).then(function(result) {
                            if (result.value) {
                                form.reset(); // Reset form			
                                modal.hide();
                            } else if (result.dismiss === 'cancel') {
                                Swal.fire({
                                    text: "Your form has not been cancelled!.",
                                    icon: "error",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, got it!",
                                    customClass: {
                                        confirmButton: "btn btn-primary",
                                    }
                                });
                            }
                        });
                    });

                    // Close button handler
                    const closeButton = element.querySelector('[data-kt-users-modal-action="close"]');
                    closeButton.addEventListener('click', e => {
                        e.preventDefault();

                        Swal.fire({
                            text: "Are you sure you would like to cancel?",
                            icon: "warning",
                            showCancelButton: true,
                            buttonsStyling: false,
                            confirmButtonText: "Yes, cancel it!",
                            cancelButtonText: "No, return",
                            customClass: {
                                confirmButton: "btn btn-primary",
                                cancelButton: "btn btn-active-light"
                            }
                        }).then(function(result) {
                            if (result.value) {
                                form.reset(); // Reset form			
                                modal.hide();
                            } else if (result.dismiss === 'cancel') {
                                Swal.fire({
                                    text: "Your form has not been cancelled!.",
                                    icon: "error",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, got it!",
                                    customClass: {
                                        confirmButton: "btn btn-primary",
                                    }
                                });
                            }
                        });
                    });
                }

                return {
                    // Public functions
                    init: function() {
                        initAddUser();
                    }
                };
            }();

            // On document ready
            KTUtil.onDOMContentLoaded(function() {
                KTUsersAddUser.init();
            });
        });
    </script>
    <script>
        /*table.on('click', '.show-detail-direct', function (){	
    			
    			$('#kt_activitiesSP_toggle').trigger('click');			
    			var id    			=	$(this).data('row-id');
    			var url   			=	"{{ route('service_company.consultancy.show', ':id') }}";
    			url     			=	url.replace(':id', id);			
    			var baseurl    		=	"{{ asset('assets/images/Loading2.gif') }}"; 
    			$.ajax({
    							type  			: 'GET',
    							url   			: url,
    							 beforeSend		: function() {
    									  			$('#kt_activitiesSP_body').html('<img src="'+baseurl+'" height="50px" width="100%">');
    							},
    							success 		: function (response) 
    							{									
    											if (response.status == "success")
    											{
    												$('#kt_activitiesSP_body').html(response.view);
    											}	
    							}
    					});
    		
    		}); */
    </script>
    <script>
        $(document).on('click', '.show-detail-direct', function() {

            $('#kt_activitiesDirectClient_toggle').trigger('click');
            var id = $(this).data('row-id');
            var url = "{{ route('service_company.consultancy.dviewmore', ':id') }}";
            url = url.replace(':id', id);

            var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
            $.ajax({
                type: 'GET',
                url: url,
                beforeSend: function() {
                    $('#kt_activitiesDirectClient_body').html('<img src="' + baseurl +
                        '" height="35px" width="100%">');
                },
                success: function(response) {
                    $('#kt_activitiesDirectClient_body').html(response.view);
                }
            });
        });



        $(document).ready(function() {
          fetchstaffingpartner();
          $(document).on('click', '#paginationstaffingpartner .page-link', function(e) {
              e.preventDefault();

              const page = $(this).data('page');
              if (!page || $(this).hasClass('disabled')) return;

              // Disable the clicked pagination link
              $(this).addClass('disabled');

              // Fetch data
              fetchstaffingpartner(page, $('#search').val()).then(() => {
                // Re-enable the links after successful data fetch
                $('#paginationstaffingpartner .page-link').removeClass('disabled');
              }).catch(() => {
                // Re-enable the links even if there's an error
                $('#paginationstaffingpartner .page-link').removeClass('disabled');
              });
              
          });
			});
			

			function fetchstaffingpartner(page = 1, search = '') {
				$('.loadingMessagestaffingpartnerajax').css({ display: 'block' });
                // $('.page-loader').css('display', 'flex');
				$.ajax({
					url: `{!! route('service_company.consultancy.data_ajax') !!}?page=${page}`,
					type: 'GET',
					data: {
						search: $('#inputSearch').val(),
						cus_search: $('#kt_tagify_2').val(),
						country: $('#country').val(),
						sortingfilter: $('#sortingfilter').val()
					},
					success: function(response) {
						$('.loadingMessagestaffingpartnerajax').css({ display: 'none' });
                        // $('.page-loader').css('display', 'none');
                        $('#span_spartnerAll').html(response.total);
						$('#staffingpartnerajax').html(response.html);
						$('#paginationstaffingpartner').html(response.pagination);
					}
				});
			}




    </script>
@endpush
