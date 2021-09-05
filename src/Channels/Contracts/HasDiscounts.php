<?php

namespace FiftySq\Commerce\Channels\Contracts;

interface HasDiscounts
{
    public function getDiscounts($ruleId);

    public function getDiscount($ruleId, $discountId);

    public function createDiscount($ruleId, array $values);

    public function updateDiscount($ruleId, $discountId, array $values);

    public function deleteDiscount($ruleId, $discountId);

    public function getPriceRules();

    public function getPriceRule($ruleId);

    public function createPriceRule(array $values);

    public function updatePriceRule($ruleId, array $values);

    public function deletePriceRule($ruleId);
}
