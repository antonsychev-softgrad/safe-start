Ext.define('SafeStartApp.view.pages.Checklist', {
    extend: 'Ext.Container',
    requires: [
        'SafeStartApp.view.pages.toolbar.Checklist',
        'SafeStartApp.view.pages.nestedlist.Vehicles',
        'SafeStartApp.store.Vehicles'
    ],
    alias: 'widget.SafeStartChecklistPage',

    config: {
        title: 'Checklist',
        styleHtmlContent: true,
        scrollable: false,
        layout: 'hbox',
    },

    initialize: function () {
        this.setItems([{
            xtype: 'SafeStartChecklistToolbar',
            docked: 'top'
        }, {
            xtype: 'SafeStartNestedListVehicles',
            store: this.createVehicleStore() 
        }, {
            xtype: 'panel',
            cls: 'sfa-info-container',
            layout: 'card',
            flex: 2,
            minWidth: 150,
            scrollable: false,
            items: [{
                xtype: 'panel',
                html: 'Current Information'
            }, {
                xtype: 'panel',
                cls: 'sfa-checklist-container',
                name: 'user-checklist',
                layout: {
                    type: 'card'
                }
            }]
        }]);

        this.callParent();
    },

    createVehicleStore: function () {
        var vehiclesStore = Ext.create('SafeStartApp.store.Vehicles'),
            companyId = SafeStartApp.userModel.get('companyId');

        vehiclesStore.getProxy().setExtraParam('companyId', companyId);
        return vehiclesStore;
    },

    loadChecklist: function (checklists) {
        var checklistPanel = this.down('panel[cls~=sfa-checklist-container]'),
            checklistForms = [],
            checklistAdditionalForms = [],
            choiseAdditionalFields = [];

        Ext.each(checklistPanel.query('formpanel'), function (panel) {
            checklistPanel.remove(panel);
        });
        this.down('panel[cls=sfa-info-container]').setActiveItem(checklistPanel);

        Ext.each(checklists, function (checklist, index) {
            var checklistForm = this.createForm(checklist);
            if (checklist.additional) {
                checklistForm.name = 'checklist-card-additional';
                checklistAdditionalForms.push(checklistForm);
                choiseAdditionalFields.push({
                    xtype: 'checkboxfield',
                    label: checklist.groupName,
                    checklistGroupId: checklist.groupId
                });
            } else {
                checklistForm.name = 'checklist-card';
                checklistForms.push(checklistForm);
            }
        }, this);

        checklistPanel.add(checklistForms);

        if (choiseAdditionalFields.length) {
            checklistPanel.add({
                xtype: 'formpanel',
                name: 'checklist-card-choise-additional',
                layout: {
                    type: 'vbox',
                    align: 'stretch',
                    pack: 'bottom'
                },
                items: [{
                    xtype: 'fieldset',
                    items: choiseAdditionalFields
                },{
                    xtype: 'titlebar',
                    docked: 'top',
                    title: 'Daily inspection checklist additional'
                }, {
                    xtype: 'spacer',
                    flex: 1
                }, {
                    xtype: 'toolbar',
                    margin: '40 0 0 0',
                    layout: {
                        type: 'hbox',
                        align: 'stretch',
                        pack: 'center'
                    },
                    items: [{
                        text: 'Prev',
                        action: 'prev'
                    }, {
                        text: 'Next',
                        action: 'next'
                    }]
                }]
            });
        }
        checklistPanel.add(checklistAdditionalForms);

        checklistPanel.add({
            xtype: 'formpanel',
            name: 'checklist-card-review',
            layout: {
                type: 'vbox',
                align: 'stretch',
                pack: 'bottom'
            },
            items: [{
                xtype: 'titlebar',
                docked: 'top',
                title: 'Review'
            }, {
                xtype: 'spacer',
                flex: 1
            }, {
                xtype: 'toolbar',
                margin: '40 0 0 0',
                layout: {
                    type: 'hbox',
                    align: 'stretch',
                    pack: 'center'
                },
                items: [{
                    text: 'Prev',
                    action: 'prev'
                }, {
                    text: 'Submit',
                    action: 'submit'
                }]
            }]
        });
    },

    createForm: function (checklist) {
        var fields = this.createFields(checklist.fields);

        fields.push({
            xtype: 'titlebar',
            docked: 'top',
            title: checklist.groupName
        }, {
            xtype: 'spacer',
            flex: 1
        }, {
            xtype: 'toolbar',
            margin: '40 0 0 0',
            layout: {
                type: 'hbox',
                align: 'stretch',
                pack: 'center'
            },
            items: [{
                text: 'Prev',
                action: 'prev'
            }, {
                text: 'Next',
                action: 'next'
            }]
        });

        return {
            xtype: 'formpanel',
            cls: 'sfa-checklist-form',
            minHeight: 400,
            groupId: checklist.groupId,
            items: fields
        };
    },

    createFields: function (fieldsData) {
        var fields = [];

        Ext.each(fieldsData, function (fieldData) {
            switch(fieldData.type) {
                case 'text':
                    fields.push(this.createTextField(fieldData));
                    break;
                case 'radio':
                    fields.push(this.createRadioField(fieldData));
                    break;
                case 'datePicker':
                    fields.push(this.createDatePickerFiled(fieldData));
                    break;
                case 'group':
                    fields.push(this.createGroupField(fieldData));
                    break;
                case 'checkbox':
                    fields.push(this.createCheckboxField(fieldData));
                    break;
                default: 
                    Ext.Logger.log('Unexpected field type:' + fieldData.type, 'warn');
                    break;
            }
        }, this);
        return fields;
    },

    createTextField: function (fieldData) {
        return {
            xtype: 'textfield',
            label: fieldData.fieldName,
            fieldId: fieldData.fieldId
        };
    },

    createRadioField: function (fieldData) {
        var optionFields = [],
            name = 'radioGroup-' + fieldData.fieldId;

        Ext.each(fieldData.options, function (option) {
            optionFields.push({
                xtype: 'radiofield',
                value: option.value,
                label: option.label,
                labelWrap: true,
                name: name,
                checked: fieldData.fieldValue === option.value
            });
        });
        return {
            xtype: 'fieldset',
            layout: {
                type: 'hbox',
                pack: 'center'
            },
            defaults: {
                labelAlign: 'right'
            },
            fieldId: fieldData.fieldId,
            title: fieldData.fieldName,
            items: optionFields 
        };
    },

    createDatePickerFiled: function (fieldData) {
        return {
            xtype: 'datepickerfield',
            label: fieldData.fieldName,
            fieldId: fieldData.fieldId,
            value: new Date()
        };
    },

    createCheckboxField: function (fieldData) {
        return {
            xtype: 'checkboxfield',
            label: fieldData.fieldName,
            fieldId: fieldData.fieldId,
            getSubmitData: function () {
                // TODO: unhardcode return value
                return this.getChecked() ? 'Yes' : 'No';
            }
        };
    },

    createGroupField: function (fieldData) {
        return {
            xtype: 'fieldset',
            fieldId: fieldData.fieldId,
            title: fieldData.fieldName,
            items: this.createFields(fieldData.items)
        };
    }

});