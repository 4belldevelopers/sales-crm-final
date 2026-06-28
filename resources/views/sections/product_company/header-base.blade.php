<style>
[type="search"] { outline-offset: -2px;   -webkit-appearance: textfield; font-size: 1rem;  font-weight: 500;  line-height: 1.9; color: #181c32;  background-color: #fff;  background-clip: padding-box;  border: 1px solid #e4e6ef;   appearance: none;  border-radius: 0.6rem 0rem 0rem 0.6rem;  outline: none;  padding: 0.3em 0.9em;  transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out; }
header {  position: fixed;  top: 0; left: 0;  display: flex;  flex-direction: row;  flex-wrap: wrap;  width: 100%;  height: 80px;  background-color: #318ebb; box-shadow: 0 0px 8px rgba(212, 212, 212, 0.4); }
header .logo { display: flex; flex: 2;   justify-content: flex-start; align-items: center;  height: 100%;  color: #FFF;  font-size: 3rem;  padding-left: 2rem;  cursor: pointer; }
header nav {  display: flex;  flex: 7; }
header nav .search-bar {  display: flex;  flex: 1;  max-width: 800px;  margin: 0 auto; }
header nav .search-bar .search {   display: flex;  align-items: center;  flex: 1; }
header nav .search-bar .search .search__input { display: flex;  flex: 1;  padding: 1.2rem 1rem 1.2rem 4rem;  height: 40px;  font-size: 0.9rem; color: #4a4a4a;  border: none; border-radius: 0; position: relative;   right: 0; letter-spacing: 0.05rem;  background: url(https://cdn1.iconfinder.com/data/icons/hawcons/32/698956-icon-111-search-128.png) #ececec; background-repeat: no-repeat;  background-position: 1rem 50%; background-size: 2rem;  outline: 0;  transition: background 0.3s ease-out, opacity 0.5s ease-out;}
header nav .search-bar .search .search__input:focus {  background: url(https://cdn1.iconfinder.com/data/icons/hawcons/32/698956-icon-111-search-128.png) no-repeat 1rem 50% #FFFFFF;  background-size: 2rem; }
header nav .search-bar .search .search__btn {  background: #36474f;  height: 40px;  border: none; width: 120px;  color: #FFF;  font-size: 0.8rem; text-transform: uppercase;  letter-spacing: 0.1rem; cursor: pointer;  outline: 0; transition: all 0.4s ease-out; }
header nav .search-bar .search .search__btn:hover {  background: #263137;}
header nav .search-bar .search .search__icon { display: none; }
header .menu {  display: flex;  flex: 2;  justify-content: flex-end;  align-items: center;  padding: 2rem;  font-size: 2rem;  color: #FFF; height: 80px;  line-height: 80px;   cursor: pointer; }
.suggestion {  display: flex;  position: absolute;  top: 55px; border-top: 1px solid #F1F1F1; opacity: 0;  visibility: hidden;   width: 270px!important; }
.suggestion.active { opacity: 1;  visibility: visible;  transform-origin: center top;  transform: translateZ(0); -webkit-backface-visibility: hidden;  backface-visibility: hidden;  -webkit-animation: rotation-show 0.5s 0.3s; animation: rotation-show 0.5s 0.3s;  -webkit-animation-fill-mode: backwards; animation-fill-mode: backwards;}
.suggestion__content { background: #fff;  display: flex;  flex: 1;   box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15); padding: 0.5em 1em;   font-size: 0.8rem; }
.suggestion__content .suggestion__content-right-side,
.suggestion__content .suggestion__content-left-side { flex: 1;  padding: 1em;}
.suggestion__content .suggestion__content-right-side h5,
.suggestion__content .suggestion__content-left-side h5 {  margin: 0; padding: 0; font-size: 0.9rem; color: #757575; font-weight: 600;  margin-bottom: 5px; }
.suggestion__content .suggestion__content-right-side ul,
.suggestion__content .suggestion__content-left-side ul {  margin: 0;  padding: 0;}
.suggestion__content .suggestion__content-right-side ul li,
.suggestion__content .suggestion__content-left-side ul li {  list-style-type: none;  padding: 2px 0; }
.suggestion__content .suggestion__content-left-side ul li { list-style-type: none; padding: 2px 0; color: #328dbb;  cursor: pointer;  transition: color 0.4s ease-out, transform 0.4s ease; }
.suggestion__content .suggestion__content-left-side ul li:hover { color: #13587b; transform: translateX(3px); }
.suggestion__content .suggestion__content-right-side h5 {  text-align: center; }
.suggestion__content .suggestion__content-right-side ul { padding: 0;  max-width: 200px; margin: auto; }
.suggestion__content .suggestion__content-right-side ul li { border: 1px solid #f3f3f3; background: #FFF; text-align: center;  border-radius: 10px;  margin-bottom: 8px;  cursor: pointer;  transition: background 0.3s ease-out, border 0.4s ease-in-out; }
.suggestion__content .suggestion__content-right-side ul li:hover { border: 1px solid #607D8B; background: #f1f1f1; }
.hide-element { display: none !important; }
.mobile-search { margin: 0 1em; opacity: 1 !important; visibility: visible !important; width: 100%; }
.close-icon {  color: #909090 !important; position: absolute !important; right: 8% !important; font-size: 1rem; }
@-webkit-keyframes rotation-show {
  0% { transform: perspective(1000px) rotateX(-90deg);  }
  100% { transform: perspective(1000px) translateY(0);  } 
}
@keyframes rotation-show {
  0% { transform: perspective(1000px) rotateX(-90deg); }
  100% { transform: perspective(1000px) translateY(0); }  
}
@media only screen and (max-width: 600px) {
  .search__input { font-size: 16px !important; opacity: 0;  visibility: hidden; width: 0;  }
  .search__btn { display: none !important; }
  .search__icon {  display: flex !important;  color: #FFF;  align-items: center;  flex: 6; position: absolute;  right: 5rem; cursor: pointer;  height: 80px;  line-height: 80px;  }
  .message {  position: relative; top: 80px;  background: #f3f3f3;  padding: 0.5rem 1.5rem;  display: block !important; text-align: center; }     
}
</style>
<div id="kt_header" style="" class="header align-items-stretch">
<div class="container-fluid d-flex align-items-stretch justify-content-between">
<div class="d-flex align-items-center d-lg-none ms-n3 me-1" title="Show aside menu">
  <div class="btn btn-icon btn-active-light-primary w-30px h-30px w-md-40px h-md-40px" id="kt_aside_mobile_toggle">
    <span class="svg-icon svg-icon-2x mt-1">
    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
        <rect x="0" y="0" width="24" height="24" />
        <rect fill="#000000" x="4" y="5" width="16" height="3" rx="1.5" />
        <path d="M5.5,15 L18.5,15 C19.3284271,15 20,15.6715729 20,16.5 C20,17.3284271 19.3284271,18 18.5,18 L5.5,18 C4.67157288,18 4,17.3284271 4,16.5 C4,15.6715729 4.67157288,15 5.5,15 Z M5.5,10 L18.5,10 C19.3284271,10 20,10.6715729 20,11.5 C20,12.3284271 19.3284271,13 18.5,13 L5.5,13 C4.67157288,13 4,12.3284271 4,11.5 C4,10.6715729 4.67157288,10 5.5,10 Z" fill="#000000" opacity="0.3" />
      </g>
    </svg>
    </span>
  </div>
</div>

<div class="d-flex align-items-center flex-grow-1 flex-lg-grow-0"> <a href="../../demo1/dist/index.html" class="d-lg-none"> <img alt="Logo" src="assets/media/logos/logo-3.svg" class="h-30px" /> </a> </div>
<div class="d-flex align-items-stretch justify-content-between flex-lg-grow-1">
<div class="d-flex align-items-stretch" id="kt_header_nav">
<div class="header-menu align-items-stretch" data-kt-drawer="true" data-kt-drawer-name="header-menu" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'200px', '300px': '250px'}" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_header_menu_mobile_toggle" data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_body', lg: '#kt_header_nav'}">
<div class="search-bar d-flex align-items-center">
<form class="search">
  <input type="search" class="search__input" name="search" placeholder="Search" onload="equalWidth()" required>
  <button class="search__btn btn btn-sm btn-primary btn-active-light-primary" style="position: absolute;border-radius: 0rem 0.475rem 0.475rem 0rem;">Search</button>
  <i class="ion-ios-search search__icon"></i>
</form>
<div class="suggestion">
<div class="suggestion__content">
<div class="suggestion__content-left-side">
<h5>Popular Categories</h5>
<ul>
<li>Marketing Material</li>
<li>Just Sold</li>
<li>Small Business Card</li>
<li>Mailing</li>
<li>Just List</li>
<ul>
</div>
<div class="suggestion__content-right-side">
  <h5>Popular Keywords</h5>
  <ul>
    <li>Business Card</li>
    <li>Brochures</li>
    <li>Just List Just Sold</li>
    <li>Flyers</li>
  </ul>
</div>
</div>
</div>
</div>
<div class="d-flex align-items-stretch ms-1 ms-lg-3">
  <div id="kt_header_search" class="d-flex align-items-stretch" data-kt-search-keypress="true" data-kt-search-min-length="2" data-kt-search-enter="enter" data-kt-search-layout="menu" data-kt-menu-trigger="auto" data-kt-menu-overflow="false" data-kt-menu-permanent="true" data-kt-menu-placement="bottom-start" data-kt-menu-flip="bottom">
    <div class="d-flex align-items-center" data-kt-search-element="toggle" id="kt_header_search_toggle">
      <div class="btn btn-icon btn-active-light-primary w-30px h-30px w-md-40px h-md-40px">
        <span class="svg-icon svg-icon-1">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
          <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
            <rect x="0" y="0" width="24" height="24" />
            <path d="M14.2928932,16.7071068 C13.9023689,16.3165825 13.9023689,15.6834175 14.2928932,15.2928932 C14.6834175,14.9023689 15.3165825,14.9023689 15.7071068,15.2928932 L19.7071068,19.2928932 C20.0976311,19.6834175 20.0976311,20.3165825 19.7071068,20.7071068 C19.3165825,21.0976311 18.6834175,21.0976311 18.2928932,20.7071068 L14.2928932,16.7071068 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" />
            <path d="M11,16 C13.7614237,16 16,13.7614237 16,11 C16,8.23857625 13.7614237,6 11,6 C8.23857625,6 6,8.23857625 6,11 C6,13.7614237 8.23857625,16 11,16 Z M11,18 C7.13400675,18 4,14.8659932 4,11 C4,7.13400675 7.13400675,4 11,4 C14.8659932,4 18,7.13400675 18,11 C18,14.8659932 14.8659932,18 11,18 Z" fill="#000000" fill-rule="nonzero" />
          </g>
        </svg>
        </span>
      </div>
    </div>
   
  </div>
</div>


</div>
</div>
<div class="d-flex align-items-stretch flex-shrink-0">
  <div class="d-flex align-items-stretch flex-shrink-0">
    <div class="d-flex align-items-stretch ms-1 ms-lg-3 d-none">
      <div id="kt_header_search" class="d-flex align-items-stretch" data-kt-search-keypress="true" data-kt-search-min-length="2" data-kt-search-enter="enter" data-kt-search-layout="menu" data-kt-menu-trigger="auto" data-kt-menu-overflow="false" data-kt-menu-permanent="true" data-kt-menu-placement="bottom-end" data-kt-menu-flip="bottom">
        <div class="d-flex align-items-center" data-kt-search-element="toggle" id="kt_header_search_toggle">
          <div class="btn btn-icon btn-active-light-primary w-30px h-30px w-md-40px h-md-40px">
            <span class="svg-icon svg-icon-1">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                <rect x="0" y="0" width="24" height="24" />
                <path d="M14.2928932,16.7071068 C13.9023689,16.3165825 13.9023689,15.6834175 14.2928932,15.2928932 C14.6834175,14.9023689 15.3165825,14.9023689 15.7071068,15.2928932 L19.7071068,19.2928932 C20.0976311,19.6834175 20.0976311,20.3165825 19.7071068,20.7071068 C19.3165825,21.0976311 18.6834175,21.0976311 18.2928932,20.7071068 L14.2928932,16.7071068 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" />
                <path d="M11,16 C13.7614237,16 16,13.7614237 16,11 C16,8.23857625 13.7614237,6 11,6 C8.23857625,6 6,8.23857625 6,11 C6,13.7614237 8.23857625,16 11,16 Z M11,18 C7.13400675,18 4,14.8659932 4,11 C4,7.13400675 7.13400675,4 11,4 C14.8659932,4 18,7.13400675 18,11 C18,14.8659932 14.8659932,18 11,18 Z" fill="#000000" fill-rule="nonzero" />
              </g>
            </svg>
            </span>
          </div>
        </div>
        
      </div>
    </div>

  
    <div class="d-flex align-items-center ms-1 ms-lg-3" id="kt_header_user_menu_toggle">
      <div class="cursor-pointer symbol symbol-30px symbol-md-40px" data-kt-menu-trigger="click" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end" data-kt-menu-flip="bottom">
@php 

$background_image		=	url('public/user-uploads/team_profile_img/avatar.png'); 
if(!empty(@$user->image) && strlen(@$user->image)>10){
	$background_image	=  url('public/user-uploads/team_profile_img/'.@$user->image);     	
}
@endphp 
        <img src="{!! @$background_image !!}" alt="{{ $user->name  }}" /> </div>
      <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-primary fw-bold py-4 fs-6 w-275px" data-kt-menu="true">
        <div class="menu-item px-3">
          <div class="menu-content d-flex align-items-center px-3">
            <div class="symbol symbol-50px me-5"> <img alt="Logo" src="{!! @$background_image !!}" /> </div>
            <div class="d-flex flex-column">
              <div class="fw-bolder d-flex align-items-center fs-5">{{ $user->name  }} </div>
              <a href="#" class="fw-bold text-muted text-hover-primary fs-7">{{ $user->email  }}</a> </div>
          </div>
        </div>
        <div class="separator my-2"></div>
        <div class="menu-item px-5"> <a href="{{ route('product_company.profile.index') }}" class="menu-link px-5">My Profile</a> </div>
        <div class="separator my-2 d-none"></div>
        <div class="menu-item px-5"> <a class="menu-link px-5 bg-light-danger text-danger bg-hover-danger text-hover-light mt-2" href="{{ route('logout') }}" title="Sign Out" onClick="event.preventDefault(); document.getElementById('logout-form').submit();">
          <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            {{ csrf_field() }}
          </form>
          Sign Out </a> </div>
      </div>
    </div>
    <div class="d-flex align-items-center d-lg-none ms-2 me-n3" title="Show header menu">
      <div class="btn btn-icon btn-active-light-primary w-30px h-30px w-md-40px h-md-40px" id="kt_header_menu_mobile_toggle">
        <span class="svg-icon svg-icon-1">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
          <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
            <rect x="0" y="0" width="24" height="24" />
            <path fill-rule="evenodd" clip-rule="evenodd" d="M22 11.5C22 12.3284 21.3284 13 20.5 13H3.5C2.6716 13 2 12.3284 2 11.5C2 10.6716 2.6716 10 3.5 10H20.5C21.3284 10 22 10.6716 22 11.5Z" fill="black" />
            <path opacity="0.5" fill-rule="evenodd" clip-rule="evenodd" d="M14.5 20C15.3284 20 16 19.3284 16 18.5C16 17.6716 15.3284 17 14.5 17H3.5C2.6716 17 2 17.6716 2 18.5C2 19.3284 2.6716 20 3.5 20H14.5ZM8.5 6C9.3284 6 10 5.32843 10 4.5C10 3.67157 9.3284 3 8.5 3H3.5C2.6716 3 2 3.67157 2 4.5C2 5.32843 2.6716 6 3.5 6H8.5Z" fill="black" />
          </g>
        </svg>
        </span>
      </div>
    </div>
  </div>
</div>
</div>
<!--end::Wrapper-->
</div>
<!--end::Container-->
</div>
<script>
    var input 			=	document.querySelector('.search__input');
    var inputWidth 		=	input.offsetWidth;
    var suggestion 		=	document.querySelector('.suggestion');
    var mobileSearch 	=	document.querySelector('.search__icon');
    var menu 			=	document.querySelector('.menu');
    var logo 			=	document.querySelector('.logo');
    var logoIcon 		=	logo.querySelector('.logo__icon');
    function equalWidth(){
      var input 				=	document.querySelector('.search__input');
      var inputWidth 			=	input.offsetWidth;
      suggestion.style.width 	=	inputWidth + "px" ;
    }
    function showSuggestion(){
      suggestion.classList.add('active');
    }
    function hideSuggestion(){
      suggestion.classList.remove('active');
    }
    document.addEventListener('DOMContentLoaded', function()
    {
      equalWidth();
      input.addEventListener('focus', showSuggestion);
    })
    document.addEventListener('click', function(e){
      var target = e.target.className;
      e.stopPropagation();
      if (target != "search__input" && target != "suggestion__content-left-side" && target != "suggestion__content-right-side" && target != "suggestion__content" ) {
        hideSuggestion();
        console.log(target);
      }
    });
    var addEvent = function(object, type, callback){
        if (object == null || typeof(object) == 'undefined') return;
        if (object.addEventListener) {
            object.addEventListener(type, callback, false);
        } else if (object.attachEvent) {
            object.attachEvent("on" + type, callback);
        } else {
            object["on"+type] = callback;
        }
    };
    addEvent(window, 'resize', function(event){
      suggestion.style.width = '';
      equalWidth();
    });
    function openMobileSearch(){
      logo.classList.toggle('hide-element');
      menu.classList.toggle('hide-element');
      input.classList.toggle('mobile-search');
      mobileSearch.classList.toggle('ion-ios-search');
      mobileSearch.classList.toggle('ion-close-round');
      mobileSearch.classList.toggle('close-icon');
    }
    mobileSearch.addEventListener('click', function(){
      openMobileSearch();
    })
</script>
