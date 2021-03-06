<?php

namespace ChronopostHomeDelivery\Form;


use ChronopostHomeDelivery\Config\ChronopostHomeDeliveryConst;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;

class ChronopostHomeDeliveryConfigurationForm extends BaseForm
{
    protected function buildForm()
    {
        $config = ChronopostHomeDeliveryConst::getConfig();

        $this->formBuilder

            /** Chronopost basic informations */
            ->add(
                ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_CODE_CLIENT,
                "text",
                [
                    'required'      => true,
                    'data'          => $config[ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_CODE_CLIENT],
                    'label'         => Translator::getInstance()->trans("Chronopost client ID"),
                    'label_attr'    => [
                        'for'           => 'title',
                    ],
                    'attr'          => [
                        'placeholder'   => Translator::getInstance()->trans("Your Chronopost client ID"),
                    ],
                ]
            )
            ->add(ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_LABEL_DIR,
                "text",
                [
                    'required'      => true,
                    'data'          => $config[ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_LABEL_DIR],
                    'label'         => Translator::getInstance()->trans("Directory where to save Chronopost labels"),
                    'label_attr'    => [
                        'for'           => 'title',
                    ],
                    'attr'          => [
                        'placeholder'   => THELIA_LOCAL_DIR . 'chronopost',
                    ],
                ]
            )

            ->add(ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_PASSWORD,
                "text",
                [
                    'required'      => true,
                    'data'          => $config[ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_PASSWORD],
                    'label'         => Translator::getInstance()->trans("Chronopost password"),
                    'label_attr'    => [
                        'for'           => 'title',
                    ],
                    'attr'          => [
                        'placeholder'   => Translator::getInstance()->trans("Your Chronopost password"),
                    ],
                ]
            )
            ->add(ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_TREATMENT_STATUS,
                "text",
                [
                    'required'      => true,
                    'data'          => $config[ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_TREATMENT_STATUS],
                    'label'         => Translator::getInstance()->trans("\"Processing\" order status ID"),
                    'label_attr'    => [
                        'for'           => 'title',
                    ],
                    'attr'          => [
                        'placeholder'   => Translator::getInstance()->trans("\"Processing\" order status ID"),
                    ],
                ]
            )
            ->add(ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_LABEL_TYPE,
                "choice",
                [
                    'required'      => true,
                    'data'          => $config[ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_LABEL_TYPE],
                    'label'         => Translator::getInstance()->trans("Label file type"),
                    'label_attr'    => [
                        'for'           => 'level_field',
                    ],
                    'choices'       => [
                        "PDF"           => "PDF label with proof of deposit laser printer",
                        "SPD"           => "PDF label without proof of deposit laser printer",
                        "THE"           => "PDF label without proof of deposit for thermal printer",
                        "Z2D"           => "ZPL label with proof of deposit for thermal printer",
                    ],
                ]
            )
            ->add(ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_PRINT_AS_CUSTOMER_STATUS,
                "choice",
                [
                    'required'      => true,
                    'data'          => $config[ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_PRINT_AS_CUSTOMER_STATUS],
                    'label'         => Translator::getInstance()->trans("For the sending address, use :"),
                    'label_attr'    => [
                        'for'           => 'level_field',
                    ],
                    'choices'       => [
                        "N"           => "The shipper's one (Default value)",
                        "Y"           => "The customer's one (Do not use without knowing what it is)",
                    ],
                ]
            )
            ->add(ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_EXPIRATION_DATE,
                "text",
                [
                    'required'      => false,
                    'data'          => $config[ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_EXPIRATION_DATE],
                    'label'         => Translator::getInstance()->trans("Number of days before expiration date from the moment the order is in \"Processing\" status"),
                    'label_attr'    => [
                        'for'           => 'title',
                    ],
                    'attr'          => [
                        'placeholder'   => Translator::getInstance()->trans("5"),
                    ],
                ]
            )

            /** Shipper Informations */
            ->add(ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_SHIPPER_NAME1,
                "text",
                [
                    'required'      => true,
                    'data'          => $config[ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_SHIPPER_NAME1],
                    'label'         => Translator::getInstance()->trans("Company name 1"),
                    'label_attr'    => [
                        'for'           => 'title',
                    ],
                    'attr'          => [
                        'placeholder'   => Translator::getInstance()->trans("Dupont & co")
                    ],
                ]
            )
            ->add(ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_SHIPPER_NAME2,
                "text",
                [
                    'required'      => false,
                    'data'          => $config[ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_SHIPPER_NAME2],
                    'label'         => Translator::getInstance()->trans("Company name 2"),
                    'label_attr'    => [
                        'for'           => 'title',
                    ],
                    'attr'          => [
                        'placeholder'   => Translator::getInstance()->trans("")
                    ],
                ]
            )
            ->add(ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_SHIPPER_ADDRESS1,
                "text",
                [
                    'required'      => true,
                    'data'          => $config[ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_SHIPPER_ADDRESS1],
                    'label'         => Translator::getInstance()->trans("Address 1"),
                    'label_attr'    => [
                        'for'           => 'title',
                    ],
                    'attr'          => [
                        'placeholder'   => Translator::getInstance()->trans("Les Gardelles")
                    ],
                ]
            )
            ->add(ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_SHIPPER_ADDRESS2,
                "text",
                [
                    'required'      => false,
                    'data'          => $config[ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_SHIPPER_ADDRESS2],
                    'label'         => Translator::getInstance()->trans("Address 2"),
                    'label_attr'    => [
                        'for'           => 'title',
                    ],
                    'attr'          => [
                        'placeholder'   => Translator::getInstance()->trans("Route de volvic")
                    ],
                ]
            )
            ->add(ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_SHIPPER_COUNTRY,
                "text",
                [
                    'required'      => true,
                    'data'          => $config[ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_SHIPPER_COUNTRY],
                    'label'         => Translator::getInstance()->trans("Country (ISO ALPHA-2 format)"),
                    'label_attr'    => [
                        'for'           => 'title',
                    ],
                    'attr'          => [
                        'placeholder'   => Translator::getInstance()->trans("FR")
                    ],
                ]
            )
            ->add(ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_SHIPPER_CITY,
                "text",
                [
                    'required'      => true,
                    'data'          => $config[ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_SHIPPER_CITY],
                    'label'         => Translator::getInstance()->trans("City"),
                    'label_attr'    => [
                        'for'           => 'title',
                    ],
                    'attr'          => [
                        'placeholder'   => Translator::getInstance()->trans("Paris")
                    ],
                ]
            )
            ->add(ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_SHIPPER_ZIP,
                "text",
                [
                    'required'      => true,
                    'data'          => $config[ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_SHIPPER_ZIP],
                    'label'         => Translator::getInstance()->trans("ZIP code"),
                    'label_attr'    => [
                        'for'           => 'title',
                    ],
                    'attr'          => [
                        'placeholder'   => Translator::getInstance()->trans("93000")
                    ],
                ]
            )
            ->add(ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_SHIPPER_CIVILITY,
                "text",
                [
                    'required'      => true,
                    'data'          => $config[ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_SHIPPER_CIVILITY],
                    'label'         => Translator::getInstance()->trans("Civility"),
                    'label_attr'    => [
                        'for'           => 'title',
                    ],
                    'attr'          => [
                        'placeholder'   => Translator::getInstance()->trans("E (Madam), L (Miss), M (Mister)")
                    ],
                ]
            )
            ->add(ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_SHIPPER_CONTACT_NAME,
                "text",
                [
                    'required'      => true,
                    'data'          => $config[ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_SHIPPER_CONTACT_NAME],
                    'label'         => Translator::getInstance()->trans("Contact name"),
                    'label_attr'    => [
                        'for'           => 'title',
                    ],
                    'attr'          => [
                        'placeholder'   => Translator::getInstance()->trans("Jean Dupont")
                    ],
                ]
            )
            ->add(ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_SHIPPER_PHONE,
                "text",
                [
                    'required'      => false,
                    'data'          => $config[ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_SHIPPER_PHONE],
                    'label'         => Translator::getInstance()->trans("Phone"),
                    'label_attr'    => [
                        'for'           => 'title',
                    ],
                    'attr'          => [
                        'placeholder'   => Translator::getInstance()->trans("0142080910")
                    ],
                ]
            )
            ->add(ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_SHIPPER_MOBILE_PHONE,
                "text",
                [
                    'required'      => false,
                    'data'          => $config[ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_SHIPPER_MOBILE_PHONE],
                    'label'         => Translator::getInstance()->trans("Mobile phone"),
                    'label_attr'    => [
                        'for'           => 'title',
                    ],
                    'attr'          => [
                        'placeholder'   => Translator::getInstance()->trans("0607080910")
                    ],
                ]
            )
            ->add(ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_SHIPPER_MAIL,
                "text",
                [
                    'required'      => true,
                    'data'          => $config[ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_SHIPPER_MAIL],
                    'label'         => Translator::getInstance()->trans("E-mail"),
                    'label_attr'    => [
                        'for'           => 'title',
                    ],
                    'attr'          => [
                        'placeholder'   => Translator::getInstance()->trans("jeandupont@gmail.com")
                    ],
                ]
            )
        ;

        /** Delivery types */
        foreach (ChronopostHomeDeliveryConst::getDeliveryTypesStatusKeys() as $deliveryTypeName => $statusKey) {
            $this->formBuilder
                ->add($statusKey,
                    "checkbox",
                    [
                        'required'      => false,
                        'data'          => (bool)$config[$statusKey],
                        'label'         => Translator::getInstance()->trans("\"" . $deliveryTypeName . "\" Delivery (Code : " . ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_DELIVERY_CODES[$deliveryTypeName] . ")"),
                        'label_attr'    => [
                            'for'           => 'title',
                        ],
                    ]
                )
            ;
        }

        /** BUILDFORM END */
    }

    public function getName()
    {
        return "chronopost_home_delivery_configuration_form";
    }
}