Ext.define('SafeStartApp.view.pages.panel.VehicleInspection', {
    extend: 'Ext.Panel',

    alias: 'widget.SafeStartVehicleInspection',

    config: {
        name: 'vehicle-inspection',
        layout: {
            type: 'card'
        }
    },

    initialize: function () {
        this.callParent();

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
                            alerts;
                        Ext.each(fieldSet.config.alerts, function (alert) {
                            alerts = [];
                            if (alert.triggerValue.match(new RegExp(radio.getValue(), 'i'))) {
                                Ext.Msg.alert('CHECKLIST', alert.alertMessage);
                                alerts.push(alert);
                            }
                        });
                        me.setAlerts(fieldSet.config.fieldId, alerts);
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
        return {
            xtype: 'dataview',
            // TODO: fix height
            height: 200,
            store: {
                fields: ['groupName'],
                data: passedCards
            },
            itemTpl: '<div class="sfa-dataview-item">{groupName}</div>',
            items: [{
                xtype: 'titlebar',
                title: 'Vehicle details'
            }]
        };
    },

    createAlertsView: function (triggeredAlerts) {
        var items = [{
            xtype: 'titlebar',
            title: 'Alerts'            
        }];
        Ext.each(triggeredAlerts, function (triggeredAlert) {
            Ext.each(triggeredAlert.alerts, function (alert) {
                items.push({
                    xtype: 'panel',
                    fieldId: triggeredAlert.fieldId,
                    html: alert.alertMessage
                });
            });
        });
        return {
            xtype: 'panel',
            items: items 
        };
    }
});