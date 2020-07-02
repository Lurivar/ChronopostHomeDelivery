<?php
/*************************************************************************************/
/*      This file is part of the Thelia package.                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace ChronopostPickupPoint;

use ChronopostPickupPoint\Config\ChronopostPickupPointConst;
use ChronopostPickupPoint\Model\ChronopostPickupPointAreaFreeshippingQuery;
use ChronopostPickupPoint\Model\ChronopostPickupPointDeliveryMode;
use ChronopostPickupPoint\Model\ChronopostPickupPointDeliveryModeQuery;
use ChronopostPickupPoint\Model\ChronopostPickupPointPriceQuery;
use PDO;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Propel;
use Symfony\Component\Filesystem\Filesystem;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Core\HttpFoundation\Session\Session;
use Thelia\Install\Database;
use Thelia\Model\ConfigQuery;
use Thelia\Model\Country;
use Thelia\Model\ModuleQuery;
use Thelia\Module\AbstractDeliveryModule;
use Thelia\Module\BaseModule;
use Thelia\Module\Exception\DeliveryException;

class ChronopostPickupPoint extends AbstractDeliveryModule
{
    /** @var string */
    const DOMAIN_NAME = 'chronopost';

    /**
     * @param ConnectionInterface|null $con
     */
    public function postActivation(ConnectionInterface $con = null)
    {
        try {
            /** Security to not erase user configuration on reactivation */
            ChronopostPickupPointDeliveryModeQuery::create()->findOne();
            ChronopostPickupPointAreaFreeshippingQuery::create()->findOne();
        } catch (\Exception $e) {
            $database = new Database($con->getWrappedConnection());
            $database->insertSql(null, array(__DIR__ . '/Config/thelia.sql'));
        }

        /** Default config values */
        $defaultConfig = [
            ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_CODE_CLIENT => null,
            ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_LABEL_DIR => THELIA_LOCAL_DIR . 'chronopost',
            ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_LABEL_TYPE => "PDF",
            ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_PASSWORD => null,
            ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_TREATMENT_STATUS => "3",
            ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_PRINT_AS_CUSTOMER_STATUS => "N",
            ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_EXPIRATION_DATE => null,

            ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_SHIPPER_NAME1 => null,
            ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_SHIPPER_NAME2 => null,
            ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_SHIPPER_ADDRESS1 => ConfigQuery::read("store_address1"),
            ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_SHIPPER_ADDRESS2 => ConfigQuery::read("store_address2"),
            ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_SHIPPER_COUNTRY => null,
            ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_SHIPPER_CITY => ConfigQuery::read("store_city"),
            ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_SHIPPER_ZIP => ConfigQuery::read("store_zipcode"),
            ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_SHIPPER_CIVILITY => null,
            ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_SHIPPER_CONTACT_NAME => null,
            ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_SHIPPER_PHONE => ConfigQuery::read("store_phone"),
            ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_SHIPPER_MOBILE_PHONE => null,
            ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_SHIPPER_MAIL => ConfigQuery::read("store_email"),
        ];

        /** Defaults the delivery types status as false */
        foreach (ChronopostPickupPointConst::getDeliveryTypesStatusKeys() as $statusKey) {
            $defaultConfig[$statusKey] = false;
        }

        /** Set the default config values in the DB table if it doesn't exists yet */
        foreach ($defaultConfig as $key => $value) {
            if (null === self::getConfigValue($key, null)) {
                self::setConfigValue($key, $value);
            }
        }

        /** Check if the path given is a directory, creates it otherwise */
        $dir = self::getConfigValue(ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_LABEL_DIR, null);
        $fs = new Filesystem();
        if (!is_dir($dir)) {
            $fs->mkdir($dir);
        }

        /** Set the delivery types as not enabled in the ChronopostPickupPointDeliveryMode table
         * when activating the module for the first time
         */
        foreach (ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_DELIVERY_CODES as $title => $code) {
            if (null === $this->isDeliveryTypeSet($code)) {
                $this->setDeliveryType($code, $title);
            }
        }

    }

    /**
     * Check if a given delivery type exists in the ChronopostPickupPointDeliveryMode table
     *
     * @param $code
     * @return ChronopostPickupPointDeliveryMode
     */
    public function isDeliveryTypeSet($code)
    {
        return ChronopostPickupPointDeliveryModeQuery::create()->findOneByCode($code);
    }

    /**
     * Add a delivery type to the ChronopostPickupPointDeliveryMode table
     *
     * @param $code
     * @param $title
     */
    public function setDeliveryType($code, $title)
    {
        $newDeliveryType = new ChronopostPickupPointDeliveryMode();

        try {
            $newDeliveryType
                ->setCode($code)
                ->setTitle($title)
                ->setFreeshippingActive(false)
                ->setFreeshippingFrom(null)
                ->save();
        } catch (\Exception $e) {

        }
    }

    /**
     * Verify if the are asked by the user is in the list of areas added to the shipping zones
     *
     * @param Country $country
     * @return bool
     */
    public function isValidDelivery(Country $country)
    {
        /** @TODO Change to CountryArea which is not deprecated */
        $areaId = $country->getAreaId();

        $prices = ChronopostPickupPointPriceQuery::create()
            ->filterByAreaId($areaId)
            ->findOne();

        $freeShipping = ChronopostPickupPointDeliveryModeQuery::create()
            ->findOneByFreeshippingActive(true);

        /** Check if Chronopost delivers in the asked area */
        if (null !== $prices || null !== $freeShipping) {
            return true;
        }
        return false;
    }

    /**
     * Return the delivery type of an ongoing order.
     *
     * @param Request|Session $request
     * @return null|string
     */
    public function getDeliveryType($request)
    {
        $deliveryMode = $request->get('chronopost-pickup-point-delivery-mode');

        $deliveryCodes = array_change_key_case(ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_DELIVERY_CODES, CASE_LOWER);

        if ($deliveryMode) {
            return $deliveryCodes[strtolower($deliveryMode)];
        }

        return null;
    }

    /**
     * Return the postage price for a given area, cart weight, cart amount, and delivery type
     *
     * @param $areaId
     * @param $weight
     * @param int $cartAmount
     * @param null $deliveryCode
     * @return int
     * @throws \Exception
     */
    public static function getPostageAmount($areaId, $weight, $cartAmount = 0, $deliveryCode = null)
    {
        if (null === $deliveryType = ChronopostPickupPointDeliveryModeQuery::create()->findOneByCode($deliveryCode)) {
            throw new \Exception("The delivery code given is not supported by the module.");
        }

        /** Check if freeshipping is activated for this delivery type */
        try {
            $freeShipping = $deliveryType->getFreeshippingActive();
        } catch (\Exception $e) {
            $freeShipping = false;
        }

        /** Get the total cart price needed to have a free shipping for all areas, if it exists */
        try {
            $freeShippingFrom = $deliveryType->getFreeshippingFrom();
        } catch (\Exception $er) {
            $freeShippingFrom = null;
        }

        /** Set the initial postage price as null */
        $postage = null;

        /** If free shipping is enabled, skip and return 0 */
        if (!$freeShipping) {

            /** If a minimum price for free shipping is defined and the amount of the cart reach this limit, return 0. */
            if (null !== $freeShippingFrom && $freeShippingFrom <= $cartAmount) {
                return 0;
            }

            /** Search the list of prices and order it in ascending order */
            $areaPrices = ChronopostPickupPointPriceQuery::create()
                ->filterByDeliveryModeId($deliveryType->getId())
                ->filterByAreaId($areaId)
                ->filterByWeightMax($weight, Criteria::GREATER_EQUAL)
                ->_or()
                ->filterByWeightMax(null)
                ->filterByPriceMax($cartAmount, Criteria::GREATER_EQUAL)
                ->_or()
                ->filterByPriceMax(null)
                ->orderByWeightMax()
                ->orderByPriceMax();

            /** Find the correct postage price for the cart weight and price according to the area and delivery mode in $areaPrices*/
            $firstPrice = $areaPrices
                ->find()
                ->getFirst()
            ;

            /** If no price was found, return null */
            if (null === $firstPrice) {
                return null;
            }

            /** Get the minimum price for free shipping in the area of the order */
            $cartAmountFreeShipping = ChronopostPickupPointAreaFreeshippingQuery::create()
                ->filterByAreaId($areaId)
                ->filterByDeliveryModeId($deliveryType->getId())
                ->findOne();

            if (null !== $cartAmountFreeShipping) {
                $cartAmountFreeShipping = $cartAmountFreeShipping->getCartAmount();
            }

            /** If the cart price is superior to the minimum price for free shipping in the area of the order,
             * return the postage as free.
             */
            if ($cartAmountFreeShipping !== null && $cartAmountFreeShipping <= $cartAmount) {
                return 0;
            }

            $postage = $firstPrice->getPrice();
        } else {
            return 0;
        }

        return $postage;
    }

    /**
     * Return the minimum postage price of a list of areas, for a given cart weight, price, and delivery type.
     *
     * @param $areaIdArray
     * @param $cartWeight
     * @param $cartAmount
     * @param $deliveryType
     * @return int|null
     */
    public function getMinPostage($areaIdArray, $cartWeight, $cartAmount, $deliveryType)
    {
        $minPostage = null;

        foreach ($areaIdArray as $areaId) {
            try {
                $postage = self::getPostageAmount($areaId, $cartWeight, $cartAmount, $deliveryType);
                if (null === $postage) {
                    continue ;
                }
                if ($minPostage === null || $postage < $minPostage) {
                    $minPostage = $postage;
                    if ($minPostage === 0) {
                        break;
                    }
                }
            } catch (\Exception $ex) {
                throw new DeliveryException($ex->getMessage()); //todo : Make a better catch, duh
            }
        }

        if (null === $minPostage) {
            throw new DeliveryException("Chronopost delivery unavailable for your cart weight or delivery country");
        }

        return $minPostage;
    }

    /**
     * Return an array of the codes of all delivery types that have been activated
     * in the backOffice configuration page.
     *
     * @return array
     */
    public function getActivatedDeliveryTypes()
    {
        $config = ChronopostPickupPointConst::getConfig();
        $deliveryTypes = ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_DELIVERY_CODES;
        $activatedDeliveryTypes = [];

        foreach (ChronopostPickupPointConst::getDeliveryTypesStatusKeys() as $deliveryTypeName => $statusKey) {
            if (true === (bool)$config[$statusKey]) {
                $activatedDeliveryTypes[] = $deliveryTypes[$deliveryTypeName];
            }
        }

        return $activatedDeliveryTypes;
    }

    /**
     * Return the postage of an ongoing order, or the minimum expected postage before the user chooses what delivery types he wants.
     *
     * @param Country $country
     * @return float|int|\Thelia\Model\OrderPostage
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function getPostage(Country $country)
    {
        $request = $this->getRequest();

        $cartWeight = $request->getSession()->getSessionCart($this->getDispatcher())->getWeight();
        $cartAmount = $request->getSession()->getSessionCart($this->getDispatcher())->getTaxedAmount($country);


        /** Get the delivery type of an ongoing order by looking at the request */
        $deliveryType = $this->getDeliveryType($request);

        /** If no delivery type was found, search again in the session. */
        if (null === $deliveryType) {
            $deliveryType = $this->getDeliveryType($request->getSession());
        }

        $deliveryArray[] = $deliveryType;

        /** If still no delivery type is found, create an array of all activated delivery types to search
         *  which one has the lowest delivery price.
         */
        if (null === $deliveryType) {
            $deliveryArray = $this->getActivatedDeliveryTypes();
        }

        /** Check what areas are covered in the shipping zones defined by the admin */
        $areaIdArray = $this->getAllAreasForCountry($country);
        if (empty($areaIdArray)) {
            throw new DeliveryException("Your delivery country is not covered by Chronopost");
        }

        $postage = null;

        /** If no delivery type was given, the loop should continue until the postage for each delivery types was
         *  found, then return the minimum one. Otherwise, the loop should stop after the first iteration.
         */
        if ($deliveryArray !== null) {
            $y = 0;
            $postage = $this->getMinPostage($areaIdArray, $cartWeight, $cartAmount, $deliveryArray[$y]);

            while (isset($deliveryArray[$y]) && !empty($deliveryArray[$y]) && null !== $deliveryArray[$y]) {
                if ($postage > $this->getMinPostage($areaIdArray, $cartWeight, $cartAmount, $deliveryArray[$y])) {
                    $postage = $this->getMinPostage($areaIdArray, $cartWeight, $cartAmount, $deliveryArray[$y]);
                }
                $y++;
            }
        }

        /** If no postage was found, we throw an exception */
        if (null === $postage) {
            throw new DeliveryException("Chronopost delivery unavailable for your cart weight or delivery country");
        }

        /** Get the postage for the shipping zones we've just got */

        ///** If delivery is free, set it to a minimal number so the price will still appear. It will be rounded up to 0 */
        //if (0 == $postage) {
        //    $postage = 0.000001;
        //}

        return (float)$postage;
    }

    /**
     * Returns ids of area containing this country and covers by this module
     * @param Country $country
     * @return array Area ids
     */
    private function getAllAreasForCountry(Country $country)
    {
        $areaArray = [];

        $sql = "SELECT ca.area_id as area_id FROM country_area ca
               INNER JOIN area_delivery_module adm ON (ca.area_id = adm.area_id AND adm.delivery_module_id = :p0)
               WHERE ca.country_id = :p1";

        $con = Propel::getConnection();

        $stmt = $con->prepare($sql);
        $stmt->bindValue(':p0', $this->getModuleModel()->getId(), PDO::PARAM_INT);
        $stmt->bindValue(':p1', $country->getId(), PDO::PARAM_INT);
        $stmt->execute();

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $areaArray[] = $row['area_id'];
        }

        return $areaArray;
    }

    public function getDeliveryMode()
    {
        return "pickup";
    }
}
