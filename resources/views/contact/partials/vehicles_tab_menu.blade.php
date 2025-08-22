<li class="
    @if(!empty($view_type) &&  $view_type == 'vehicles')
        active
    @else
        ''
    @endif">
    <a href="#vehicles_tab" data-toggle="tab" aria-expanded="true"><i class="fas fa-car" aria-hidden="true"></i> @lang('lang_v1.vehicles')</a>
</li>