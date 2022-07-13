<?php

namespace Pim\Bundle\CustomEntityBundle\Connector\ArrayConverter\FlatToStandard;

use Akeneo\Tool\Component\Connector\ArrayConverter\ArrayConverterInterface;

/**
 * @author    Mathias METAYER <mathias.metayer@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ReferenceData implements ArrayConverterInterface
{
    /**
     * {@inheritdoc}
     *
     * Converts flat csv array to standard structured array:
     *
     * Before:
     * [
     *      'code'        => 'mycode',
     *      'label-fr_FR' => 'Label en français',
     *      'label-en_US' => 'Label in English',
     *      'other_prop'  => 'other_value',
     * ]
     *
     * After:
     * [
     *      'code'         => 'mycode',
     *      'translations' => [
     *          'fr_FR' => [
     *              'label' => 'Label en français',
     *           ],
     *          'en_US' => [
     *              'label' => 'Label in English',
     *           ],
     *      ],
     *      'other_prop' => 'other_value',
     * ]
     */
    public function convert(array $item, array $options = [])
    {
        $convertedItem = [];
        foreach ($item as $field => $data) {
            $convertedItem = $this->convertField($convertedItem, $field, $data);
        }

        return $convertedItem;
    }

    /**
     * @param array $convertedItem
     * @param string $field
     * @param mixed $data
     *
     * @return array
     */
    protected function convertField($convertedItem, $field, $data)
    {
        if (false === strpos($field, '-')) {
            $convertedItem[$field] = $data;
        } else {
            $fieldTokens = explode('-', $field);
            $fieldName = $fieldTokens[0];
            $fieldLocale = $fieldTokens[1];

            $convertedItem['translations'][$fieldLocale][$fieldName] = $data;
        }

        return $convertedItem;
    }
}
