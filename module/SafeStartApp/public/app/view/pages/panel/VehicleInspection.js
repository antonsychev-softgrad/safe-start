Ext.define('SafeStartApp.view.pages.panel.VehicleInspection', {
    extend: 'Ext.Panel',

    alias: 'widget.SafeStartVehicleInspection',
    requires: [
        'SafeStartApp.store.ChecklistAlerts',
        'SafeStartApp.view.components.FileUpload'
    ],

    config: {
        name: 'vehicle-inspection',
        cls: 'sfa-vehicle-inspection',
        layout: {
            type: 'card'
        }
    },

    initialize: function () {
        this.callParent();

        this.setAlertsStore(SafeStartApp.store.ChecklistAlerts.create({}));

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
                cls: 'sfa-checklist-form',
                layout: {
                    type: 'vbox',
                    align: 'center'
                },
                items: [{
                    xtype: 'fieldset',
                    maxWidth: 900,
                    width: '100%',
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
            cls: 'sfa-checklist-form',
            layout: {
                type: 'vbox',
                align: 'center'
            },
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
            layout: {
                type: 'vbox',
                align: 'center'
            },
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
                    critical: alert.critical,
                    alertDescription: alert.alertDescription,
                    triggerValue: alert.triggerValue,
                    fieldId: fieldData.fieldId,
                    photos: []
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
            maxWidth: 900,
            width: '100%',
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
                labelWidth: 50,
                fieldId: fieldData.fieldId,
                name: name,
                checked: fieldData.fieldValue === option.value,
                listeners: {
                    check: function (radio) {
                        var fieldSet = radio.up('fieldset'),
                            alert = me.getAlertsStore().findRecord('fieldId', fieldSet.config.fieldId);

                        if (alert !== null) {
                            if (alert.get('triggerValue').match(new RegExp(radio.getValue(), 'i'))) {
                                if (alert.get('critical')) {
                                    Ext.Msg.alert('CHECKLIST', alert.get('alertMessage'));
                                }
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
            triggerable: true,
            layout: {
                type: 'hbox',
                pack: 'center'
            },
            defaults: {
                labelAlign: 'right'
            },
            maxWidth: 900,
            width: '100%',
            fieldId: fieldData.fieldId,
            title: fieldData.fieldName,
            items: optionFields 
        };
    },

    createDatePickerFiled: function (fieldData) {
        return {
            xtype: 'datepickerfield',
            maxWidth: 900,
            width: '100%',
            label: fieldData.fieldName,
            fieldId: fieldData.fieldId,
            value: new Date()
        };
    },

    createCheckboxField: function (fieldData) {
        var me = this;
        return {
            xtype: 'checkboxfield',
            maxWidth: 900,
            width: '100%',
            label: fieldData.fieldName,
            labelWidth: '90%',
            fieldId: fieldData.fieldId,
            alerts: fieldData.alerts,
            triggerable: true,
            listeners: {
                check: function (checkbox) {
                    var alert = me.getAlertsStore().findRecord('fieldId', checkbox.config.fieldId);

                    if (alert !== null) {
                        if (alert.get('triggerValue').match(new RegExp(checkbox.getValue(), 'i'))) {
                            if (alert.get('critical')) {
                                Ext.Msg.alert('CHECKLIST', alert.get('alertMessage'));
                            }
                            alert.set('active', true);
                        } else {
                            alert.set('active', false);
                        }
                    }
                }
            }
        };
    },

    createGroupField: function (fieldData) {
        return {
            xtype: 'fieldset',
            maxWidth: 900,
            width: '100%',
            fieldId: fieldData.fieldId,
            title: fieldData.fieldName,
            items: this.createFields(fieldData.items)
        };
    },

    updateReview: function (passedCards, alerts) {
        var reviewCard = this.down('formpanel[name=checklist-card-review]');
        reviewCard.removeAll();
        reviewCard.add(this.createVehicleDetailsView(passedCards));
        reviewCard.add(this.createGpsView());
        reviewCard.add(this.createAlertsView(alerts));
    },

    createGpsView: function () {
        return {
            xtype: 'container',
            width: '100%',
            cls: 'sfa-vehicle-inspection-gps',
            maxWidth: 900,
            items: [{
                xtype: 'togglefield',
                label: 'GPS',
                labelWidth: 50,
                listeners: {
                    change: function(field, slider, thumb, newValue, oldValue) {
                        var container = field.up('container[cls=sfa-vehicle-inspection-gps]');
                        if (newValue) {
                            if (!container.gps) {
                                container.gps = Ext.create('Ext.util.Geolocation');
                            }
                        }
                    }
                }
            }]
        };
    },
    createVehicleDetailsView: function (passedCards) {
        var items = [{
            xtype: 'titlebar',
            title: 'Vehicle details'
        }];
        Ext.each(passedCards, function (card) {
            items.push({
                xtype: 'container',
                html: card.groupName,
                cls: card.alert ? 'checklist-details-alerts' : 'checklist-details-ok'
            });
        });
        return {
            xtype: 'fieldset',
            width: '100%',
            maxWidth: 900,
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
                xtype: 'panel',
                width: '100%',
                maxWidth: 900,
                name: 'alert-container',
                fieldId: alert.get('fieldId'),
                alertModel: alert,
                items: [{
                    xtype: 'container',
                    cls: 'checklist-alert-description',
                    html: alert.get('alertDescription')
                }, {
                    xtype: 'textfield',
                    label: 'Additional comments',
                    value: alert.get('comment'),
                    listeners: {
                        change: function (textfield, value) {
                            alert.set('comment', value);
                        }
                    }
                }, this.createImageUploadPanel(alert)
                ]
            });
        }, this);
        return items;
    },

    createImageUploadPanel: function(alert) {
        var images = [];
        Ext.each(alert.get('photos'), function (photo) {
            images.push({
                xtype: 'image', 
                height: 70,
                margin: 10,
                width: 70,
                src: 'api/image/' + photo + '/70x70'
            });
        });
        return {
            xtype: 'panel',
            name: 'image-container',
            width: '100%',
            maxWidth: 900,
            layout: 'box',
            items: [{
                xtype: 'toolbar',
                docked: 'bottom',
                items: [{
                    xtype: 'imageupload',
                    autoUpload: true,
                    url: 'api/upload-images',
                    name: 'image',
                    states: {
                        browse: {
                            text: 'Add photo'
                        },

                        uploading: {
                            loading: true
                        }
                    },
                    listeners: {
                        success: function (btn, data) {
                            var panel = btn.up('panel[name=image-container]');

                            panel.add({
                                xtype: 'image', 
                                height: 70,
                                margin: 10,
                                width: 70,
                                src: 'api/image/' + data.hash + '/70x70'
                            });
                            var photos = alert.get('photos');
                            photos.push(data.hash);
                        }
                    }
                }]
            }].concat(images)
        };
    }
});