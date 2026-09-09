<link rel="stylesheet" href="{{ asset('css/vendor.css?v='.$asset_v) }}">

@if( in_array(session()->get('user.language', config('app.locale')), config('constants.langs_rtl')) )
	<link rel="stylesheet" href="{{ asset('css/rtl.css?v='.$asset_v) }}">
@endif

@yield('css')

<!-- app css -->
<link href="{{ asset('css/tailwind/app.css?v=' . time()) }}" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/app.css?v=' . time()) }}">

@if(isset($pos_layout) && $pos_layout)
	<style type="text/css">
		.content{
			padding-bottom: 0px !important;
		}
	</style>
@endif
<style type="text/css">
	/*
	* Pattern lock css
	* Pattern direction
	* http://ignitersworld.com/lab/patternLock.html
	*/
	.patt-wrap {
	  z-index: 10;
	}
	.patt-circ.hovered {
	  background-color: #cde2f2;
	  border: none;
	}
	.patt-circ.hovered .patt-dots {
	  display: none;
	}
	.patt-circ.dir {
	  background-image: url("{{asset('/img/pattern-directionicon-arrow.png')}}");
	  background-position: center;
	  background-repeat: no-repeat;
	}
	.patt-circ.e {
	  -webkit-transform: rotate(0);
	  transform: rotate(0);
	}
	.patt-circ.s-e {
	  -webkit-transform: rotate(45deg);
	  transform: rotate(45deg);
	}
	.patt-circ.s {
	  -webkit-transform: rotate(90deg);
	  transform: rotate(90deg);
	}
	.patt-circ.s-w {
	  -webkit-transform: rotate(135deg);
	  transform: rotate(135deg);
	}
	.patt-circ.w {
	  -webkit-transform: rotate(180deg);
	  transform: rotate(180deg);
	}
	.patt-circ.n-w {
	  -webkit-transform: rotate(225deg);
	   transform: rotate(225deg);
	}
	.patt-circ.n {
	  -webkit-transform: rotate(270deg);
	  transform: rotate(270deg);
	}
	.patt-circ.n-e {
	  -webkit-transform: rotate(315deg);
	  transform: rotate(315deg);
	}
</style>

<style type="text/css">
	/* Global Dropdown List Styles (Yellow, Dark Grey & Black palette) */
	select {
		color-scheme: dark;
	}
	select option,
	select optgroup {
		background-color: #0b1220 !important;
		color: #e9edf6 !important;
		padding: 8px 12px;
	}
	select option:hover,
	select option:focus,
	select option:active,
	select option:checked {
		background-color: #f2b52a !important;
		color: #070b15 !important;
		font-weight: 600;
	}
	.select2-container--default .select2-selection--single,
	.select2-container--default .select2-selection--multiple {
		background-color: #0b1220 !important;
		border: 1px solid rgba(255, 255, 255, 0.12) !important;
		color: #e9edf6 !important;
		border-radius: 8px !important;
	}
	.select2-container--default .select2-selection--single .select2-selection__rendered {
		color: #e9edf6 !important;
	}
	.select2-dropdown {
		background-color: #101a30 !important;
		border: 1px solid #f2b52a !important;
		border-radius: 8px !important;
		box-shadow: 0 12px 36px rgba(0, 0, 0, 0.6) !important;
		overflow: hidden !important;
	}
	.select2-search--dropdown {
		background-color: #101a30 !important;
		padding: 8px !important;
	}
	.select2-search--dropdown .select2-search__field {
		background-color: #0b1220 !important;
		border: 1px solid rgba(242, 181, 42, 0.4) !important;
		color: #e9edf6 !important;
		border-radius: 6px !important;
		padding: 6px 10px !important;
	}
	.select2-results__option {
		background-color: #101a30 !important;
		color: #e9edf6 !important;
		padding: 8px 14px !important;
	}
	.select2-container--default .select2-results__option--highlighted[aria-selected],
	.select2-container--default .select2-results__option--highlighted[aria-selected="true"],
	.select2-container--default .select2-results__option--highlighted[aria-selected="false"],
	.select2-results__option:hover,
	.select2-results__option:focus {
		background-color: #f2b52a !important;
		color: #070b15 !important;
		font-weight: 600 !important;
	}
	.select2-container--default .select2-results__option[aria-selected="true"] {
		background-color: #14203a !important;
		color: #f2b52a !important;
		font-weight: 600 !important;
	}
	.dropdown-menu {
		background-color: #101a30 !important;
		border: 1px solid rgba(242, 181, 42, 0.3) !important;
		border-radius: 8px !important;
		box-shadow: 0 12px 36px rgba(0, 0, 0, 0.6) !important;
		padding: 6px 0 !important;
	}
	.dropdown-item,
	.dropdown-menu > li > a,
	.dropdown-menu > a {
		color: #e9edf6 !important;
		padding: 8px 16px !important;
		background-color: transparent !important;
		transition: background-color 0.15s ease, color 0.15s ease !important;
	}
	.dropdown-item:hover,
	.dropdown-item:focus,
	.dropdown-item:active,
	.dropdown-item.active,
	.dropdown-menu > li > a:hover,
	.dropdown-menu > li > a:focus,
	.dropdown-menu > .active > a,
	.dropdown-menu > .active > a:hover,
	.dropdown-menu > .active > a:focus {
		background-color: #f2b52a !important;
		color: #070b15 !important;
		font-weight: 600 !important;
	}
	.btn-primary,
	.bg-light-blue,
	.label-primary {
		background-color: #f2b52a !important;
		border-color: #e0a320 !important;
		color: #070b15 !important;
		font-weight: 600 !important;
	}
	.btn-primary:hover,
	.btn-primary:focus,
	.btn-primary:active {
		background-color: #ffd24a !important;
		border-color: #f2b52a !important;
		color: #070b15 !important;
	}
	a:hover, a:focus {
		color: #f2b52a;
	}
	.ui-menu-item:hover,
	.ui-menu-item-wrapper:hover,
	.ui-menu-item .ui-state-active,
	.ui-menu-item-wrapper.ui-state-active,
	.ui-state-highlight,
	.ui-state-hover,
	.ui-state-focus,
	.tt-suggestion:hover,
	.tt-cursor {
		background-color: #f2b52a !important;
		color: #070b15 !important;
		font-weight: 600 !important;
	}
	.form-control:focus,
	input:focus,
	select:focus,
	textarea:focus {
		border-color: rgba(242, 181, 42, 0.6) !important;
		box-shadow: 0 0 0 3px rgba(242, 181, 42, 0.25) !important;
	}
	.pagination > li > a:hover,
	.pagination > li > span:hover,
	.pagination > li.active > a,
	.pagination > li.active > span,
	.dataTables_wrapper .dataTables_paginate .paginate_button:hover,
	.dataTables_wrapper .dataTables_paginate .paginate_button.current,
	.dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
		background: #f2b52a !important;
		background-color: #f2b52a !important;
		border-color: #f2b52a !important;
		color: #070b15 !important;
		font-weight: 600 !important;
	}
	.nav-pills > li.active > a,
	.nav-pills > li.active > a:hover,
	.nav-pills > li > a:hover,
	.nav-tabs > li.active > a,
	.nav-tabs > li > a:hover {
		background-color: #f2b52a !important;
		color: #070b15 !important;
		border-color: #f2b52a !important;
	}
</style>

@if(!empty($__system_settings['additional_css']))
    {!! $__system_settings['additional_css'] !!}
@endif

