<?php

namespace FiftySq\Commerce\Actions;

use FiftySq\Commerce\Channels\ChannelManager;
use FiftySq\Commerce\Data\Models\PendingOrder;

class InitiateRemoteCheckout
{
    protected ChannelManager $manager;

    /**
     * InitiateRemoteCheckout constructor.
     *
     * @param  ChannelManager  $manager
     */
    public function __construct(ChannelManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param  PendingOrder  $pending_order
     * @param $channel
     * @return mixed
     */
    public function __invoke(PendingOrder $pending_order, $channel)
    {
        return $this->manager->driver($channel)->sendToCheckout($pending_order);
    }
}
