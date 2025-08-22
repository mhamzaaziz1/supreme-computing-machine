<!-- Profile Picture -->
@if(!empty($contact->profile_picture))
    <div class="profile-image text-center mb-10">
        <div style="border: 2px solid #3c8dbc; border-radius: 10px; padding: 10px; display: inline-block;">
            <h4 class="text-center" style="margin-top: 0; color: #3c8dbc;">@lang('lang_v1.profile_picture')</h4>
            <img src="{{ asset($contact->profile_picture) }}" alt="Profile Picture" class="img-circle img-responsive" style="max-width: 150px; margin: 0 auto; border: 3px solid #d2d6de; box-shadow: 0 1px 3px rgba(0,0,0,.15);">
        </div>
    </div>
@endif

<!-- <strong>{{ $contact->name }}</strong><br><br> -->
<h3 class="profile-username">
    <i class="fas fa-user-tie"></i>
    {{ $contact->full_name_with_business }}
    <small>
        @if($contact->type == 'both')
            {{__('role.customer')}} & {{__('role.supplier')}}
        @elseif(($contact->type != 'lead'))
            {{__('role.'.$contact->type)}}
        @endif
    </small>
</h3><br>
<strong><i class="fa fa-map-marker margin-r-5"></i> @lang('business.address')</strong>
<p class="text-muted">
    {!! $contact->contact_address !!}
</p>
@if($contact->supplier_business_name)
    <strong><i class="fa fa-briefcase margin-r-5"></i> 
    @lang('business.business_name')</strong>
    <p class="text-muted">
        {{ $contact->supplier_business_name }}
    </p>

    @if(!empty($contact->business_picture))
        <div class="business-image mt-10 mb-10 text-center">
            <div style="border: 2px solid #00a65a; border-radius: 10px; padding: 10px; display: inline-block;">
                <h4 class="text-center" style="margin-top: 0; color: #00a65a;">@lang('lang_v1.business_picture')</h4>
                <img src="{{ asset($contact->business_picture) }}" alt="Business Picture" class="img-rounded img-responsive" style="max-width: 200px; border: 3px solid #d2d6de; box-shadow: 0 1px 3px rgba(0,0,0,.15);">
            </div>
        </div>
    @endif
@endif

<strong><i class="fa fa-mobile margin-r-5"></i> @lang('contact.mobile')</strong>
<p class="text-muted">
    {{ $contact->mobile }}
</p>
@if($contact->landline)
    <strong><i class="fa fa-phone margin-r-5"></i> @lang('contact.landline')</strong>
    <p class="text-muted">
        {{ $contact->landline }}
    </p>
@endif
@if($contact->alternate_number)
    <strong><i class="fa fa-phone margin-r-5"></i> @lang('contact.alternate_contact_number')</strong>
    <p class="text-muted">
        {{ $contact->alternate_number }}
    </p>
@endif
@if($contact->dob)
    <strong><i class="fa fa-calendar margin-r-5"></i> @lang('lang_v1.dob')</strong>
    <p class="text-muted">
        {{ @format_date($contact->dob) }}
    </p>
@endif
