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
                addVehicleAction: this.addVehicle
            },
            'SafeStartExtComponentCompany': {
                activate: this.refreshPage,
                afterrender: this.refreshPage
            },
            'SafeStartExtFormVehicle': {
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
                afterrender: this.createInspection,
                completeInspectionAction: this.completeInspection
            },
            'SafeStartExtPanelManageChecklist': {
                saveField: this.saveChecklistField,
                deleteField: this.deleteChecklistField
            },
            'SafeStartExtPanelVehicleUsers': {
                saveVehicleUsers: this.saveVehicleUsers
            }
        });
    },

    changeAction: function (action) {
        return this.getVehicleTabsView().changeAction(action);
    },

    addVehicle: function () {
        var vehicle = SafeStartExt.model.MenuVehicle.create({});
        vehicle.pages().add([{
            action: 'info',
            text: 'Current Information'
        }]);
        this.deselectVehicle();
        this.changeVehicleAction(vehicle);
    },

    updateVehicle: function (vehicle, data) {
        var me = this;
        data.companyId = this.company.get('id');
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

    fillForm: function (form) {
        form.loadRecord(this.vehicle);
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
                        []
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
            }
        });
    },

    actionChange: function () {
        console.log(arguments);
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
    }
});
