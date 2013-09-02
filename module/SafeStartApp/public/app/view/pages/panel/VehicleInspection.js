Ext.define('SafeStartApp.view.pages.panel.VehicleInspection', {
    extend: 'Ext.Panel',

    alias: 'widget.SafeStartVehicleInspection',
    requires: [
        'SafeStartApp.store.ChecklistAlerts'
    ],

    config: {
        name: 'vehicle-inspection',
        layout: {
            type: 'card'
        }
    },

    initialize: function () {
        this.callParent();

        this.setAlertsStore(SafeStartApp.store.ChecklistAlerts.create({}));

        var submitMsgBox = Ext.create('Ext.MessageBox', {
            cls: 'sfa-messagebox-confirm',
            message: 'Please confirm your submission',
            hidden: true,
            buttons: [{
                ui: 'confirm',
                action: 'confirm',
                text: 'Confirm'
            }, {
                ui: 'action',
                text: 'Cancel',
                handler: function (btn) {
                    btn.up('sheet[cls=sfa-messagebox-confirm]').hide();
                }
            }]
        });
        this.add(submitMsgBox);
    },

    setAlertsStore: function (store) {
        this._alertsStore = store;
    },

    getAlertsStore: function () {
        return this._alertsStore;
    },

    alerts: [],
    setAlerts: function (fieldId, value) {
        this.alerts[fieldId] = value;
        console.log(fieldId);
        console.log(this.alerts);
    },

    getAlerts: function (fieldId) {
        return this.alerts[fieldId];
    },

    loadChecklist: function (checklists, id) {
        var checklistForms = [],
            checklistAdditionalForms = [],
            choiseAdditionalFields = [];

        this.getAlertsStore().removeAll();
        this.vehicleId = id || 0;
        checklists = checklists || [];

        Ext.each(this.query('formpanel'), function (panel) {
            this.remove(panel);
        }, this);

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

        this.add(checklistForms);

        if (choiseAdditionalFields.length) {
            this.add({
                xtype: 'formpanel',
                name: 'checklist-card-choise-additional',
                items: [{
                    xtype: 'fieldset',
                    items: choiseAdditionalFields
                },{
                    xtype: 'titlebar',
                    docked: 'top',
                    title: 'Daily inspection checklist additional'
                }, {
                    xtype: 'toolbar',
                    docked: 'bottom',
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
        this.add(checklistAdditionalForms);

        this.add({
            xtype: 'formpanel',
            name: 'checklist-card-review',
            items: [{
                xtype: 'titlebar',
                docked: 'top',
                title: 'Review'
            }, {
                xtype: 'toolbar',
                margin: '40 0 0 0',
                docked: 'bottom',
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
            xtype: 'toolbar',
            docked: 'bottom',
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
            additional: checklist.additional,
            groupName: checklist.groupName,
            items: fields
        };
    },

    createFields: function (fieldsData) {
        var fields = [];
        var alertsStore = this.getAlertsStore();


        Ext.each(fieldsData, function (fieldData) {
            if (Ext.isArray(fieldData.alerts) && fieldData.alerts[0]) {
                var alert = fieldData.alerts[0];
                var alertRecord = Ext.create('SafeStartApp.model.ChecklistAlert', {
                    alertMessage: alert.alertMessage,
                    triggerValue: alert.triggerValue,
                    fieldId: fieldData.fieldId
                });
                alertsStore.add(alertRecord);
            }
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
        var me = this,
            name = 'checklist-radio-' + fieldData.fieldId,
            optionFields = [];

        Ext.each(fieldData.options, function (option) {
            optionFields.push({
                xtype: 'radiofield',
                value: option.value,
                label: option.label,
                fieldId: fieldData.fieldId,
                name: name,
                checked: fieldData.fieldValue === option.value,
                listeners: {
                    check: function (radio) {
                        var fieldSet = radio.up('fieldset'),
                            alert = me.getAlertsStore().findRecord('fieldId', fieldSet.config.fieldId);

                        if (alert !== null) {
                            if (alert.get('triggerValue').match(new RegExp(radio.getValue(), 'i'))) {
                                Ext.Msg.alert('CHECKLIST', alert.get('alertMessage'));
                                alert.set('active', true);
                            } else {
                                alert.set('active', false);
                            }
                        }
                    }
                }
            });
        });
        return {
            xtype: 'fieldset',
            alerts: fieldData.alerts,
            layout: {
                type: 'hbox',
                pack: 'center'
            },
            defaults: {
                labelAlign: 'right'
            },
            fieldId: fieldData.fieldId,
            triggerable: true,
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
            fieldId: fieldData.fieldId
        };
    },

    createGroupField: function (fieldData) {
        return {
            xtype: 'fieldset',
            fieldId: fieldData.fieldId,
            title: fieldData.fieldName,
            items: this.createFields(fieldData.items)
        };
    },

    updateReview: function (passedCards, alerts) {
        var reviewCard = this.down('formpanel[name=checklist-card-review]');
        reviewCard.removeAll();
        console.log(passedCards);
        reviewCard.add(this.createVehicleDetailsView(passedCards));
        reviewCard.add(this.createAlertsView(alerts));
    },

    createVehicleDetailsView: function (passedCards) {
        var items = [{
            xtype: 'titlebar',
            title: 'Vehicle details'
        }];
        Ext.each(passedCards, function (card) {
            items.push({
                xtype: 'checkboxfield',
                labelWidth: '90%',
                label: card.groupName,
                checked: true,
                listeners: {
                    uncheck: function (checkbox) {
                        checkbox.setChecked(true);
                    }
                }
            });
        });
        return {
            xtype: 'panel',
            items: items
        };
    },

    createAlertsView: function (alerts) {
        var items = [{
            xtype: 'titlebar',
            title: 'Alerts'            
        }];
        Ext.each(alerts, function (alert) {
            items.push({
                xtype: 'container',
                name: 'alert-container',
                fieldId: alert.get('fieldId'),
                alertModel: alert,
                items: [{
                    label: alert.get('alertMessage'),
                    xtype: 'checkboxfield',
                    checked: true,
                    labelWidth: '90%',
                    listeners: {
                        uncheck: function (checkbox) {
                            checkbox.setChecked(true);
                        }
                    }
                }, {
                    xtype: 'textfield',
                    label: 'Additional comments',
                    value: alert.get('comment'),
                    labelAlign: 'top',
                    listeners: {
                        change: function (textfield, value) {
                            alert.set('comment', value);
                        }
                    }
                }, {
                    xtype: 'button',
                    text: 'Add photo',
                    action: 'add-photo'
                }]
            });
        });
        return {
            xtype: 'panel',
            items: items 
        };
    }
});