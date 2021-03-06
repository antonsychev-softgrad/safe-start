Ext.define('SafeStartExt.view.form.Vehicle', {
    extend: 'Ext.form.Panel',
    requires: [
        'Ext.form.field.Date',
        'Ext.form.field.Checkbox',
        'Ext.form.field.Text',
        'Ext.form.FieldSet',
        'Ext.form.FieldContainer'
    ],
    xtype: 'SafeStartExtFormVehicle',
    layout: {
        type: 'vbox',
        align: 'stretch'
    },
    fieldDefaults: {
        msgTarget: 'side'
    },
    autoScroll: true,
    buttonAlign: 'left',
    cls: 'sfa-vehicle-fields-form',
    padding: 20,

    initComponent: function () {
        var me = this;
        Ext.apply(this, {
            items: [
//                {
//                xtype: 'textfield',
//                fieldLabel: 'Model',
//                maxWidth: 400,
//                labelWidth: 130,
//                labelSeparator: '*',
//                allowBlank: false,
//                name: 'title'
//            },
//                {
//                xtype: 'textfield',
//                labelWidth: 130,
//                maxWidth: 400,
//                fieldLabel: 'Make',
//                labelSeparator: '',
//                name: 'type'
//            },
                {
                    xtype: 'textfield',
                    labelWidth: 130,
                    maxWidth: 400,
                    fieldLabel: 'Plant ID',
                    labelSeparator: '*',
                    allowBlank: false,
                    name: 'plantId'
                },
//                {
//                xtype: 'textfield',
//                labelWidth: 130,
//                maxWidth: 400,
//                fieldLabel: 'Project Name',
//                labelSeparator: '',
//                name: 'projectName'
//            }, {
//                xtype: 'textfield',
//                labelWidth: 130,
//                maxWidth: 400,
//                fieldLabel: 'Project Number',
//                labelSeparator: '',
//                name: 'projectNumber'
//            },
                {
                    xtype: 'fieldcontainer',
                    height: 32,
                    labelWidth: 130,
                    maxWidth: 400,
                    labelSeparator: '',
                    fieldLabel: 'Registration Expiry',
                    items: [{
                        xtype: 'datefield',
                        name: 'expiryDate',
                        format: SafeStartExt.dateFormat
                    }],
                    cls: 'sfa-datepicker'
                }, {
                    xtype: 'fieldcontainer',
                    height: 32,
                    labelWidth: 130,
                    maxWidth: 400,
                    labelSeparator: '',
                    fieldLabel: 'Enabled',
                    items: [{
                        xtype: 'checkboxfield',
                        name: 'enabled',
                        inputValue: true
                    }]
                }, {
                    xtype: 'fieldcontainer',
                    height: 32,
                    labelWidth: 130,
                    maxWidth: 400,
                    labelSeparator: '',
                    fieldLabel: 'Automatically Send Email',
                    items: [{
                        xtype: 'checkboxfield',
                        name: 'automaticSending',
                        inputValue: true
                    }]
                }, {
                    xtype: 'checkboxgroup',
                    name: 'usage-value',
                    fieldLabel: 'Check inspections by:',
                    cls: 'sfa-field-group',
                    labelCls: 'sfa-field-group-label',
                    padding: '1 0 0 0',
                    labelAlign: 'top',
                    layout: {
                        type: 'vbox',
                        align: 'stretch'
                    },
                    maxWidth: 400,
                    height: 110,
                    allowBlank: false,
                    msgTarget: 'side',
                    autoFitErrors: false,
                    hidden: true,
                    labelableRenderTpl: [
                        // body row. If a heighted Field (eg TextArea, HtmlEditor, this must greedily consume height.
                        '<tr role="presentation" id="{id}-inputRow" <tpl if="inFormLayout">id="{id}"</tpl> class="{inputRowCls}">',
                            // Label cell
                            '<tpl if="labelOnLeft">',
                                '<td role="presentation" id="{id}-labelCell" style="{labelCellStyle}" {labelCellAttrs}>',
                                    '{beforeLabelTpl}',
                                    '<label id="{id}-labelEl" {labelAttrTpl}<tpl if="inputId"> for="{inputId}"</tpl> class="{labelCls}"',
                                        '<tpl if="labelStyle"> style="{labelStyle}"</tpl>',
                                        // Required for Opera
                                        ' unselectable="on"',
                                    '>',
                                        '{beforeLabelTextTpl}',
                                        '<tpl if="fieldLabel">{fieldLabel}{labelSeparator}</tpl>',
                                        '{afterLabelTextTpl}',
                                    '</label>',
                                    '{afterLabelTpl}',
                                '</td>',
                            '</tpl>',
                            // Body of the input. That will be an input element, or, from a TriggerField, a table containing an input cell and trigger cell(s)
                            '<td role="presentation" class="{baseBodyCls} {fieldBodyCls} {extraFieldBodyCls}" id="{id}-bodyEl" colspan="{bodyColspan}" role="presentation">',
                                '{beforeBodyEl}',
                                // Label just sits on top of the input field if labelAlign === 'top'
                                '<tpl if="labelAlign==\'top\'">',
                                    '{beforeLabelTpl}',
                                    '<div role="presentation" id="{id}-labelCell" style="{labelCellStyle}">',
                                        '<label id="{id}-labelEl" {labelAttrTpl}<tpl if="inputId"> for="{inputId}"</tpl> class="{labelCls}"',
                                            '<tpl if="labelStyle"> style="{labelStyle}"</tpl>',
                                            // Required for Opera
                                            ' unselectable="on"',
                                        '>',
                                            '{beforeLabelTextTpl}',
                                            '<tpl if="fieldLabel">{fieldLabel}{labelSeparator}</tpl>',
                                            '{afterLabelTextTpl}',
                                        '</label>',
                                    '</div>',
                                    '{afterLabelTpl}',
                                '</tpl>',
                                '{beforeSubTpl}',
                                '{[values.$comp.getSubTplMarkup(values)]}',
                                '{afterSubTpl}',
                            // Final TD. It's a side error element unless there's a floating external one
                            '<tpl if="msgTarget===\'side\'">',
                                '{afterBodyEl}',
                                '</td>',
                                '<td role="presentation" id="{id}-sideErrorCell" vAlign="middle" style="{[values.autoFitErrors ? \'display:none\' : \'\']}" width="{errorIconWidth}">',
                                    '<div role="presentation" id="{id}-errorEl" class="{errorMsgCls}" style="display:none"></div>',
                                '</td>',
                            '<tpl elseif="msgTarget==\'under\'">',
                                '<div role="presentation" id="{id}-errorEl" class="{errorMsgClass}" colspan="2" style="display:none"></div>',
                                '{afterBodyEl}',
                                '</td>',
                            '</tpl>',
                        '</tr>',
                        {
                            disableFormats: true
                        }
                    ],
                    fieldDefaults: {
                        labelWidth: 130,
                        labelSeparator: '',
                        inputValue: true,
                        uncheckedValue: false
                    },
                    items: [{
                        xtype: 'checkboxfield',
                        name: 'useKms',
                        fieldLabel: 'Km\'s',
                        handler: function () {
                            //if (this.checked) {
                            //    this.up('form').down('[name=useHours]').setValue(false);
                            //} else {
                            //    this.up('form').down('[name=useHours]').setValue(true);
                            //}
                        }
                    }, {
                        xtype: 'checkboxfield',
                        name: 'useHours',
                        fieldLabel: 'Hours',
                        handler: function () {
                            //if (this.checked) {
                            //    this.up('form').down('[name=useKms]').setValue(false);
                            //} else {
                            //    this.up('form').down('[name=useKms]').setValue(true);
                            //}
                        }
                    }]
                }, {
                    xtype: 'fieldcontainer',
                    height: 110,
                    fieldLabel: 'Next service due',
                    maxWidth: 800,
                    cls: 'sfa-field-group',
                    labelCls: 'sfa-field-group-label',
                    layout: {
                        type: 'vbox',
                        align: 'stretch'
                    },
                    labelAlign: 'top',
                    items: [{
                        xtype: 'container',
                        name: 'service-due-hours',
                        layout: 'hbox',
                        padding: '0 0 5 0',
                        items: [{
                            xtype: 'numberfield',
                            labelWidth: 130,
                            labelSeparator: '*',
                            fieldLabel: 'Hours',
                            name: 'serviceDueHours'
                        }, {
                            xtype: 'numberfield',
                            labelWidth: 180,
                            width: 300,
                            labelSeparator: '',
                            fieldLabel: 'Service Reminder Threshold',
                            name: 'serviceThresholdHours'
                        }, {
                            xtype: 'component',
                            html: 'Hours',
                            cls: 'x-form-item-label'
                        }]
                    }, {
                        xtype: 'container',
                        name: 'service-due-kms',
                        layout: 'hbox',
                        items: [{
                            xtype: 'numberfield',
                            //hideTrigger: true,
                            labelWidth: 130,
                            labelSeparator: '*',
                            fieldLabel: 'Kilometers',
                            name: 'serviceDueKm'
                        }, {
                            xtype: 'numberfield',
                            labelWidth: 180,
                            width: 300,
                            labelSeparator: '',
                            fieldLabel: 'Service Reminder Threshold',
                            name: 'serviceThresholdKm'
                        }, {
                            xtype: 'component',
                            html: 'Kms',
                            cls: 'x-form-item-label'
                        }]
                    }]
                }, {
                    xtype: 'fieldcontainer',
                    height: 110,
                    fieldLabel: 'Current Odometer',
                    cls: 'sfa-field-group',
                    labelCls: 'sfa-field-group-label',
                    padding: '1 0 0 0',
                    labelAlign: 'top',
                    layout: {
                        type: 'vbox',
                        align: 'stretch'
                    },
                    maxWidth: 400,
                    items: [{
                        xtype: 'numberfield',
                        //hideTrigger: true,
                        fieldLabel: 'Hours',
                        labelWidth: 130,
                        labelSeparator: '',
                        name: 'currentOdometerHours'
                    }, {
                        xtype: 'numberfield',
                        //hideTrigger: true,
                        labelWidth: 130,
                        fieldLabel: 'Kilometers',
                        labelSeparator: '',
                        name: 'currentOdometerKms'
                    }]
                }, {
                    xtype: 'fieldcontainer',
                    labelWidth: 130,
                    maxWidth: 400,
                    labelSeparator: '',
                    fieldLabel: 'Next Service Day',
                    items: [{
                        xtype: 'datefield',
                        disabled: true,
                        name: 'nextServiceDay'
                    }],
                    cls: 'sfa-datepicker'
                }]
//            bbar: [{
//                xtype: 'container',
//                defaults: {
//                    xtype: 'button',
//                    margin: '4 8'
//                },
//                items: [{
//                    text: 'Delete',
//                    ui: 'red',
//                    name: 'delete',
//                    scale: 'medium',
//                    minWidth: 140,
//                    handler: function () {
//                        Ext.Msg.confirm({
//                            title: 'Confirmation',
//                            msg: 'Are you sure want to delete this vehicle?',
//                            buttons: Ext.Msg.YESNO,
//                            fn: function (btn) {
//                                if (btn !== 'yes') {
//                                    return;
//                                }
//                                me.fireEvent('deleteVehicleAction', me.getRecord());
//                            }
//                        });
//                    }
//                }, {
//                    text: 'Save',
//                    ui: 'blue',
//                    scale: 'medium',
//                    minWidth: 140,
//                    handler: function () {
//                        if (me.isValid()) {
//                            me.fireEvent('updateVehicleAction', me.getRecord(), me.getValues());
//                        }
//                    }
//                }]
//            }]
        });
        this.callParent();
    },

    loadRecord: function (record) {
//        if (! record.get('id')) {
//            this.down('button[name=delete]').disable();
//        } else {
//            this.down('button[name=delete]').enable();
//        }

        var expiryDate = record.get('expiryDate');
        if (!expiryDate) {
            expiryDate = new Date().getTime();
        } else {
            expiryDate = expiryDate * 1000;
        }

        this.callParent(arguments);
        this.down('field[name=expiryDate]').setValue(Ext.Date.format(new Date(expiryDate), SafeStartExt.dateFormat));
    }
});
