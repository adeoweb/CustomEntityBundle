'use strict';

/**
 * Reference data select field
 *
 * @author    Anaël CHARDAN <anael.chardan@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
define(
    [
        'jquery',
        'underscore',
        'oro/translator',
        'pim/fetcher-registry',
        'pim/job/common/edit/field/select',
        'pim/job/common/edit/field/field',
        'pim/common/property'
    ],
    function (
        $,
        _,
        __,
        FetcherRegistry,
        SelectField,
        BaseField,
        propertyAccessor
    ) {
        return SelectField.extend({
            /**
             * {@inherit}
             */
            configure: function () {
                return $.when(
                    FetcherRegistry.getFetcher('custom_entity').fetchAll(),
                    SelectField.prototype.configure.apply(this, arguments)
                ).then(function (referenceDataList) {
                    if (_.isEmpty(referenceDataList)) {
                        this.config.readOnly = true;
                        this.config.options = {
                            'NO OPTION': __('pim_custom_entity.import.csv.entity_name.no_reference_data')
                        };
                    } else {
                        this.config.options = referenceDataList;
                    }
                }.bind(this));
            },
            /**
             * {@inheritdoc}
             */
            render: function () {
                var currentValue = propertyAccessor.accessProperty(this.getFormData(), this.getFieldCode());
                if (!currentValue && this.config.options && !_.isEmpty(this.config.options)) {
                    var firstOption = _.first(_.keys(this.config.options));
                    if (firstOption) {
                        this.config.value = _.first(_.keys(this.config.options));
                        const data = propertyAccessor.updateProperty(
                            this.getFormData(),
                            this.getFieldCode(),
                            firstOption
                        );

                        this.setData(data, {silent: true});
                    }
                }
                BaseField.prototype.render.apply(this, arguments);

                this.$('.select2').select2();
            },
        });
    });
