<?php

namespace AHT\DeliveryStep\Block\Adminhtml\System\Form\Field;

use Magento\Framework\DataObject;

class DateOff extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    private $dateOffRenderer;
    private $dateRenderer;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $data);
    }

    /**
     * Prepare to render
     *
     * @return void
     */
    protected function _prepareToRender()
    {

        $this->addColumn(
            'date_off',
            [
                'label' => __('Date'),
                'id' => 'date_off',
                'class' => 'daterecuring'
            ]
        );

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }

    protected function _prepareArrayRow(DataObject $row): void
    {
        $options = [];
        $row->setData('option_extra_attrs', $options);
    }

    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $html = parent::_getElementHtml($element);

        $script = '<script type="text/javascript">
                require(["jquery", "jquery/ui", "mage/calendar"], function (jq) {
                    jq(function(){
                        function bindDatePicker() {
                            setTimeout(function() {
                                jq(".daterecuring").datepicker( { dateFormat: "yy/mm/dd" } );
                            }, 50);
                        }
                        bindDatePicker();
                        jq("button.action-add").on("click", function(e) {
                            bindDatePicker();
                        });
                    });
                });
            </script>';
        $html .= $script;
        return $html;
    }
}
