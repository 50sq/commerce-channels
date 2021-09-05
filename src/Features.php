<?php

namespace FiftySq\Commerce;

class Features
{
    /**
     * Determine if the given feature is enabled.
     *
     * @param  string  $feature
     * @return bool
     */
    public static function enabled(string $feature)
    {
        return in_array($feature, config('commerce.features', []));
    }

    /**
     * Determine if the feature is enabled and has a given option enabled.
     *
     * @param  string  $feature
     * @param  string  $option
     * @return bool
     */
    public static function optionEnabled(string $feature, string $option)
    {
        return static::enabled($feature) &&
            config("commerce-options.{$feature}.{$option}") === true;
    }

    /**
     * Enable the discounts feature.
     *
     * @return string
     */
    public static function discounts()
    {
        return 'discounts';
    }

    /**
     * Enable the guest customer feature.
     *
     * @return string
     */
    public static function guests()
    {
        return 'guests';
    }

    /**
     * Enable inventory tracking.
     *
     * @return string
     */
    public static function inventory()
    {
        return 'inventory';
    }

    /**
     * Enable shipping feature.
     *
     * @return string
     */
    public static function shipping()
    {
        return 'shipping';
    }
}
