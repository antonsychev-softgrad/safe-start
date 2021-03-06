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

    fieldValueExists: function (fields) {
        var field, 
            len,
            i;

        for (i = 0, len = fields.length; i < len; i++) {
            field = fields[i];
            switch (field.type) {
                case 'group': 
                    if (this.fieldValueExists(field.items)) {
                        return true;
                    }
                    break;
                default:
                    if (Ext.isString(field.fieldValue) && field.fieldValue.length) {
                        return true;
                    }
                    break;
            }
        }
        return false;
    },

    clearChecklist: function () {
        this.inspectionRecord = null;
        this.getAlertsStore().removeAll();
        Ext.each(this.query('formpanel'), function (panel) {
            this.remove(panel);
        }, this);
    },

    fillAlertsData: function (alerts) {
        var store = this.getAlertsStore();
        var alertRecord;
        Ext.each(alerts, function (alert) {
            alertRecord = store.findRecord('fieldId', alert.field.id);
            if (alertRecord) {
                alertRecord.set('photos', alert.images);
                alertRecord.set('comment', alert.description);
            }
        });
    },

    loadChecklist: function (data, vehicle, inspectionRecord) {
        var me = this;
        var checklists = data.checklist,
            previousAlerts = data.alerts || [],
            checklistForms = [],
            checklistAdditionalForms = [],
            choiseAdditionalFields = [];

        this.clearChecklist();

        if (Ext.isNumeric(vehicle)) {
            this.vehicleId = vehicle;
            this.vehicleRecord = null;
        } else {
            this.vehicleId = vehicle.get('id');
            this.vehicleRecord = vehicle;
        }
        this.isNew = ! inspectionRecord;
        this.inspectionRecord = inspectionRecord;

        checklists = checklists || [];


        var choiseAdditionalListeners = {
            painted: function (checkbox) {
                me.down('formpanel{config.groupId === ' + this.config.checklistGroupId + '}')
                    .isIncluded = this.config.checked;
            }
        };

        Ext.each(checklists, function (checklist, index) {
            var checklistForm = this.createForm(checklist, index);
            if (checklist.additional) {
                checklistForm.name = 'checklist-card-additional';
                checklistAdditionalForms.push(checklistForm);

                choiseAdditionalFields.push({
                    xtype: 'checkboxfield',
                    label: checklist.groupName,
                    checked: this.isNew ? false : this.fieldValueExists(checklist.fields),
                    checklistGroupId: checklist.groupId,
                    listeners: this.isNew ? {} : choiseAdditionalListeners
                });
            } else {
                checklistForm.name = 'checklist-card';
                checklistForms.push(checklistForm);
            }
        }, this);

        this.add(checklistForms);

        if (choiseAdditionalFields.length) {
            this.add(this.createChoiseAdditionalCard(choiseAdditionalFields));
        }
        this.add(checklistAdditionalForms);

        this.add(this.createReviewCard());


        var alertsData = [];
        Ext.each(previousAlerts, function (alert) {
            var message = alert.alert_description || alert.alert_message;
            if (message) {
                alertsData.push({
                    message: message
                });
            }
        });

        if (alertsData.length) {
            this.getAlertsListView(alertsData).show();
        }
    },

    createChoiseAdditionalCard: function (fields) {
        return {
            xtype: 'formpanel',
            name: 'checklist-card-choise-additional',
            cls: 'sfa-checklist-form',
            layout: {
                type: 'vbox',
                align: 'center'
            },
            items: [{
                xtype: 'fieldset',
                maxWidth: 700,
                width: '100%',
                title: 'Do you need to perform additional inspections for any of the following equipment?',
                padding: '20 0 0 0',
                defaults: {
                    labelWidth: '80%'
                },
                items: fields
            }, {
                xtype: 'toolbar',
                title: 'PRE START INSPECTION',
                docked: 'top',
                items: [{
                    text: 'Prev',
                    action: 'prev'
                }, {
                    xtype: 'spacer'
                }, {
                    text: 'Next',
                    action: 'next'
                }]
            }, {
                xtype: 'toolbar',
                docked: 'top',
                title: 'ADDITIONAL'
            }]
        };
    },

    createReviewCard: function () {
        return {
            xtype: 'formpanel',
            name: 'checklist-card-review',
            cls: 'sfa-checklist-form',
            layout: {
                type: 'vbox',
                align: 'center'
            },
            items: [{
                xtype: 'toolbar',
                title: 'PRE START INSPECTION',
                docked: 'top',
                layout: {
                    type: 'hbox',
                    align: 'stretch',
                    pack: 'center'
                },
                items: [{
                    text: 'Prev',
                    action: 'prev'
                }, {
                    xtype: 'spacer'
                }, {
                    text: 'Submit',
                    action: 'submit'
                }]
            }, {
                xtype: 'toolbar',
                docked: 'top',
                title: 'REVIEW'
            }]
        };
    },

    createForm: function (checklist, index) {
        var buttons = [{
            text: 'Back',
            hidden: true,
            action: 'back'
        }]; 
        if (index !== 0) {
            buttons.push({
                text: 'Prev',
                action: 'prev'
            });
        }
        buttons.push({
            xtype: 'spacer'
        },{
            text: 'Next',
            action: 'next'
        });
        var fields = this.createFields(checklist.fields);

        fields.push({
            xtype: 'toolbar',
            title: 'PRE START INSPECTION',
            docked: 'top',
            layout: {
                type: 'hbox',
                align: 'stretch',
                pack: 'center'
            },
            items: buttons
        }, {
            xtype: 'toolbar',
            docked: 'top',
            title: checklist.groupName.toUpperCase()
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
        var alert;
        var alertRecord;
        var additionalFieldsConfig;
        var additionalFields;

        Ext.each(fieldsData, function (fieldData) {
            alert = [];
            alertRecord = null;
            additionalFields = [];
            additionalFieldsConfig = [];
            if (fieldData.additional) {
                additionalFieldsConfig = this.createFields(fieldData.items, true);
            }
            if (Ext.isArray(fieldData.alerts) 
                    && fieldData.alerts[0] 
                    && fieldData.alerts[0].triggerValue
                ) {
                alert = fieldData.alerts[0];
                alertRecord = Ext.create('SafeStartApp.model.ChecklistAlert', {
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
                case 'label':
                    fields.push(this.createLabelField(fieldData));
                    break;
                case 'radio':
                    fields.push(this.createRadioField(fieldData, alertRecord, additionalFieldsConfig));
                    break;
                case 'datePicker':
                    fields.push(this.createDatePickerField(fieldData));
                    break;
                case 'group':
                    fields.push(this.createGroupField(fieldData));
                    break;
                case 'checkbox':
                    fields.push(this.createCheckboxField(fieldData, alertRecord, additionalFieldsConfig));
                    break;
                default: 
                    break;
            }

            Ext.each(additionalFields, function (field) {
                fields.push(field);
            });
        }, this);
        
        return fields;
    },

    createTextField: function (fieldData) {
        return {
            xtype: 'textfield',
            label: fieldData.fieldName,
            maxWidth: 420,
            width: '100%',
            value: fieldData.fieldValue,
            fieldId: fieldData.fieldId
        };
    },

    createRadioField: function (fieldData, alertRecord, additionalFieldsConfig) {
        var name = 'checklist-radio-' + fieldData.fieldId,
            optionFields = [];

        if (alertRecord && RegExp(alertRecord.get('triggerValue'), 'i').test(fieldData.fieldValue)) {
            alertRecord.set('active', true);
        }

        Ext.each(fieldData.options, function (option) {
            var fieldValue = fieldData.fieldValue || 'N/A';

            optionFields.push({
                xtype: 'radiofield',
                value: option.value,
                label: option.label,
                labelWidth: 50,
                fieldId: fieldData.fieldId,
                name: name,
                checked: new RegExp(option.value, 'i').test(fieldValue),
                listeners: {
                    check: function (radio) {
                        var value = radio.getValue();
                        radio.up('fieldset').fireEvent('checkTriggers', value);
                    }
                }
            });
        });
        return {
            xtype: 'fieldset',
            alerts: fieldData.alerts,
            additional: fieldData.additional,
            additionalFieldsConfig: additionalFieldsConfig,
            triggerValue: fieldData.triggerValue,
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
            items: optionFields,
            alertRecord: alertRecord,
            listeners: {
                checkTriggers: function (value) {
                    var alert = this.config.alertRecord,
                        additionalFields;
                    if (alert) {
                        if (RegExp(alert.get('triggerValue'), 'i').test(value)) {
                            if (alert.get('critical') && alert.get('alertMessage')) {
                                Ext.Msg.alert('DANGER', alert.get('alertMessage'));
                            }
                            alert.set('active', true);
                        } else {
                            alert.set('active', false);
                        }
                    }

                    if (this.config.additional) {
                        if (! this.hasOwnProperty('additionalFields')) {
                            this.additionalFields = [];
                            var index = this.up('component').indexOf(this);

                            Ext.each(additionalFieldsConfig, function (config) {
                                index++;
                                config.hidden = true;
                                this.additionalFields.push(this.up('component').insert(index, config));
                            }, this);
                        }
                        additionalFields = this.additionalFields;
                        if (RegExp(this.config.triggerValue, 'i').test(value)) {
                            Ext.each(additionalFields, function (field) {
                                field.show(true);
                                field.enable();
                            });
                        } else {
                            Ext.each(additionalFields, function (field) {
                                field.hide(true);
                                field.disable();
                            });
                        }
                    }
                },
                hide: function (fieldset) {
                    if (Ext.isArray(this.additionalFields)) {
                        Ext.each(this.additionalFields, function (field) {
                            field.hide();
                        });
                    }
                },
                show: function (fieldset) {
                    if (Ext.isArray(this.additionalFields)) {
                        this.fireEvent('checkTriggers', this.down('radiofield[checked]').getValue());
                    }
                }

            }
        };
    },

    createDatePickerField: function (fieldData, alertRecord) {
        var value = fieldData.fieldValue * 1000,
            field = {
                xtype: 'datepickerfield',
                maxWidth: 420,
                labelWidth: 300,
                width: '100%',
                triggerable: true,
                picker: {
                    yearFrom: 2000,
                    yearTo: new Date().getFullYear() + 10,
                    listeners: {
                        show: function (picker) {
                            var datepicker = this.down('datepickerfield{config.fieldId === ' + fieldData.fieldId + '}'),
                                datePickerValue = datepicker.getValue();

                            datepicker.originalValue = datePickerValue;
                            if (datePickerValue === null || typeof datePickerValue !== 'object') {
                                datepicker.setValue(new Date());
                            }
                        },
                        cancel: function (picker) {
                            var datepicker = this.down('datepickerfield{config.fieldId === ' + fieldData.fieldId + '}');
                            if (datepicker.originalValue === null) {
                                datepicker.reset();
                                picker.setValue({
                                    year: null,
                                    day: null,
                                    month: null
                                });
                            }
                        },
                        scope: this
                    }
                },
                dateFormat: SafeStartApp.dateFormat,
                label: fieldData.fieldName,
                fieldId: fieldData.fieldId,
                defaultValue: fieldData.defaultValue
            };
        if (value) {
            field.value = new Date(value);
        } else {
            field.value = null;
        }
        return field;
    },

    createCheckboxField: function (fieldData, alertRecord, additionalFieldsConfig) {
        if (alertRecord && RegExp(alertRecord.get('triggerValue'), 'i').test(fieldData.fieldValue)) {
            alertRecord.set('active', true);
        }
        return {
            xtype: 'checkboxfield',
            maxWidth: 900,
            additional: fieldData.additional,
            width: '100%',
            label: fieldData.fieldName,
            labelWidth: '90%',
            triggerValue: fieldData.triggerValue,
            fieldId: fieldData.fieldId,
            alerts: fieldData.alerts,
            alertRecord: alertRecord,
            checked: fieldData.fieldValue ? new RegExp(fieldData.fieldValue, 'i').test('yes') : false,
            triggerable: true,
            listeners: {
                check: function (checkbox) {
                    this.fireEvent('checkTriggers', 'Yes');
                },
                uncheck: function (checkbox) {
                    this.fireEvent('checkTriggers', 'No');
                },
                checkTriggers: function (value) {
                    var alert = this.config.alertRecord,
                        additionalFields;
                    if (alert) {
                        if (RegExp(alert.get('triggerValue', 'i').test(value))) {
                            if (alert.get('critical')) {
                                Ext.Msg.alert('DANGER', alert.get('alertMessage'));
                            }
                            alert.set('active', true);
                        } else {
                            alert.set('active', false);
                        }
                    }

                    if (this.config.additional) {
                        if (! this.hasOwnProperty('additionalFields')) {
                            this.additionalFields = [];
                            var index = this.up('component').indexOf(this);

                            Ext.each(additionalFieldsConfig, function (config) {
                                index++;
                                config.hidden = true;
                                this.additionalFields.push(this.up('component').insert(index, config));
                            }, this);
                        }
                        additionalFields = this.additionalFields;
                        if (RegExp(this.config.triggerValue, 'i').test(value)) {
                            Ext.each(additionalFields, function (field) {
                                field.show(true);
                                field.enable();
                            });
                        } else {
                            Ext.each(additionalFields, function (field) {
                                field.hide(true);
                                field.disable();
                            });
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
        reviewCard.add(this.createAdditionalFields());
        reviewCard.add(this.createAlertsView(alerts));
    },

    createAdditionalFields: function () {
        var odometerKms = 1000,
            odometerHours = 0;

        if (this.vehicleRecord) {
            odometerKms = this.vehicleRecord.get('currentOdometerKms');
            odometerHours = this.vehicleRecord.get('currentOdometerHours');
        } else if (! this.isNew && this.inspectionRecord) {
            odometerKms = this.inspectionRecord.get('odometerKms');
            odometerHours = this.inspectionRecord.get('odometerHours');
        }
        return [{
            xtype: 'container',
            width: '100%',
            cls: 'sfa-vehicle-inspection-gps',
            maxWidth: 520,
            items: [{
                xtype: 'togglefield',
                label: 'GPS',
                labelWidth: 50,
                listeners: {
                    change: function(field, slider, thumb, newValue, oldValue) {
                        var container = field.up('container[cls=sfa-vehicle-inspection-gps]');
                        if (newValue) {
                            if (!container.gps) {
                                container.gps = Ext.create('Ext.util.Geolocation', {
                                    autoUpdate: false
                                });
                                container.gps.updateLocation();
                            }
                        }
                    }
                }
            }]
        }, {
            xtype: 'container',
            name: 'vehicle-inspection-additional-fields',
            width: '100%',
            maxWidth: 520,
            height: 'auto',
            items: [{
                xtype: 'fieldset',
                title: 'Current odometer',
                layout: {
                    type: 'vbox'
                },
                width: '100%',
                items: [{
                    xtype: 'numberfield',
                    name: 'current-odometer-kms',
                    label: 'Kilometers',
                    required: true,
                    value: odometerKms,
                    minValue: 0
                }, {
                    xtype: 'numberfield',
                    name: 'current-odometer-hours',
                    label: 'Hours',
                    value: odometerHours,
                    minValue: 0
                }]
            }]
        }];
    },
    createVehicleDetailsView: function (passedCards) {
        var data = [];
        Ext.each(passedCards, function (card) {
            data.push({
                title: card.groupName,
                checklist: card.checklist,
                cls: card.alert ? 'alerts' : 'ok'
            });
        });
        var items = [{
            xtype: 'titlebar',
            title: 'Vehicle details'
        }, {
            xtype: 'dataview',
            scrollable: false,
            height: data.length * 27,
            itemTpl: [
                '<div class="checklist-details-{cls}">',
                '{title}',
                '</div>'
            ].join(''),
            store: {
                fields: ['title', 'cls'],
                data: data
            },
            listeners: {
                itemtap: function (view, index, el, record) {
                    var checklist = record.raw.checklist;

                    var back = checklist.down('button[action=back]');
                    if (back) {
                        back.show();
                    }

                    var next = checklist.down('button[action=next]');
                    if (next) {
                        next.hide();
                    }

                    var prev = checklist.down('button[action=prev]');
                    if (prev) {
                        prev.hide();
                    }
                    this.setActiveItem(record.raw.checklist);
                },
                scope: this
            }
        }];

        return {
            xtype: 'fieldset',
            width: '100%',
            maxWidth: 520,
            items: items
        };
    },

    createAlertPanel: function (alert) {
        return {
            xtype: 'panel',
            width: '100%',
            maxWidth: 520,
            name: 'alert-container',
            margin: '5 0 10 0',
            cls: 'sfa-alert-panel',
            fieldId: alert.get('fieldId'),
            alertModel: alert,
            items: [{
                xtype: 'container',
                cls: 'checklist-alert-description-critical',
                html: [
                    alert.get('alertDescription'),
                    '<div class="checklist-alert-icon"></div>'
                ].join('')
            }, {
                xtype: 'textfield',
                label: 'Additional comments',
                padding: '10 0 0 0',
                value: alert.get('comment'),
                listeners: {
                    change: function (textfield, value) {
                        alert.set('comment', value);
                    }
                }
            }, this.createImageUploadPanel(alert)
            ]
        };
    },

    createAlertsView: function (alerts) {
        var criticalAlerts = [{
                xtype: 'container',
                cls: 'sfa-alerts-toolbar',
                margin: '10 0',
                height: 40,
                html: [
                    '<div style="position: absolute; left: 0;" class="sfa-alerts-toolbar-title">Alerts</div>',
                    '<div style="position: absolute; right: 0;" class="sfa-alerts-toolbar-description">Critical</div>'
                ].join('')
            }],
            nonCriticalAlerts = [{
                xtype: 'container',
                cls: 'sfa-alerts-toolbar',
                margin: '10 0',
                height: 40,
                html: [
                    '<div class="sfa-alerts-toolbar-title">Alerts</div>',
                    '<div class="sfa-alerts-toolbar-description">Non-Critical</div>'
                ].join('')
            }];

        Ext.each(alerts, function (alert) {
            if (alert.get('critical')) {
                criticalAlerts.push(this.createAlertPanel(alert));
            } else {
                nonCriticalAlerts.push(this.createAlertPanel(alert));
            }
        }, this);

        var items = [{
            xtype: 'panel',
            width: '100%',
            maxWidth: 520,
            hidden: (criticalAlerts.length <= 1),
            items: criticalAlerts
        }, {
            xtype: 'panel',
            width: '100%',
            maxWidth: 520,
            hidden: (nonCriticalAlerts.length <= 1),
            items: nonCriticalAlerts
        }];

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
                    padding: '5 10',
                    cls: 'sfa-add-button',
                    states: {
                        browse: {
                            ui: 'action',
                            padding: 0,
                            margin: 0,
                            text: 'Add photo'
                        },

                        uploading: {
                            ui: 'action',
                            margin: 0,
                            padding: '0 25 0 0',
                            text: 'Loading ...',
                            loading: false
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
    },

    getAlertsListView: function (alerts) {
        return Ext.MessageBox.create({
            layout: 'fit',
            title: 'Outstanding Alerts:',
            scrollable: true,
            listeners: {
                resize: function (view) {
                    var maxHeight = this.element.dom.offsetHeight || 320;
                    var height = alerts.length * 16 + 130;

                    view.setHeight(height < maxHeight ? height : maxHeight);
                },
                scope: this
            },
            items: [{
                xtype: 'panel',
                scroll: 'vertical',
                tpl: new Ext.XTemplate(
                    '<div class="sfa-alerts">',
                    '<tpl for=".">',
                        '<div class="sfa-alert-description">',
                        '{message}',
                        '</div>',
                    '</tpl>',
                    '</div>'
                ),
                data: alerts
            }],
            buttons: [{
                text: 'OK',
                ui: 'action',
                handler: function () {
                    this.up('sheet').destroy();
                }
            }]
        });
    },

    createLabelField: function (fieldData) {
        return {
            xtype: 'container',
            style: {
                fontSize: '20px'
            },
            cls: 'sfa-field-label',
            html: fieldData.fieldName
        };
    }

});
