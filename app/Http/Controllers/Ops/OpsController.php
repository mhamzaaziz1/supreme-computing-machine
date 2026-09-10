<?php

namespace App\Http\Controllers\Ops;

use App\Http\Controllers\Controller;
use App\User;
use App\Utils\Util;
use Illuminate\Support\Facades\DB;

/**
 * Shared plumbing for the JSON endpoints behind the overlays.
 *
 * Every outlet-scoped endpoint goes through outlet(), which ports the
 * customer.view / customer.view_own rule from ContactController@show, so a
 * seller restricted to their own customers cannot open someone else's
 * outlet by editing an id in the URL.
 */
abstract class OpsController extends Controller
{
    protected function businessId(): int
    {
        return (int) request()->session()->get('user.business_id');
    }

    protected function isAdmin(): bool
    {
        return app(Util::class)->is_admin(auth()->user(), $this->businessId());
    }

    protected function canViewCustomers(): bool
    {
        return $this->isAdmin() || auth()->user()->can('customer.view') || auth()->user()->can('customer.view_own');
    }

    /**
     * The contact, if this user may see it.
     */
    protected function outlet(int $id, array $columns = ['*']): object
    {
        if (! $this->canViewCustomers()) {
            abort(403, 'You do not have permission to view customers.');
        }

        $contact = DB::table('contacts')
            ->where('business_id', $this->businessId())
            ->where('id', $id)
            ->whereIn('type', ['customer', 'both'])
            ->first($columns === ['*'] ? ['*'] : array_unique([...$columns, 'id', 'created_by']));

        if (! $contact) {
            abort(404, 'That customer does not exist.');
        }

        if (! $this->isAdmin() && ! auth()->user()->can('customer.view')) {
            $user = auth()->user();
            $allowed = (int) $contact->created_by === (int) $user->id
                || (User::isSelectedContacts($user->id) && $user->contactAccess->pluck('id')->contains($contact->id));

            if (! $allowed) {
                abort(403, 'This customer is not assigned to you.');
            }
        }

        return $contact;
    }

    /**
     * Name shown for an outlet: the business name when there is one, since
     * that is what is painted over the forecourt.
     */
    protected static function outletName(object $c): string
    {
        $business = trim((string) ($c->supplier_business_name ?? ''));
        $person = trim((string) ($c->name ?? ''));

        return $business !== '' ? $business : ($person !== '' ? $person : 'Unnamed customer');
    }
}
