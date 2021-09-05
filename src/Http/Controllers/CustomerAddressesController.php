<?php

namespace FiftySq\Commerce\Http\Controllers;

use FiftySq\Commerce\Actions\CreateAddress;
use FiftySq\Commerce\Data\Models\Address;
use FiftySq\Commerce\Http\Requests\CreateAddressRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CustomerAddressesController extends Controller
{
    /**
     * @param  CreateAddressRequest  $request
     * @param  CreateAddress  $action
     * @return JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function store(CreateAddressRequest $request, CreateAddress $action)
    {
        $action($request->customer(), $request);

        return $request->wantsJson()
            ? new JsonResponse('', 200)
            : back()->with('status', 'address-created');
    }

    /**
     * @param  Request  $request
     * @param  Address  $address
     * @return JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, Address $address)
    {
        $address->delete();

        return $request->wantsJson()
            ? new JsonResponse('', 200)
            : back()->with('status', 'address-deleted');
    }
}
