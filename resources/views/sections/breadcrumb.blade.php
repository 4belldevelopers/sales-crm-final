<style type="text/css">
    /*Countdown Dropdown*/
.dropdown-toggle.btn:after, .dropdown-toggle.nav-link:after{content:""!important;margin:0;}
.dropbtn{background-color:#f2f2f2;border:none;}
.dropbtn-border{border: 1px solid lightgray;height: fit-content;border-radius: 2px;}
.dropdown{position:relative;display:inline-block;}
.dropdown-content{display:none;position:absolute;background-color:#FFFFFF;min-width:auto;z-index: 1;    
    border: 1px solid #828282;}
.dropdown-content a{color:black;padding:.5rem .3rem;text-decoration:none;display:block;text-align:left;}
.dropdown-content a:hover{background-color:#f9f8f8;display: block;}
.dropdown:hover .dropdown-content{display:block;}
.dropdown-content1{min-width:auto!important;}
ul li{list-style: none;}
.navi{padding-left: 0px;}
.dropdown-content .custom-width{width:40px!important;}
/*Countdown Dropdown End*/
</style>

<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                    @yield('create-button')
                <div class="row">                    
                    <div class="col-md-6 text-left"></div>
                    <div class="col-md-6 text-right"></div>    
                </div>
            </div>
        </div>
    </div>
</section>