<?php
namespace Mageplaza\Osc\Model\Geoip\Database;
use Mageplaza\Osc\Model\Geoip\ProviderInterface;
use Mageplaza\Osc\Model\Geoip\Maxmind\Db\Reader as DbReader;

/**
 * Instances of this class provide a reader for the GeoIP2 database format.
 * IP addresses can be looked up using the database specific methods.
 *
 * ## Usage ##
 *
 * The basic API for this class is the same for every database. First, you
 * create a reader object, specifying a file name. You then call the method
 * corresponding to the specific database, passing it the IP address you want
 * to look up.
 *
 * If the request succeeds, the method call will return a model class for
 * the method you called. This model in turn contains multiple record classes,
 * each of which represents part of the data returned by the database. If
 * the database does not contain the requested information, the attributes
 * on the record class will have a `null` value.
 *
 * If the address is not in the database, an
 * {@link \GeoIp2\Exception\AddressNotFoundException} exception will be
 * thrown. If an invalid IP address is passed to one of the methods, a
 * SPL {@link \InvalidArgumentException} will be thrown. If the database is
 * corrupt or invalid, a {@link \MaxMind\Db\Reader\InvalidDatabaseException}
 * will be thrown.
 *
 */
class Reader implements ProviderInterface
{
    /**
     * @type \Mageplaza\Osc\Model\Geoip\Maxmind\Db\Reader
     */
    private $_dbReader;

    /**
     * @type array
     */
    private $locales;


    /**
     * @param \Mageplaza\Osc\Model\Geoip\Maxmind\Db\Reader $dbreader
     */
    public function __construct(
        DbReader $dbreader
    ) {
        $this->_dbReader = $dbreader;
        $this->locales = array('en');
    }

    /**
     * This method returns a GeoIP2 City model.
     * @param string $ipAddress IPv4 or IPv6 address as a string.
     * @return array
     */
    public function city($ipAddress)
    {
        return $this->modelFor('City', 'City', $ipAddress);
    }

    /**
     * This method returns a GeoIP2 Country model.
     * @param string $ipAddress IPv4 or IPv6 address as a string.
     * @return array
     */
    public function country($ipAddress)
    {
        return $this->modelFor('Country', 'Country', $ipAddress);
    }


    /**
     * @param $class
     * @param $type
     * @param $ipAddress
     * @return array
     * @throws \Mageplaza\Osc\Model\Geoip\Database\AddressNotFoundException
     * @throws \Mageplaza\Osc\Model\Geoip\Database\InvalidDatabaseException
     */
    private function modelFor($class, $type, $ipAddress)
    {
        $record = $this->getRecord($class, $type, $ipAddress);

        $record['traits']['ip_address'] = $ipAddress;
        $this->close();
        return $record;
    }

    /**
     * @param $class
     * @param $type
     * @param $ipAddress
     * @return array
     * @throws \Mageplaza\Osc\Model\Geoip\Database\AddressNotFoundException
     * @throws \Mageplaza\Osc\Model\Geoip\Database\InvalidDatabaseException
     */
    private function getRecord($class, $type, $ipAddress)
    {
        if (strpos($this->metadata()->databaseType, $type) === false) {
            $method = lcfirst($class);
            throw new \BadMethodCallException(
                "The $method method cannot be used to open a "
                . $this->metadata()->databaseType . " database"
            );
        }
        $record = $this->_dbReader->get($ipAddress);
        if ($record === null) {
            throw new AddressNotFoundException(
                "The address $ipAddress is not in the database."
            );
        }
        if (!is_array($record)) {
            // This can happen on corrupt databases. Generally,
            // MaxMind\Db\Reader will throw a
            // MaxMind\Db\Reader\InvalidDatabaseException, but occasionally
            // the lookup may result in a record that looks valid but is not
            // an array. This mostly happens when the user is ignoring all
            // exceptions and the more frequent InvalidDatabaseException
            // exceptions go unnoticed.
            throw new InvalidDatabaseException(
                "Expected an array when looking up $ipAddress but received: "
                . gettype($record)
            );
        }
        return $record;
    }

    /**
     * @throws \InvalidArgumentException if arguments are passed to the method.
     * @throws \BadMethodCallException if the database has been closed.
     * @return Metadata object for the database.
     */
    public function metadata()
    {
        return $this->_dbReader->metadata();
    }

    /**
     * Closes the GeoIP2 database and returns the resources to the system.
     */
    public function close()
    {
        $this->_dbReader->close();
    }
}
