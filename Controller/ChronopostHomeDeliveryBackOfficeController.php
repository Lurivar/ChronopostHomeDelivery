<?php

namespace ChronopostHomeDelivery\Controller;


use ChronopostHomeDelivery\ChronopostHomeDelivery;
use ChronopostHomeDelivery\Config\ChronopostHomeDeliveryConst;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Core\Translation\Translator;

class ChronopostHomeDeliveryBackOfficeController extends BaseAdminController
{
    /**
     * Render the module config page
     *
     * @return \Thelia\Core\HttpFoundation\Response
     */
    public function viewAction($tab)
    {
        return $this->render(
            'module-configure',
            [
                'module_code' => 'ChronopostHomeDelivery',
                'current_tab' => $tab,
            ]
        );
    }

    public function saveLabel()
    {
        if (null !== $response = $this->checkAuth([AdminResources::MODULE], 'ChronopostHomeDelivery', AccessManager::UPDATE)) {
            return $response;
        }

        $labelNbr = $this->getRequest()->get("labelNbr");
        $labelDir = $this->getRequest()->get("labelDir");

        $file = $labelDir .'/'. $labelNbr;

        if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($file).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            readfile($file);
        } else {
            return $this->viewAction('export');
            // todo : Error message
        }

        return $this->generateSuccessRedirect();
    }

    /**
     * Save configuration form - Chronopost informations
     *
     * @return mixed|null|\Symfony\Component\HttpFoundation\Response|\Thelia\Core\HttpFoundation\Response
     */
    public function saveAction()
    {
        if (null !== $response = $this->checkAuth([AdminResources::MODULE], 'ChronopostHomeDelivery', AccessManager::UPDATE)) {
            return $response;
        }

        $form = $this->createForm("chronopost_home_delivery_configuration_form");

        try {
            $data = $this->validateForm($form)->getData();

            /** Basic informations */
            ChronopostHomeDelivery::setConfigValue(ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_CODE_CLIENT, $data[ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_CODE_CLIENT]);
            ChronopostHomeDelivery::setConfigValue(ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_LABEL_DIR, $data[ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_LABEL_DIR]);
            ChronopostHomeDelivery::setConfigValue(ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_LABEL_TYPE, $data[ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_LABEL_TYPE]);
            ChronopostHomeDelivery::setConfigValue(ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_PASSWORD, $data[ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_PASSWORD]);
            ChronopostHomeDelivery::setConfigValue(ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_TREATMENT_STATUS, $data[ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_TREATMENT_STATUS]);
            ChronopostHomeDelivery::setConfigValue(ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_PRINT_AS_CUSTOMER_STATUS, $data[ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_PRINT_AS_CUSTOMER_STATUS]);
            ChronopostHomeDelivery::setConfigValue(ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_EXPIRATION_DATE, $data[ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_EXPIRATION_DATE]);

            /** Delivery types */
            foreach (ChronopostHomeDeliveryConst::getDeliveryTypesStatusKeys() as $statusKey) {
                ChronopostHomeDelivery::setConfigValue($statusKey, $data[$statusKey]);
            }

        } catch (\Exception $e) {
            $this->setupFormErrorContext(
                Translator::getInstance()->trans(
                    "Error",
                    [],
                    ChronopostHomeDelivery::DOMAIN_NAME
                ),
                $e->getMessage(),
                $form
            );

            return $this->viewAction('configure');
        }

        return $this->generateSuccessRedirect($form);
    }

    /**
     * Save configuration form - Shipper informations
     *
     * @return mixed|null|\Symfony\Component\HttpFoundation\Response|\Thelia\Core\HttpFoundation\Response
     */
    public function saveActionShipper()
    {
        if (null !== $response = $this->checkAuth([AdminResources::MODULE], 'ChronopostHomeDelivery', AccessManager::UPDATE)) {
            return $response;
        }

        $form = $this->createForm("chronopost_home_delivery_configuration_form");

        try {
            $data = $this->validateForm($form)->getData();

            /** Shipper informations */
            ChronopostHomeDelivery::setConfigValue(ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_SHIPPER_NAME1, $data[ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_SHIPPER_NAME1]);
            ChronopostHomeDelivery::setConfigValue(ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_SHIPPER_NAME2, $data[ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_SHIPPER_NAME2]);
            ChronopostHomeDelivery::setConfigValue(ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_SHIPPER_ADDRESS1, $data[ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_SHIPPER_ADDRESS1]);
            ChronopostHomeDelivery::setConfigValue(ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_SHIPPER_ADDRESS2, $data[ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_SHIPPER_ADDRESS2]);
            ChronopostHomeDelivery::setConfigValue(ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_SHIPPER_COUNTRY, $data[ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_SHIPPER_COUNTRY]);
            ChronopostHomeDelivery::setConfigValue(ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_SHIPPER_CITY, $data[ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_SHIPPER_CITY]);
            ChronopostHomeDelivery::setConfigValue(ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_SHIPPER_ZIP, $data[ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_SHIPPER_ZIP]);
            ChronopostHomeDelivery::setConfigValue(ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_SHIPPER_CIVILITY, $data[ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_SHIPPER_CIVILITY]);
            ChronopostHomeDelivery::setConfigValue(ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_SHIPPER_CONTACT_NAME, $data[ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_SHIPPER_CONTACT_NAME]);
            ChronopostHomeDelivery::setConfigValue(ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_SHIPPER_PHONE, $data[ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_SHIPPER_PHONE]);
            ChronopostHomeDelivery::setConfigValue(ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_SHIPPER_MOBILE_PHONE, $data[ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_SHIPPER_MOBILE_PHONE]);
            ChronopostHomeDelivery::setConfigValue(ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_SHIPPER_MAIL, $data[ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_SHIPPER_MAIL]);

        } catch (\Exception $e) {
            $this->setupFormErrorContext(
                Translator::getInstance()->trans(
                    "Error",
                    [],
                    ChronopostHomeDelivery::DOMAIN_NAME
                ),
                $e->getMessage(),
                $form
            );

            return $this->viewAction('configure');
        }

        return $this->generateSuccessRedirect($form);
    }
}