<?php namespace Winter\Storm\Validation;

use Illuminate\Contracts\Validation\Factory as FactoryContract;
use Illuminate\Validation\Factory as BaseFactory;

/**
 * Winter CMS wrapper for the Laravel Validation factory.
 *
 * Ensures that the default resolver is the Winter\Storm\Validation\Validator.
 */
class Factory extends BaseFactory implements FactoryContract
{
    /**
     * Resolve a new Validator instance.
     *
     * @param  array  $data
     * @param  array  $rules
     * @param  array  $messages
     * @param  array  $customAttributes
     * @return \Winter\Storm\Validation\Validator
     */
    protected function resolve(array $data, array $rules, array $messages, array $customAttributes)
    {
        if (is_null($this->resolver)) {
            return new Validator($this->translator, $data, $rules, $messages, $customAttributes);
        }

        return call_user_func($this->resolver, $this->translator, $data, $rules, $messages, $customAttributes);
    }
}
