<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Model\Spi;

use Magento\Framework\Model\AbstractModel;

/**
 * Interface ResourceInterface.
 */
interface QuoteCustomerDataResourceInterface
{
    /**
     * Save object data.
     *
     * @param AbstractModel $object
     *
     * @return $this
     */
    public function save(AbstractModel $object);

    /**
     * Load an object.
     *
     * @param mixed $value
     * @param AbstractModel $object
     * @param string|null $field field to load by (defaults to model id)
     *
     * @return mixed
     */
    public function load(AbstractModel $object, $value, $field = null);

    /**
     * Delete the object.
     *
     * @param AbstractModel $object
     *
     * @return mixed
     */
    public function delete(AbstractModel $object);
}
