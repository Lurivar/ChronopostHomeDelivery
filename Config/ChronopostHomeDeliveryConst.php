<?php

namespace ChronopostHomeDelivery\Config;


use ChronopostHomeDelivery\ChronopostHomeDelivery;
use Symfony\Component\Filesystem\Filesystem;
use Thelia\Model\ConfigQuery;

class ChronopostHomeDeliveryConst
{
    /** Delivery types Name => Code */
    const CHRONOPOST_HOME_DELIVERY_DELIVERY_CODES = [
        "Chrono13"      => "1",
        "Chrono18"      => "16",
        "ChronoExpress" => "17",
        "ChronoClassic" => "44",
        "Fresh13"       => "2R"
    ];
    /** @TODO Add other delivery types */

    /** Chronopost shipper identifiers */
    const CHRONOPOST_HOME_DELIVERY_CODE_CLIENT                    = "chronopost_home_delivery_code";
    const CHRONOPOST_HOME_DELIVERY_PASSWORD                       = "chronopost_home_delivery_password";

    /** Chronopost label type (PDF,ZPL | With or without proof of deposit */
    const CHRONOPOST_HOME_DELIVERY_LABEL_TYPE                     = "chronopost_home_delivery_label_type";

    /** Directory where we save the label */
    const CHRONOPOST_HOME_DELIVERY_LABEL_DIR                      = "chronopost_home_delivery_label_dir";

    /** ID of the treatment status in Thelia */
    const CHRONOPOST_HOME_DELIVERY_TREATMENT_STATUS               = "chronopost_home_delivery_treatment_status";

    /** Send as customer status. */
    const CHRONOPOST_HOME_DELIVERY_PRINT_AS_CUSTOMER_STATUS       = "chronopost_home_delivery_send_as_customer_status";

    /** Days before fresh products expiration after processing */
    const CHRONOPOST_HOME_DELIVERY_EXPIRATION_DATE                = "chronopost_home_delivery_expiration_date";

    /** WSDL for the Chronopost Shipping Service */
    const CHRONOPOST_HOME_DELIVERY_SHIPPING_SERVICE_WSDL              = "https://ws.chronopost.fr/shipping-cxf/ShippingServiceWS?wsdl";
    //const CHRONOPOST_HOME_DELIVERY_RELAY_SEARCH_SERVICE_WSDL          = "https://ws.chronopost.fr/recherchebt-ws-cxf/PointRelaisServiceWS?wsdl";
    const CHRONOPOST_HOME_DELIVERY_COORDINATES_SERVICE_WSDL           = "https://ws.chronopost.fr/rdv-cxf/services/CreneauServiceWS?wsdl";
    /** @TODO Add other WSDL config key */

    /** @Unused */
    const CHRONOPOST_HOME_DELIVERY_TRACKING_URL                   = "https://ws.chronopost.fr/tracking-cxf/TrackingServiceWS/trackSkybillV2";

    /** Shipper informations */
    const CHRONOPOST_HOME_DELIVERY_SHIPPER_NAME1          = "chronopost_home_delivery_shipper_name1";
    const CHRONOPOST_HOME_DELIVERY_SHIPPER_NAME2          = "chronopost_home_delivery_shipper_name2";
    const CHRONOPOST_HOME_DELIVERY_SHIPPER_ADDRESS1       = "chronopost_home_delivery_shipper_address1";
    const CHRONOPOST_HOME_DELIVERY_SHIPPER_ADDRESS2       = "chronopost_home_delivery_shipper_address2";
    const CHRONOPOST_HOME_DELIVERY_SHIPPER_COUNTRY        = "chronopost_home_delivery_shipper_country";
    const CHRONOPOST_HOME_DELIVERY_SHIPPER_CITY           = "chronopost_home_delivery_shipper_city";
    const CHRONOPOST_HOME_DELIVERY_SHIPPER_ZIP            = "chronopost_home_delivery_shipper_zipcode";
    const CHRONOPOST_HOME_DELIVERY_SHIPPER_CIVILITY       = "chronopost_home_delivery_shipper_civ";
    const CHRONOPOST_HOME_DELIVERY_SHIPPER_CONTACT_NAME   = "chronopost_home_delivery_shipper_contact_name";
    const CHRONOPOST_HOME_DELIVERY_SHIPPER_PHONE          = "chronopost_home_delivery_shipper_phone";
    const CHRONOPOST_HOME_DELIVERY_SHIPPER_MOBILE_PHONE   = "chronopost_home_delivery_shipper_mobile_phone";
    const CHRONOPOST_HOME_DELIVERY_SHIPPER_MAIL           = "chronopost_home_delivery_shipper_mail";

    /** @Unused */
    public function getTrackingURL()
    {
        $URL = self::CHRONOPOST_HOME_DELIVERY_TRACKING_URL;
        $URL .= "language=" . "fr_FR"; //todo Make locale a variable
        $URL .= "&skybillNumber=" . "XXX"; //todo Use real skybill Number -> getTrackingURL(variable)

        return $URL;
    }

    /** Local static config value, used to limit the number of calls to the DB  */
    protected static $config = null;

    /**
     * Set the local static config value
     */
    public static function setConfig()
    {
        $config = [
            /** Chronopost basic informations */
            self::CHRONOPOST_HOME_DELIVERY_CODE_CLIENT                => ChronopostHomeDelivery::getConfigValue(self::CHRONOPOST_HOME_DELIVERY_CODE_CLIENT),
            self::CHRONOPOST_HOME_DELIVERY_LABEL_DIR                  => ChronopostHomeDelivery::getConfigValue(self::CHRONOPOST_HOME_DELIVERY_LABEL_DIR),
            self::CHRONOPOST_HOME_DELIVERY_LABEL_TYPE                 => ChronopostHomeDelivery::getConfigValue(self::CHRONOPOST_HOME_DELIVERY_LABEL_TYPE),
            self::CHRONOPOST_HOME_DELIVERY_PASSWORD                   => ChronopostHomeDelivery::getConfigValue(self::CHRONOPOST_HOME_DELIVERY_PASSWORD),
            self::CHRONOPOST_HOME_DELIVERY_TREATMENT_STATUS           => ChronopostHomeDelivery::getConfigValue(self::CHRONOPOST_HOME_DELIVERY_TREATMENT_STATUS),
            self::CHRONOPOST_HOME_DELIVERY_PRINT_AS_CUSTOMER_STATUS   => ChronopostHomeDelivery::getConfigValue(self::CHRONOPOST_HOME_DELIVERY_PRINT_AS_CUSTOMER_STATUS),
            self::CHRONOPOST_HOME_DELIVERY_EXPIRATION_DATE            => ChronopostHomeDelivery::getConfigValue(self::CHRONOPOST_HOME_DELIVERY_EXPIRATION_DATE),

            /** Shipper informations */
            self::CHRONOPOST_HOME_DELIVERY_SHIPPER_NAME1              => ChronopostHomeDelivery::getConfigValue(self::CHRONOPOST_HOME_DELIVERY_SHIPPER_NAME1),
            self::CHRONOPOST_HOME_DELIVERY_SHIPPER_NAME2              => ChronopostHomeDelivery::getConfigValue(self::CHRONOPOST_HOME_DELIVERY_SHIPPER_NAME2),
            self::CHRONOPOST_HOME_DELIVERY_SHIPPER_ADDRESS1           => ChronopostHomeDelivery::getConfigValue(self::CHRONOPOST_HOME_DELIVERY_SHIPPER_ADDRESS1),
            self::CHRONOPOST_HOME_DELIVERY_SHIPPER_ADDRESS2           => ChronopostHomeDelivery::getConfigValue(self::CHRONOPOST_HOME_DELIVERY_SHIPPER_ADDRESS2),
            self::CHRONOPOST_HOME_DELIVERY_SHIPPER_COUNTRY            => ChronopostHomeDelivery::getConfigValue(self::CHRONOPOST_HOME_DELIVERY_SHIPPER_COUNTRY),
            self::CHRONOPOST_HOME_DELIVERY_SHIPPER_CITY               => ChronopostHomeDelivery::getConfigValue(self::CHRONOPOST_HOME_DELIVERY_SHIPPER_CITY),
            self::CHRONOPOST_HOME_DELIVERY_SHIPPER_ZIP                => ChronopostHomeDelivery::getConfigValue(self::CHRONOPOST_HOME_DELIVERY_SHIPPER_ZIP),
            self::CHRONOPOST_HOME_DELIVERY_SHIPPER_CIVILITY           => ChronopostHomeDelivery::getConfigValue(self::CHRONOPOST_HOME_DELIVERY_SHIPPER_CIVILITY),
            self::CHRONOPOST_HOME_DELIVERY_SHIPPER_CONTACT_NAME       => ChronopostHomeDelivery::getConfigValue(self::CHRONOPOST_HOME_DELIVERY_SHIPPER_CONTACT_NAME),
            self::CHRONOPOST_HOME_DELIVERY_SHIPPER_PHONE              => ChronopostHomeDelivery::getConfigValue(self::CHRONOPOST_HOME_DELIVERY_SHIPPER_PHONE),
            self::CHRONOPOST_HOME_DELIVERY_SHIPPER_MOBILE_PHONE       => ChronopostHomeDelivery::getConfigValue(self::CHRONOPOST_HOME_DELIVERY_SHIPPER_MOBILE_PHONE),
            self::CHRONOPOST_HOME_DELIVERY_SHIPPER_MAIL               => ChronopostHomeDelivery::getConfigValue(self::CHRONOPOST_HOME_DELIVERY_SHIPPER_MAIL),

            /** END */
        ];

        /** Delivery types */
        foreach (self::getDeliveryTypesStatusKeys() as $statusKey) {
            $config[$statusKey] = ChronopostHomeDelivery::getConfigValue($statusKey);
        }

        /** Add a / to the end of the path for the label directory if it wasn't added manually */
        if (substr($config[self::CHRONOPOST_HOME_DELIVERY_LABEL_DIR], -1) !== '/') {
            $config[self::CHRONOPOST_HOME_DELIVERY_LABEL_DIR] .= '/';
        }

        /** Check if the label directory exists, create it if it doesn't */
        if (!is_dir($config[self::CHRONOPOST_HOME_DELIVERY_LABEL_DIR])) {
            $fs = new Filesystem();

            $fs->mkdir($config[self::CHRONOPOST_HOME_DELIVERY_LABEL_DIR]);
        }

        /** Set the local static config value */
        self::$config = $config;
    }

    /**
     * Return the local static config value or the value of a given parameter
     *
     * @param null $parameter
     * @return array|mixed|null
     */
    public static function getConfig($parameter = null)
    {
        /** Check if the local config value is set, and set it if it's not */
        if (null === self::$config) {
            self::setConfig();
        }

        /** Return the value of the config parameter given, or null if it wasn't set */
        if (null !== $parameter) {
            return (isset(self::$config[$parameter])) ? self::$config[$parameter] : null;
        }

        /** Return the local static config value */
        return self::$config;
    }

    /** Status keys of the delivery types.
     *  @return array
     */
    public static function getDeliveryTypesStatusKeys()
    {
        $statusKeys = [];

        foreach (self::CHRONOPOST_HOME_DELIVERY_DELIVERY_CODES as $name => $code) {
            $statusKeys[$name] = 'chronopost_home_delivery_delivery_' . strtolower($name) . '_status';
        }

        return $statusKeys;
    }
}