Ext.define('SafeStartExt.view.panel.Inspection', {
    extend: 'Ext.panel.Panel',
    requires: [
        'Ext.form.field.Radio',
        'Ext.form.field.File',
        'Ext.form.field.Checkbox',
        'Ext.form.field.Date',
        'SafeStartExt.store.InspectionChecklists'
    ],
    xtype: 'SafeStartExtPanelInspection',
    configData: {
        autoCreateInspection: true
    },
    layout: {
        type: 'hbox',
        align: 'strech'
    },
    statics: {
        CHECKLIST_FORM: 1,
        CHECKLIST_ADDITIONAL_FORM: 2,
        REVIEW_FORM: 3,
        CHOISE_ADDITIONAL_FORM: 4
    },
    current: -1,
    forms: {},

    initComponent: function () {
        Ext.apply(this, {
            items: [{
                xtype: 'dataview',
                width: 250,
                height: '100%',
                cls: 'sfa-vehicle-inspection-checklist',
                itemSelector: 'div',
                tpl: new Ext.XTemplate(
                    '<tpl for=".">',
                    '<div class="sfa-review-details-item ',
                    '<tpl if="enabled == false">sfa-review-details-item-disabled</tpl> ',
                    '<tpl if="completed == true">sfa-review-details-item-completed</tpl> ',
                    '<tpl if="alerts.length != 0">sfa-review-details-item-alerts</tpl> ',
                    '" style="padding: 10px; font-size: 20px;">',
                    '{groupName}',
                    '</div>',
                    '</tpl>'
                ),
                store: {
                    proxy: {
                        type: 'memory'
                    },
                    fields: [
                        'groupName', 
                        'view', 
                        'checklist', 
                        'checklistPages', 
                        'enabled', 
                        'type', 
                        'alerts', 
                        'completed'
                    ],
                    data: []
                },
                listeners: {
                    itemclick: function (dataview, record) {
                        this.onChecklistClick(record);
                    },
                    scope: this
                }
            }, {
                xtype: 'container',
                autoScroll: true,
                padding: 10,
                height: '100%',
                name: 'checklists-container',
                flex: 1,
                cls: 'sfa-checklists-container',
                layout: 'card'
            }]
        });
        this.callParent();
    },

    createInspection: function (checklists, inspectionId, prevAlerts) {
        var listStore = this.down('dataview').getStore();
        
        this.checklists = checklists;
        this.current = -1;
        this.forms = [];
        this.listStore = listStore;
        this.inspectionId = inspectionId;
        this.isNew = !! inspectionId;

        listStore.removeAll();

        checklists.query('additional', false).each(function (checklist) {
            listStore.add({
                type: this.self.CHECKLIST_FORM,
                view: null,
                checklist: checklist,
                enabled: true,
                alerts: [],
                groupName: checklist.get('groupName'),
                form: this.forms[this.forms.length-1]
            });
        }, this);
        listStore.first().set('isFirst', true);
        var checklistPages = [];
        var additional = listStore.add({
            type: this.self.CHOISE_ADDITIONAL_FORM,
            groupName: 'Additional',
            alerts: [],
            enabled: true,
            checklistPages: [],
            view: null
        })[0];

        checklists.query('additional', true).each(function (checklist) {
            var page = listStore.add({
                groupName: checklist.get('groupName'),
                view: null,
                enabled: false,
                alerts: [],
                checklist: checklist,
                type: this.self.CHECKLIST_ADDITIONAL_FORM
            })[0];
            checklistPages.push(page);
        }, this);

        additional.set('checklistPages', checklistPages);

        listStore.add({
            type: this.self.REVIEW_FORM,
            alerts: [],
            view: null,
            enabled: true,
            groupName: 'Review'
        });

        this.showPreviousAlerts(prevAlerts);

        this.onNextClick();
    },

    editInspection: function (data, inspectionId) {
        var checklists = SafeStartExt.store.InspectionChecklists.create({data: data.checklist});
        this.createInspection(checklists, inspectionId, data.alerts);
    },

    showPreviousAlerts: function (alerts) {
        if (! (Ext.isArray(alerts) && alerts.length)) {
            return;
        }
        var messageBox = Ext.create('Ext.window.Window', {
            title: 'Outstanding alerts',
            padding: 10,
            width: 300,
            items: [{
                xtype: 'dataview',
                width: '100%',
                store: {
                    proxy: 'memory',
                    fields: ['alert_description']
                },
                itemSelector: 'div.sfa-previous-alert-item',
                tpl: [
                    '<tpl for=".">',
                    '<div class="sfa-previous-alert-item" style="color: #F00; font-size: 16px;"> {alert_description} </div>',
                    '</tpl>'
                ].join(''),
                data: alerts
            }],
            bbar: {
                xtype: 'toolbar', 
                buttonAlign: 'center',
                layout: {
                    type: 'hbox',
                    pack: 'center'
                },
                items: [{
                    text: 'OK',
                    handler: function () {
                        this.up('window').close();
                    }
                }]
            }
        });
        this.add(messageBox);
        messageBox.show();
    },

    onLeave: function (form) {
        if (form) {
            form.set('completed', true);

            var alerts = [];
            if (form.get('type') === this.self.CHECKLIST_FORM
                || (form.get('type') === this.self.CHECKLIST_ADDITIONAL_FORM && form.get('enabled') === true)
                ) {
                if (form.get('view') === null) {
                    return;
                }
                var triggerableFields = form.get('view').query('fieldcontainer{alert !== false}');
                Ext.each(triggerableFields, function (field) {
                    if (field.alert.get('active')) {
                        alerts.push(field.alert);
                    }
                }, this);
            }
            form.set('alerts', alerts);
        }
    },

    onNextClick: function (leave) {
        var form;
        if (leave) {
            this.onLeave(this.listStore.getAt(this.current));
        }

        this.current++;
        form = this.listStore.getAt(this.current);

        if (form) {
            if (form.get('type') === this.self.CHECKLIST_ADDITIONAL_FORM && ! form.get('enabled')) {
                this.onNextClick(false);
                return;
            }
            this.goToForm(form);
        } 
    },

    onPrevClick: function (leave) {
        var form;
        if (leave) {
            this.onLeave(this.listStore.getAt(this.current));
        }
        this.current--;
        form = this.listStore.getAt(this.current);

        if (form) {
            if (form.get('type') === this.self.CHECKLIST_ADDITIONAL_FORM && ! form.get('enabled')) {
                this.onPrevClick(false);
                return;
            }
            this.goToForm(form);
        } 
    },

    onChecklistClick: function (form) {
        this.onLeave(this.listStore.getAt(this.current));
        this.current = this.listStore.indexOf(form);
        this.goToForm(form);
    },

    onReviewClick: function (form) {
        this.goToForm(form);
    },

    onSubmitClick: function (form) {
        var me = this,
            message = this.validateReviewForm();

        if (message !== undefined) {
            this.up('SafeStartExtPanelVehicleTabs').confirm.display({
                msg: message,
                onConfirm: function () {
                    me.up('SafeStartExtPanelVehicleTabs').confirm.display({
                        msg: 'Please confirm your submission',
                        onConfirm: Ext.Function.bind(me.onConfirm, me)
                    });

                } 
            });
            return;
        }

        this.up('SafeStartExtPanelVehicleTabs').confirm.display({
            msg: 'Please confirm your submission',
            onConfirm: Ext.Function.bind(this.onConfirm, this)
        });
    },

    validateReviewForm: function () {
        var vehicle = this.vehicle,
            reviewForm = this.listStore.findRecord('type', this.self.REVIEW_FORM),
            odometerHoursInterval,
            inspectionDueHours,
            inspectionDueKms,
            inspectionInterval,
            lastInspectionDate,
            currentOdometerKms,
            currentOdometerHours,
            odometerKms = reviewForm.get('view').down('numberfield[name=currentOdometerKms]').getValue(),
            odometerHours = reviewForm.get('view').down('numberfield[name=currentOdometerHours]').getValue(),
            intervals;

        if (this.isNew) {
            currentOdometerHours = parseInt(vehicle.get('currentOdometerHours'), 10);
            currentOdometerKms = parseInt(vehicle.get('currentOdometerKms'), 10);

            if (odometerKms == currentOdometerKms && odometerHours == currentOdometerHours) {
                return 'Current odometer should be changed';
            }

            lastInspectionDate = vehicle.get('lastInspectionDay');
            odometerHoursInterval = odometerHours - currentOdometerHours;
            inspectionDueHours = vehicle.get('inspectionDueHours');
            inspectionDueKms = vehicle.get('inspectionDueKms');
            if (lastInspectionDate) {
                inspectionInterval = (new Date().getTime() - lastInspectionDate) / 60 / 60 / 1000;
                if (inspectionInterval < odometerHoursInterval) {
                    return 'Please make sure the data is correct';
                }
                intervals = (inspectionInterval / inspectionDueHours);
            } else {
                intervals = 1;
            }

            if (intervals * inspectionDueKms < odometerKms) {
                return 'Please make sure the data is correct';
            }

            if (odometerKms < currentOdometerKms || odometerHours < currentOdometerHours) {
                return 'Please make sure the data is correct';
            }
        }
    },

    onConfirm: function (form) {
        var inspectionId = this.inspectionId || 0;
        this.up('SafeStartExtPanelVehicleTabs').confirm.close();
        this.fireEvent('completeInspectionAction', this.getValues(), inspectionId);
    },

    getValues: function () {
        return {
            date: parseInt(Date.now()/1000, 10),
            odometer: this.getOdometerKmsValue(),
            odometer_hours: this.getOdometerHoursValue(),
            fields: this.getFieldsValue(),
            alerts: this.getAlertsValue(),
            gps: this.getGpsValue()
        };
    },

    getOdometerHoursValue: function () {
        return this.down('numberfield[name=currentOdometerHours]').getValue();
    },

    getOdometerKmsValue: function () {
        return this.down('numberfield[name=currentOdometerKms]').getValue();
    }, 

    getFieldsValue: function () {
        var completedForms = [];
        var fields = [];

        this.listStore.each(function (form) {
            if (form.get('type') === this.self.CHECKLIST_FORM
                || (form.get('type') === this.self.CHECKLIST_ADDITIONAL_FORM && form.get('enabled') === true)
            ) {
                completedForms.push(form);
                fields = fields.concat(this.getFieldValuesByParent(form.get('checklist'), form));
            }
        }, this);
        return fields;
    },

    getFieldValuesByParent: function (group, form) {
        var fields = [];
        group.items().each(function (field) {
            var value;
            if (field.get('type') == 'group') {
                fields = fields.concat(this.getFieldValuesByParent(field, form));
            } else {
                value = this.getFieldValue(field, form.get('view'));
                if (value !== undefined) {
                    fields.push({
                        id: field.get('id'),
                        value: value
                    });
                }
            }
        }, this);
        return fields;
    },

    getFieldValue: function (field, view) {
        var el, 
            value;

        view = view || this;

        el = view.down('component[fieldId=' + field.get('id') + ']');

        if (! el) {
            return this.getDefaultFieldValue(field);
        }

        switch(field.get('type')) {
            case 'text':
                value = el.getValue();
                break;
            case 'checkbox':
            case 'radio':
                var checkedOption = el.down('radio[checked]');
                if (checkedOption) {
                    value = checkedOption.inputValue;
                } else {
                    value = 'n/a';
                }
                break;
            case 'datePicker':
                var date = el.getValue();
                if (date) {
                    value = parseInt(date.getTime()/1000, 10);
                } else {
                    value = null;
                }
                break;
            case 'group':
                break;
            default: 
                break;
        }

        return value;
    },

    getDefaultFieldValue: function (field) {
        return field.get('fieldValue');
    },

    getAlertsValue: function () {
        var alerts = [], 
            reviewForm = this.listStore.findRecord('type', this.self.REVIEW_FORM);

        if (Ext.isArray(reviewForm.alerts)) {
            Ext.each(reviewForm.alerts, function (alert) {
                alerts.push({
                    fieldId: alert.getField().get('fieldId'), 
                    comment: alert.get('comment'),
                    images: alert.get('images')
                });
            });
        }

        return alerts;
    },

    getGpsValue: function () {
        return this.down('hiddenfield[name=geolocation]').getValue();
    },

    goToForm: function (form) {
        if (! form.get('view')) {
            this.createForm(form);
        } else {
            this.updateForm(form);
        }

        if (form.get('type') === this.self.CHECKLIST_ADDITIONAL_FORM && ! form.get('enabled')) {
            form.set('enabled', true);
        }
        this.down('dataview').select(form);

        this.getChecklistsContainer().getLayout().setActiveItem(form.get('view'));
    },

    createForm: function (form) {
        switch(form.get('type')) {
            case this.self.CHECKLIST_FORM:
                this.createChecklistForm(form);
                break;
            case this.self.CHECKLIST_ADDITIONAL_FORM:
                this.createChecklistForm(form);
                break;
            case this.self.REVIEW_FORM:
                this.createReviewForm(form);
                break;
            case this.self.CHOISE_ADDITIONAL_FORM:
                this.createChoiseAdditionalForm(form);
                break;
        }
        return form;
    },

    updateForm: function (form) {
        switch(form.get('type')) {
            case this.self.REVIEW_FORM:
                this.updateReviewForm(form);
                break;
        }
        return form;
    },

    getChecklistsContainer: function () {
        return this.down('container[name=checklists-container]');
    },

    createChecklistForm: function (form) {
        var leftBtns = [],
            rightBtns = [];

        rightBtns.push({
            xtype: 'button',
            action: 'next',
            text: 'Next',
            scale: 'small',
            handler: this.onNextClick,
            scope: this
        }, {
            xtype: 'button',
            action: 'review',
            text: 'Review',
            scale: 'small',
            hidden: true,
            handler: function () {
                var reviewForm = this.listStore.findRecord('type', this.self.REVIEW_FORM);
                this.goToForm(reviewForm);
            },
            scope: this
        });

        if (! form.get('isFirst')) {
            leftBtns.unshift({
                xtype: 'button',
                text: 'Prev',
                action: 'prev',
                scale: 'small',
                handler: this.onPrevClick,
                scope: this
            });
        }

        form.set('view', this.getChecklistsContainer().add({
            xtype: 'form',
            maxWidth: 600,
            overflowY: 'auto',
            checklist: form.checklist,
            layout: {
                type: 'vbox',
                align: 'center'
            },
            items: this.createChecklistFields(form.get('checklist').items(), form),
            tbar: [{
                xtype: 'container',
                width: '100%',
                layout: 'hbox',
                items: [{
                    xtype: 'container',
                    layout: {
                        type: 'hbox',
                        align: 'middle'
                    },
                    minWidth: 100,
                    items: leftBtns
                }, {
                    xtype: 'container',
                    flex: 1,
                    style: {
                        textAlign: 'center',
                        fontSize: '18px'
                    },
                    cls:'sfa-group-title',
                    html: 'PRE START INSPECTION - ' + form.get('groupName').toUpperCase()
                }, {
                    xtype: 'container',
                    layout: {
                        type: 'hbox',
                        align: 'middle',
                        pack: 'end'
                    },
                    minWidth: 100,
                    items: rightBtns
                }]
            }]
        }));
    },

    createChecklistFields: function (fields, form) {
        var formFields = [];

        fields.each(function (field) {
            switch(field.get('type')) {
                case 'text':
                    formFields = formFields.concat(this.createTextField(field));
                    break;
                case 'radio':
                    formFields = formFields.concat(this.createRadioField(field, form));
                    break;
                case 'datePicker':
                    formFields = formFields.concat(this.createDatePickerField(field));
                    break;
                case 'group':
                    formFields = formFields.concat(this.createGroupField(field, form));
                    break;
                case 'checkbox':
                    formFields = formFields.concat(this.createRadioField(field));
                    break;
                default: 
                    break;
            }
        }, this);
        
        return formFields;

    },

    createTextField: function (field) {
        return {
            xtype: 'textfield',
            width: 300,
            labelWidth: 70,
            field: field,
            fieldId: field.get('fieldId'),
            fieldLabel: field.get('fieldName'),
            value: field.get('fieldValue') || field.get('defaultValue')
        };
    },

    createRadioField: function (field, form) {
        var me = this,
            alert = false,
            formAdditionalFields = [],
            options = [],
            listeners = {},
            additionalFields;

        if (field.get('additional')) {
            additionalFields = field.items();
            formAdditionalFields = this.createChecklistFields(additionalFields, form);
            Ext.each(formAdditionalFields, function (field) {
                field.hidden = true;
                field.disabled = true;
            });
            listeners.checkAdditional = function (value) {
                if (RegExp(this.field.get('triggerValue'), 'i').test(value)) {
                    additionalFields.each(function (field) {
                        var formField = me.down('component[fieldId=' + field.get('id') + ']');
                        if (formField) {
                            formField.show(true);
                            formField.enable();
                        }
                    });
                } else {
                    additionalFields.each(function (field) {
                        var formField = me.down('component[fieldId=' + field.get('id') + ']');
                        if (formField) {
                            formField.hide(true);
                            formField.disable();
                        }
                    });
                }

            };
            listeners.hide = function () {
                additionalFields.each(function (field) {
                    var formField = me.down('component[fieldId=' + field.get('id') + ']');
                    formField.hide(true);
                    formField.disable();
                });
            };
            listeners.show = function () {
                additionalFields.each(function (field) {
                    this.fireEvent('checkAdditional', this.down('radio[checked]').inputValue);
                }, this);
            };
            listeners.afterrender = function () {
                var value = this.down('radio[checked]').inputValue;
                this.fireEvent('checkAdditional', value);
                this.fireEvent('checkAlert', value);
            };
        }

        if (field.alerts().getCount() && field.alerts().first().get('triggerValue')) {
            alert = field.alerts().first();
            if (RegExp(alert.get('triggerValue'), 'i').test(field.get('fieldValue'))) {
                alert.set('active', true);
                var formAlerts = form.get('alerts');
                formAlerts.push(alert);
            }
            // alert.set('fieldId', field.get('fieldId'));
            listeners.checkAlert = function (value) {
                if (RegExp(alert.get('triggerValue'), 'i').test(value)) {
                    alert.set('active', true);
                    if (alert.get('critical') && alert.get('alertMessage')) {
                        Ext.Msg.alert('DANGER', alert.get('alertMessage'));
                    }
                } else {
                    alert.set('active', false);
                }
            };
        }
        Ext.each(field.raw.options, function (option) {
            options.push({
                boxLabel: option.label,
                inputValue: option.value,
                checked: new RegExp(option.value).test(field.get('fieldValue'))
            });
        }, this);
        return [{
            xtype: 'container',
            layout: {
                type: 'vbox',
                align: 'center'
            },
            items: [{
                xtype: 'container',
                style: {
                    textAlign: 'center'
                },
                html: field.get('fieldName')
            }, {
                xtype: 'fieldcontainer',
                alert: alert,
                fieldId: field.get('id'),
                field: field,
                layout: 'hbox',
                defaults: {
                    xtype: 'radio',
                    name: 'sfa-checklist-radio-' + field.get('id'),
                    listeners: {
                        change: function (combo, checked) {
                            if (checked) {
                                var formField = this.up('fieldcontainer[fieldId=' + field.get('id') + ']');
                                formField.fireEvent('checkAdditional', combo.inputValue);
                                formField.fireEvent('checkAlert', combo.inputValue);
                            }
                        }
                    }
                },
                items: options,
                listeners: listeners
            }]
            // fieldLabel: field.get('fieldName'),
            // items: options,
        }].concat(formAdditionalFields);
    },

    createDatePickerField: function (field) {
        return {
            xtype: 'datefield',
            value: field.get('fieldValue') || '',
            field: field,
            fieldId: field.get('fieldId'),
            labelWidth: 300,
            cls: 'sfa-datepicker',
            fieldLabel: field.get('fieldName')
        };
    },

    createCheckboxField: function (field) {
        return {
            xtype: 'checkbox',
            field: field,
            fieldId: field.get('fieldId'),
            fieldLabel: field.get('fieldName')
        };
    },

    createGroupField: function (field, form) {
        return {
            xtype: 'container',
            layout: {
                type: 'vbox',
                align: 'center'
            },
            items: [{
                xtype: 'container',
                style: {
                    textAlign: 'center'
                },
                html: field.get('fieldName')
            }, {
                xtype: 'container',
                cls: 'sfa-group-container',
                layout: {
                    type: 'vbox',
                    align: 'center'
                },
                items: this.createChecklistFields(field.items(), form)
            }]
        };
    },

    createChoiseAdditionalForm: function (form) {
        var checkboxes = [];
        Ext.each(form.get('checklistPages'), function (checklistPage) {
            checkboxes.push({
                fieldLabel: checklistPage.get('checklist').get('groupName'),
                listeners: {
                    change: function (checkbox, value) {
                        checklistPage.set('enabled', value);
                    }
                }
            });
        });
        form.set('view', this.getChecklistsContainer().add({
            xtype: 'form',
            maxWidth: 600,
            cls:'sfa-additional',
            defaultType: 'checkboxfield',
            layout: {
                type: 'vbox',
                align: 'center'
            },
            items: checkboxes,
            tbar: [{
                xtype: 'container',
                width: '100%',
                layout: 'hbox',
                items: [{
                    xtype: 'container',
                    layout: {
                        type: 'hbox',
                        align: 'middle'
                    },
                    minWidth: 100,
                    items: [{
                        xtype: 'button',
                        action: 'prev',
                        text: 'Prev',
                        handler: this.onPrevClick,
                        scope: this
                    }]
                }, {
                    xtype: 'container',
                    flex: 1,
                    style: {
                        textAlign: 'center',
                        fontSize: '18px'
                    },
                    cls:'sfa-group-title',
                    html: 'PRE START INSPECTION - ADDITIONAL' 
                }, {
                    xtype: 'container',
                    layout: {
                        type: 'hbox',
                        align: 'middle',
                        pack: 'end'
                    },
                    minWidth: 100,
                    items: [{
                        xtype: 'button',
                        action: 'prev',
                        text: 'Next',
                        handler: this.onNextClick,
                        scope: this
                    }]
                }]
            }]
        }));
    },

    createReviewForm: function (form) {
        var items = [],
            criticalAlerts = [],
            alerts = [];

        form.alerts = [];

        if (navigator && navigator.geolocation) {
            items.push({
                xtype: 'checkboxfield',
                fieldLabel: 'GPS',
                listeners: {
                    change: function (checkbox, value) {
                        if (value) {
                            this.up('form').down('hiddenfield[name=geolocation]').enable(); 
                            if (! this.checkedGeo) {
                                navigator.geolocation.getCurrentPosition(Ext.Function.bind(this.changePosition, this));
                                this.checkedGeo = true;
                            }
                        } else {
                            this.up('form').down('hiddenfield[name=geolocation]').disable(); 
                        }
                    }
                },
                changePosition: function (position) {
                    this.up('form').down('hiddenfield[name=geolocation]').setValue(position.coords.latitude + ';' + position.coords.longitude);
                }
            }, {
                xtype: 'hiddenfield',
                name: 'geolocation'
            });
        }

        items.push({
            xtype: 'fieldcontainer',
            fieldLabel: 'Current odometer',
            labelAlign: 'top',
            items: [{
                xtype: 'numberfield',
                fieldLabel: 'Kms',
                minValue: 0,
                maxValue: 100000000,
                name: 'currentOdometerKms',
                value: this.vehicle.get('currentOdometerKms')
            }, {
                xtype: 'numberfield',
                fieldLabel: 'Hours',
                minValue: 0,
                maxValue: 100000000,
                name: 'currentOdometerHours',
                value: this.vehicle.get('currentOdometerHours')
            }]
        });

        this.listStore.each(function (card) {
            if (card.get('type') !== this.self.CHECKLIST_FORM &&
                (card.get('type') === this.self.CHECKLIST_ADDITIONAL_FORM && card.get('enabled') === false)
            ) {
                return;
            }
            Ext.each(card.get('alerts'), function (alert) {
                if (alert.get('critical')) {
                    criticalAlerts.push(alert);
                } else {
                    alerts.push(alert);
                }
                form.alerts.push(alert);
            });
        }, this);

        if (criticalAlerts.length) {
            items.push({
                xtype: 'container',
                width: '100%',
                flex: 1,
                layout: 'vbox',
                items: [{
                    xtype: 'container',
                    width: '100%',
                    html: [ 
                        '<div class="sfa-alerts-container">',
                            '<div class="sfa-alerts-container-title">',
                                'Alerts',
                            '</div>',
                            '<div class="sfa-alerts-container-title-desc">',
                                'Critical',
                            '</div>',
                        '</div>'
                    ].join('')
                }, {
                    xtype: 'container',
                    items: this.createAlertsView(criticalAlerts)
                }]
            });
        }
        if (alerts.length) {
            items.push({
                xtype: 'container',
                width: '100%',
                layout: 'vbox',
                items: [{
                    xtype: 'container',
                    width: '100%',
                    html: [ 
                        '<div class="sfa-alerts-container">',
                            '<div class="sfa-alerts-container-title">',
                                'Alerts',
                            '</div>',
                            '<div class="sfa-alerts-container-title-desc">',
                                'Non-Critical',
                            '</div>',
                        '</div>'
                    ].join('')
                }, {
                    xtype: 'container',
                    items: this.createAlertsView(alerts)
                }]
            });
        }

        form.set('view', this.getChecklistsContainer().add({
            xtype: 'form',
            maxWidth: 600,            
            cls: 'form-scrollable sfa-pre-start-inspection-review',
            width: '100%',
            items: items,
            tbar: [{
                xtype: 'container',
                width: '100%',
                layout: 'hbox',
                items: [{
                    xtype: 'container',
                    layout: {
                        type: 'hbox',
                        align: 'middle'
                    },
                    minWidth: 100,
                    items: [{
                        xtype: 'button',
                        action: 'prev',
                        text: 'Prev',
                        handler: this.onPrevClick,
                        scope: this
                    }]
                }, {
                    xtype: 'container',
                    flex: 1,
                    style: {
                        textAlign: 'center'
                    },
                    cls:'sfa-group-title',
                    html: 'PRE START INSPECTION - REVIEW' 
                }, {
                    xtype: 'container',
                    layout: {
                        type: 'hbox',
                        align: 'middle',
                        pack: 'end'
                    },
                    minWidth: 100,
                    items: [{
                        xtype: 'button',
                        action: 'submit',
                        text: 'Submit',
                        handler: function(btn) {
                            this.onSubmitClick();
                        },
                        scope: this
                    }]
                }]
            }]
        }));
    },

    updateReviewForm: function (form) {
        form.get('view').destroy();
        this.createReviewForm(form);
    },

    createImageUploadView: function (alert) {
        var images = [],
            items =[];
        if (! Ext.isArray(alert.get('images'))) {
            alert.set('images', images);
        } else {
            images = alert.get('images');
        }
        items = [];
        Ext.each(images, function (image) {
            items.push({
                xtype: 'image', 
                height: 70,
                margin: 10,
                width: 70,
                src: '/api/image/' + image + '/70x70'
            });
        });
        return {
            xtype: 'form',
            width: '100%',
            name: 'image-container',
            maxWidth: 400,
            cls:'sfa-upload-image',
            items: items.concat([{
                xtype: 'filefield',
                buttonOnly: true,
                buttonConfig: {
                    scale: 'small'
                },
                buttonText: 'Upload Image',
                ui: 'default',
                msgTarget: 'side',
                name: 'image',
                listeners: {
                    change: function () {
                        var form = this.up('form');
                        if (form.isValid()) {
                            form.submit({
                                url: '/api/upload-images',
                                waitMsg: 'Uploading your photos...',
                                success: function (fp, o) {
                                },
                                failure: function (fp, o) {
                                    var data = Ext.decode(o.response.responseText);
                                    var index = form.items.indexOf(form.down('filefield'));
                                    images.push(data.data.hash);
                                    form.insert(index || 0, {
                                        xtype: 'image', 
                                        height: 70,
                                        margin: 10,
                                        width: 70,
                                        src: '/api/image/' + data.data.hash + '/70x70'
                                    });
                                }
                            });
                        }
                    }
                }
            }])
        };
    },

    createAlertsView: function (alerts) {
        var items = [];
        Ext.each(alerts, function (alert) {
            items.push({
                xtype: 'container',
                items: [{
                    xtype: 'container',
                    html: [ 
                        '<div class="sfa-alert-title">',
                            alert.get('alertDescription'),
                        '</div>'
                    ].join('')
                }, {
                    xtype: 'textfield',
                    fieldLabel: 'Comment',
                    value: alert.get('comment'),
                    listeners: {
                        change: function (textfield, value) {
                            alert.set('comment', value);
                        }
                    }
                }, this.createImageUploadView(alert)
                ]
            });
        }, this);
        return items;
    },

    getAlertsStore: function () {
        return this.alertsStore;
    },

    getChecklists: function () {
        return this.checklists;
    }
});
