<?php

namespace Mageplaza\Osc\Model\Geoip;

interface ProviderInterface
{
	/**
	 * @param ipAddress
	 *            IPv4 or IPv6 address to lookup.
	 * @return Country model for the requested IP address.
	 */
	public function country($ipAddress);

	/**
	 * @param ipAddress
	 *            IPv4 or IPv6 address to lookup.
	 * @return City model for the requested IP address.
	 */
	public function city($ipAddress);
}
