<div
    class="tw-transition-all tw-duration-200 tw-bg-white tw-shadow-sm hover:tw-shadow-md tw-rounded-xl hover:tw--translate-y-0.5 tw-ring-1 tw-ring-gray-200">
    <div class="tw-p-4 sm:tw-p-5">
        <div class="tw-flex tw-items-center tw-gap-4">
            <div
                class="tw-inline-flex tw-items-center tw-justify-center tw-w-10 tw-h-10 tw-text-red-500 tw-bg-red-100 tw-rounded-full sm:tw-w-12 sm:tw-h-12 shrink-0">
                <svg aria-hidden="true" class="tw-w-6 tw-h-6"
                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" fill="none" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path
                        d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16l-3 -2l-2 2l-2 -2l-2 2l-2 -2l-3 2" />
                    <path d="M15 14v-2a2 2 0 0 0 -2 -2h-4l2 -2m0 4l-2 -2" />
                </svg>
            </div>

            <div class="tw-flex-1 tw-min-w-0">
                <p
                    class="tw-text-sm tw-font-medium tw-text-gray-500 tw-truncate tw-whitespace-nowrap">
                    {{ __('lang_v1.total_purchase_return') }}
                    <i class="fa fa-info-circle text-info hover-q no-print" aria-hidden="true" data-container="body"
                    data-toggle="popover" data-placement="auto bottom" id="total_prp"
                    data-value="{{ __('lang_v1.total_purchase_return') }}-{{ __('lang_v1.total_purchase_return_paid') }}"
                    data-content="" data-html="true" data-trigger="hover"></i>
                </p>
                <p
                    class="total_purchase_return tw-mt-0.5 tw-text-gray-900 tw-text-xl tw-truncate tw-font-semibold tw-tracking-tight tw-font-mono">
                </p>
            </div>
        </div>
    </div>
</div>