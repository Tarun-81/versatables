<?php

namespace Lof\RequestForQuote\Plugin;


class Validate
{
    public function around_processActionData(\Lof\RequestForQuote\Controller\Adminhtml\Quote\Create $subject, callable $proceed, $action = null)
    {
        return $proceed($action);
    }
}