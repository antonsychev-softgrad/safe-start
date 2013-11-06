Ext.define('SafeStartExt.view.panel.VehicleInspection', {
    extend: 'Ext.panel.Panel',
    requires: [
        'Ext.form.field.Radio',
        'Ext.form.field.Checkbox',
        'Ext.form.field.Date',
        'SafeStartExt.store.InspectionChecklists'
    ],
    xtype: 'SafeStartExtPanelVehicleInspection',
    layout: {
        type: 'card'
    },

    statics: {
        CHECKLIST_FORM: 1,
        CHECKLIST_ADDITIONAL_FORM: 2,
        REVIEW_FORM: 3,
        CHOISE_ADDITIONAL_FORM: 4
    },

    ui: 'light',
    html: 'inspection',
    current: -1,
    forms: {},

    initComponent: function () {
        Ext.apply(this, {
        });
        this.callParent();
    },

    createInspection: function (checklists) {
        this.checklists = checklists;
        this.current = -1;
        this.forms = [];

        checklists.query('additional', false).each(function (checklist) {
            this.forms.push({
                type: this.self.CHECKLIST_FORM,
                view: null,
                checklist: checklist
            });
        }, this);
        this.forms[0].isFirst = true;

        var index = this.forms.push({
            type: this.self.CHOISE_ADDITIONAL_FORM,
            checklistPages: [],
            view: null
        });

        checklists.query('additional', true).each(function (checklist) {
            var page = {
                type: this.self.CHECKLIST_ADDITIONAL_FORM,
                view: null,
                enabled: false,
                checklist: checklist
            };
            this.forms.push(page);
            this.forms[index-1].checklistPages.push(page);
        }, this);

        this.forms.push({
            type: this.self.REVIEW_FORM,
            view: null
        });

        this.onNextClick();
    },

    onNextClick: function () {
        var form;
        this.current++;
        form = this.forms[this.current];

        if (form) {
            if (form.type === this.self.CHECKLIST_ADDITIONAL_FORM && ! form.enabled) {
                this.onNextClick();
                return;
            }
            this.goToForm(form);
        } 
    },

    onPrevClick: function () {
        var form;
        this.current--;
        form = this.forms[this.current];

        if (form) {
            if (form.type === this.self.CHECKLIST_ADDITIONAL_FORM && ! form.enabled) {
                this.onPrevClick();
                return;
            }
            this.goToForm(form);
        } 
    },

    goToForm: function (form) {
        if (! form.view) {
            this.createForm(form);
        }

        this.getLayout().setActiveItem(form.view);
    },

    createForm: function (form) {
        switch(form.type) {
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

    createChecklistForm: function (form) {
        var buttons = [];

        buttons.unshift({
            text: 'Next',
            handler: this.onNextClick,
            scope: this
        });

        if (! form.isFirst) {
            buttons.unshift({
                text: 'Prev',
                handler: this.onPrevClick,
                scope: this
            });
        }

        form.view = this.add({
            xtype: 'form',
            checklist: form.checklist,
            title: form.checklist.get('groupName').toUpperCase(),
            items: this.createChecklistFields(form.checklist.items()),
            buttons: buttons
        });
    },

    createChecklistFields: function (fields) {
        var formFields = [];

        fields.each(function (field) {
            switch(field.get('type')) {
                case 'text':
                    formFields = formFields.concat(this.createTextField(field));
                    break;
                case 'radio':
                    formFields = formFields.concat(this.createRadioField(field));
                    break;
                case 'datePicker':
                    formFields = formFields.concat(this.createDatePickerField(field));
                    break;
                case 'group':
                    formFields = formFields.concat(this.createGroupField(field));
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
            fieldLabel: field.get('fieldName'),
            value: field.get('fieldValue') || field.get('defaultValue')
        };
    },

    createRadioField: function (field) {
        var me = this,
            formAdditionalFields = [],
            options = [],
            listeners = {},
            additionalFields;

        if (field.get('additional')) {
            additionalFields = field.items();
            formAdditionalFields = this.createChecklistFields(additionalFields);
            Ext.each(formAdditionalFields, function (field) {
                field.hidden = true;
                field.disabled = true;
            });
            listeners.checkAdditional = function (value) {
                if (RegExp(this.field.get('triggerValue'), 'i').test(value)) {
                    additionalFields.each(function (field) {
                        var formField = me.down('component[fieldId=' + field.get('id') + ']');
                        formField.show(true);
                        formField.enable();
                    });
                } else {
                    additionalFields.each(function (field) {
                        var formField = me.down('component[fieldId=' + field.get('id') + ']');
                        formField.hide(true);
                        formField.disable();
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

        if (field.alerts().getCount()) {
            listeners.checkAlert = function (value) {
                var alert = field.alerts().first();
                if (RegExp(this.field.get('triggerValue'), 'i').test(value)) {
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
            xtype: 'fieldcontainer',
            layout: 'hbox',
            fieldId: field.get('id'),
            field: field,
            labelAlign: 'top',
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
            fieldLabel: field.get('fieldName'),
            items: options,
            listeners: listeners
        }].concat(formAdditionalFields);
    },

    createDatePickerField: function (field) {
        return {
            xtype: 'datefield',
            value: field.get('fieldValue') || '',
            fieldLabel: field.get('fieldName')
        };
    },

    createCheckboxField: function (field) {
        return {
            xtype: 'checkbox',
            fieldLabel: field.get('fieldName')
        };
    },

    createGroupField: function (field) {
        return {
            xtype: 'fieldcontainer',
            layout: 'vbox',
            labelAlign: 'top',
            fieldLabel: field.get('fieldName'),
            items: this.createChecklistFields(field.items())
        };
    },

    createChoiseAdditionalForm: function (form) {
        var checkboxes = [];
        Ext.each(form.checklistPages, function (checklistPage) {
            checkboxes.push({
                fieldLabel: checklistPage.checklist.get('groupName'),
                listeners: {
                    change: function (checkbox, value) {
                        checklistPage.enabled = value;
                    }
                }
            });
        });
        form.view = this.add({
            xtype: 'form',
            defaultType: 'checkboxfield',
            title: 'ADDITIONAL',
            items: checkboxes,
            buttons: [{
                text: 'Prev',
                handler: this.onPrevClick,
                scope: this
            }, {                
                text: 'Next',
                handler: this.onNextClick,
                scope: this
            }]
        });
    },

    createReviewForm: function (form) {
        form.view = this.add({
            xtype: 'form',
            title: 'REVIEW',
            buttons: [{
                text: 'Prev',
                handler: this.onPrevClick,
                scope: this
            }, {                
                text: 'Submit',
                handler: this.onSubmitClick,
                scope: this
            }]
        });
    },

    getAlertsStore: function () {
        return this.alertsStore;
    },

    getChecklists: function () {
        return this.checklists;
    }
});
