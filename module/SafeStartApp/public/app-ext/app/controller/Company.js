Ext.define('SafeStartExt.controller.Company', {
    extend: 'Ext.app.Controller',

    refs: [{
        selector: 'SafeStartExtComponentCompany SafeStartExtPanelVehicleList',
        ref: 'vehicleListView'
    }, {
        selector: 'SafeStartExtComponentCompany SafeStartExtPanelVehicleTabs',
        ref: 'vehicleTabsView'
    }, {
        selector: 'SafeStartExtComponentCompany SafeStartExtContainerTopNav',
        ref: 'vehicleTopNav'
    }, {
        selector: 'SafeStartExtComponentCompany',
        ref: 'companyPage'
    }, {
        selector: 'SafeStartExtMain',
        ref: 'mainPanel'
    }, {
        selector: 'SafeStartExtPanelInspectionInfo',
        ref: 'inspectionInfoPanel'
    }, {
        selector: 'SafeStartExtComponentCompany SafeStartExtFormVehicle',
        ref: 'vehicleForm'
    }, {
        selector: 'SafeStartExtComponentCompany SafeStartExtPanelInspection',
        ref: 'inspectionPanel'
    }],

    needUpdate: false,

    init: function () {
        this.control({
            'SafeStartExtPanelVehicleTabs': {
                tabchange: this.actionChange
            },
            'SafeStartExtMain': {
                setCompanyAction: this.setCompanyAction,
                changeCompanyAction: this.changeCompanyAction
            },
            'SafeStartExtPanelVehicleList': {
                changeVehicleAction: this.changeVehicleAction,
                addVehicleAction: this.addVehicle,
                exportCompanyAction: this.exportCompany
            },
            'SafeStartExtComponentCompany': {
                activate: this.refreshPage,
                afterrender: this.refreshPage
            },
            'SafeStartExtFormVehicle': {
            },
            'SafeStartExtFormVehicleCustomFields': {
            },
            'SafeStartExtPanelVehicleFields': {
                afterrender: this.fillForm,
                updateVehicleAction: this.updateVehicle,
                deleteVehicleAction: this.deleteVehicle
            },
            'SafeStartExtPanelInspections': {
                afterrender: this.loadInspections,
                editInspectionAction: this.editInspection,
                deleteInspectionAction: this.deleteInspection,
                printInspectionAction: this.printInspection
            },
            'SafeStartExtPanelInspections dataview': {
                itemclick: this.setInspectionInfo
            },
            'SafeStartExtPanelInspection': {
                activate: this.createInspection,
                //afterrender: this.createInspection,
                completeInspectionAction: this.completeInspection
            },
            'SafeStartExtPanelManageChecklist': {
                saveField: this.saveChecklistField,
                deleteField: this.deleteChecklistField
            },
            'SafeStartExtPanelManageVehicleField': {
                saveField: this.saveVehicleField,
                deleteField: this.deleteVehicleField
            },
            'SafeStartExtPanelVehicleUsers': {
                saveVehicleUsers: this.saveVehicleUsers
            }
        });
    },

    exportCompany: function () {

        var me = this,
            now = new Date(),
            prevDate = new Date();
        prevDate.setMonth(prevDate.getMonth() - 1);

        var win = Ext.widget('window', {
            title: 'Export Vehicle List',
            closeAction: 'hide',
            layout: 'fit',
            resizable: false,
            modal: true,
            items: Ext.create('Ext.form.Panel', {
                width: 350,
                border: false,
                bodyBorder: false,
                fieldDefaults: {
                    labelWidth: 75,
                    msgTarget: 'side'
                },
                defaults: {
                    margins: '0 0 10 0'
                },
                layout: {
                    type: 'vbox',
                    align: 'stretch'
                },
                buttonAlign: 'center',
                bodyPadding: 10,
                items: [{
                    xtype: 'fieldcontainer',
                    fieldLabel: 'Select start and end dates for a report',
                    maxWidth: 400,
                    cls: 'sfa-field-group',
                    labelCls: 'sfa-field-group-label',
                    layout: {
                        type: 'vbox',
                        align: 'stretch'
                    },
                    labelAlign: 'top',
                    items: [{
                        xtype: 'datefield',
                        name: 'from',
                        fieldLabel: 'From',
                        dateFormat: SafeStartExt.dateFormat,
                        cls: 'sfa-datepicker',
                        value: prevDate,
                        allowBlank: false
                    }, {
                        xtype: 'datefield',
                        name: 'to',
                        fieldLabel: 'To',
                        dateFormat: SafeStartExt.dateFormat,
                        cls: 'sfa-datepicker',
                        value: now,
                        allowBlank: false
                    }]
                }],
                buttons: [{
                    text: 'Cancel',
                    handler: function () {
                        //this.up('form').getForm().reset();
                        this.up('window').hide();
                    }
                }, {
                    name: 'print',
                    text: 'Export',
                    handler: function () {
                        if (this.up('form').getForm().isValid()) {
                            var form = this.up('form').getForm();
                            var from = Math.round(form.findField('from').getValue().getTime() / 1000);
                            var to = Math.round(form.findField('to').getValue().getTime() / 1000);
                            var url = '/api/company/' + me.company.getId() + '/export/' +
                                from + '/' + to;

                            this.up('window').hide();

                            window.open(url, '_blank');
                        }
                    }
                }]
            })
        });

        win.show();
    },

    changeAction: function (action) {
        return this.getVehicleTabsView().changeAction(action);
    },

    addVehicle: function () {
        var vehicle = SafeStartExt.model.MenuVehicle.create({});
        vehicle.set('customFields', [
            {id: 0, title: "Model", type: "text", default_value: null},
            {id: 0, title: "Make", type: "text", default_value: null},
            {id: 0, title: "Project Name", type: "text", default_value: null},
            {id: 0, title: "Project Number", type: "text", default_value: null}
        ]);

        vehicle.pages().add([{
            action: 'info',
            text: 'Current Information'
        }]);
        this.deselectVehicle();
        this.changeVehicleAction(vehicle);
    },

    updateVehicle: function (vehicle, data, customFields) {
        var me = this;
        data.companyId = this.company.get('id');
        var date = Ext.Date.parseDate(data.expiryDate, SafeStartExt.dateFormat);
        if (date) {
            data.expiryDate = date.getTime()/1000;
        }

        data.customFields = customFields;
        Ext.applyIf(data, {
            enabled: false
        });

        SafeStartExt.Ajax.request({
            url: 'vehicle/' + vehicle.get('id') + '/update',
            data: data,
            success: function (res) {
                if (res.done) {
                    me.reloadVehicles(res.vehicleId);
                }
            }
        });
    },

    deleteVehicle: function (vehicle) {
        var me = this;
        SafeStartExt.Ajax.request({
            url: 'vehicle/' + vehicle.get('id') + '/delete',
            success: function (res) {
                if (res.done) {
                    me.reloadVehicles();
                }
            }
        });
    },

    reloadVehicles: function (vehicleId, action, params) {
        var me = this, 
            store = this.getVehicleListView().getListStore();

        store.load({
            callback: function (records) {
                var record;
                if (vehicleId) {
                    record = this.findRecord('id', vehicleId);
                }
                if (record) {
                    me.selectVehicle(record, action, params);
                } else {
                    me.deselectVehicle();
                }
            }
        });
    },

    selectVehicle: function (vehicle, action, params) {
        this.getVehicleListView().getList().select(vehicle);
        this.changeVehicleAction(vehicle, action, params);
    },

    deselectVehicle: function () {
        if (this.vehicle) {
            this.getVehicleListView().getList().deselect(this.vehicle);
        }
        this.getCompanyPage().unsetVehicle();
    },

    changeCompanyAction: function(company) {
        if (this.company === company) {
            return;
        }
        this.company = company;
        this.needUpdate = true;

        if (this.getMainPanel().getLayout().getActiveItem() === this.getCompanyPage()) {
            this.refreshPage();
        }
    },

    setCompanyAction: function (company) {
        if (this.company === company) {
            return;
        }
        this.company = company;
        this.needUpdate = true;

        if (this.getMainPanel().getLayout().getActiveItem() === this.getCompanyPage()) {
            this.refreshPage();
        }
    },

    fillForm: function (panel) {
        panel.setVehicle(this.vehicle);
        panel.down('SafeStartExtFormVehicleCustomFields').loadFields(this.vehicle.get('customFields'));
    },

    loadInspections: function (view) {
        view.getListStore().load();
    },

    setInspectionInfo: function (view, inspection) {
        var me = this;
        view.inspection = inspection;
        SafeStartExt.Ajax.request({
            url: 'vehicle/' + inspection.get('id') + '/getchecklistdata',
            success: function (data) {
                me.getInspectionInfoPanel().setInspectionInfo(inspection, data);
            }
        });
    },

    editInspection: function (id) {
        var me = this;
        SafeStartExt.Ajax.request({
            url: 'vehicle/' + this.vehicle.get('id') + '/getchecklist?checklistId=' + id, 
            success: function (result) {
                var inspectionPanel = me.changeAction('fill-checklist', {autoCreateInspection: false});
                if (! inspectionPanel) {
                    return;
                }
                inspectionPanel.editInspection(result, id);
            }
        });
    },

    deleteInspection: function (id) {
        var vehicleId = this.vehicle.get('id');
        var me = this;
        SafeStartExt.Ajax.request({
            url: 'vehicle/inspection/' + id + '/delete',
            success: function (result) {
                me.reloadVehicles(vehicleId, 'inspections');
            }
        });
    },

    printInspection: function (id) {
        window.open('/api/checklist/' + id + '/generate-pdf', '_blank');
    },

    changeVehicleAction: function (vehicle, action, params) {
        this.vehicle = vehicle;
        this.getCompanyPage().setVehicle(vehicle, action, params);
    },

    refreshPage: function () {
        if (this.needUpdate) {
            this.needUpdate = false;
            this.getCompanyPage().unsetVehicle();

            var store = this.getVehicleListView().getListStore();
            this.getVehicleTopNav().setCompanyName(this.company.get('title'));
            store.getProxy().setExtraParam('companyId', this.company.get('id'));
            store.load();
        }
    },

    createInspection: function (inspectionPanel) {
        var me = this;
        if (inspectionPanel.configData && inspectionPanel.configData.autoCreateInspection) {
            SafeStartExt.Ajax.request({
                url: 'vehicle/' + this.vehicle.get('id') + '/getchecklist',
                success: function (result) {
                    me.getInspectionPanel().createInspection(
                        Ext.create('SafeStartExt.store.InspectionChecklists', {data: result.checklist}),
                        null,
                        result.alerts
                    );
                }
            });
        }
    },

    completeInspection: function (data, inspectionId) {
        var me = this;
        var getParams = '';
        if (inspectionId) {
            getParams = '?checklistId=' + inspectionId;
        }
        SafeStartExt.Ajax.request({
            url: 'vehicle/' + this.vehicle.get('id') + '/completechecklist' + getParams,
            data: data,
            success: function (result) {
                me.reloadVehicles(me.vehicle.get('id'), 'inspections', {checklistHash: result.checklist});
            },
            failure: function (result) {
                if (result && result.meta && result.meta.errorCode === 4010) {
                    me.reloadVehicles(me.vehicle.get('id'), 'inspections');
                }
            }
        });
    },

    actionChange: function () {
    },

    saveChecklistField: function (form) {
        var record = form.getRecord();
        SafeStartExt.Ajax.request({
            url: 'checklist/' + record.get('id') + '/update',
            data: record.getWriteData(),
            success: function (result) {
                record.beginEdit();
                if (! record.get('id')) {
                    record.set('id', result.fieldId);
                }
                record.modified = {};
                record.endEdit();
                form.up('SafeStartExtPanelManageChecklist').down('treepanel').getStore().load();
                form.loadRecord(record);
            }
        });
    },

    deleteChecklistField: function (form) {
        var record = form.getRecord();
        var parent = record.parentNode;
        if (record.get('id') === 0 && parent) {
            parent.removeChild(record);
            if (parent.getDepth()) {
                form.up('SafeStartExtPanelManageChecklist').down('treepanel').getSelectionModel().select(parent);
            }
            return;
        }

        Ext.Msg.confirm({
            msg: 'Do you sure you want to delete this field from checklist?',
            buttons: Ext.Msg.YESNO,
            fn: function (result) {
                if (result !== 'yes') {
                    return;
                }
                SafeStartExt.Ajax.request({
                    url: 'checklist/' + record.get('id') + '/delete',
                    success: function(result) {
                        // record.destroy();
                        parent.removeChild(record);
                        form.up('SafeStartExtPanelManageChecklist').down('treepanel').getStore().load();
                        if (parent && parent.getDepth() != 0) {
                            form.up('SafeStartExtPanelManageChecklist').down('treepanel').getSelectionModel().select(parent);
                        }
                    }
                });
            }
        });
    },

    saveVehicleUsers: function (values) {
        SafeStartExt.Ajax.request({
            url: 'vehicle/' + this.vehicle.get('id') + '/update-users',
            data: {value: values}
        });
    },

    saveVehicleField: function (form) {
        var record = form.getRecord();
        SafeStartExt.Ajax.request({
            url: 'vehiclefield/' + record.get('id') + '/update',
            data: record.getWriteData(),
            success: function (result) {
                record.beginEdit();
                if (! record.get('id')) {
                    record.set('id', result.fieldId);
                }
                record.modified = {};
                record.endEdit();
                form.loadRecord(record);
            }
        });
    },

    deleteVehicleField: function (form) {
        var record = form.getRecord();
        var parent = record.parentNode;
        if (record.get('id') === 0 && parent) {
            parent.removeChild(record);
            if (parent.getDepth()) {
                form.up('SafeStartExtPanelManageVehicleField').down('treepanel').getSelectionModel().select(parent);
            }
            return;
        }

        Ext.Msg.confirm({
            msg: 'Do you sure you want to delete this field from checklist?',
            buttons: Ext.Msg.YESNO,
            fn: function (result) {
                if (result !== 'yes') {
                    return;
                }
                SafeStartExt.Ajax.request({
                    url: 'vehiclefield/' + record.get('id') + '/delete',
                    success: function(result) {
                        // record.destroy();
                        parent.removeChild(record);
                        if (parent && parent.getDepth() != 0) {
                            form.up('SafeStartExtPanelManageVehicleField').down('treepanel').getSelectionModel().select(parent);
                        }
                    }
                });
            }
        });
    }

});
