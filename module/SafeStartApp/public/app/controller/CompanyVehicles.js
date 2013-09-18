Ext.define('SafeStartApp.controller.CompanyVehicles', {
    extend: 'SafeStartApp.controller.DefaultVehicles',

    config: {
        control: {
            'SafeStartCompanyPage SafeStartVehicleInspection formpanel button[action=prev]': {
                tap: 'onPrevBtnTap'
            },
            'SafeStartCompanyPage SafeStartVehicleInspection formpanel button[action=next]': {
                tap: 'onNextBtnTap'
            },
            'SafeStartCompanyPage SafeStartVehicleInspection formpanel[name=checklist-card-choise-additional] checkboxfield': {
                change: 'onSelectAdditional'
            },
            'SafeStartCompanyPage SafeStartVehicleInspection formpanel button[action=submit]': {
                tap: 'onReviewSubmitBtnTap'
            },
            'SafeStartCompanyPage SafeStartVehicleInspection sheet[cls=sfa-messagebox-confirm] button[action=confirm]': {
                tap: 'onReviewConfirmBtnTap'
            },
            'SafeStartCompanyPage SafeStartVehicleInspectionDetails button[action=print]': {
                tap: 'downloadVehicleInspectionDetailsPdf'
            },
            'SafeStartCompanyPage SafeStartVehicleInspectionsPanel': {
                editInspection: 'onEditInspectionAction'
            },
            reviewCard: {
                activate: 'onActivateReviewCard'
            },
            navMain: {
                itemtap: 'onSelectAction',
                back: 'hideSelectionAction'
            },
            addButton: {
                tap: 'addAction'
            }
        },

        refs: {
            navMain: 'SafeStartCompanyPage SafeStartNestedListVehicles',
            infoPanel: 'SafeStartCompanyPage panel[name=info-container]',
            vehicleInspectionPanel: 'SafeStartCompanyPage SafeStartVehicleInspection',
            vehicleUsersPanel: 'SafeStartCompanyPage SafeStartVehicleUsersPanel',
            vehicleAlertsPanel: 'SafeStartCompanyPage SafeStartVehicleAlertsPanel',
            vehicleInspectionDetailsPanel: 'SafeStartCompanyPage SafeStartVehicleInspectionDetails',
            vehicleInspectionsPanel: 'SafeStartCompanyPage SafeStartVehicleInspectionsPanel',
            addButton: 'SafeStartCompanyPage SafeStartCompanyToolbar > button[action=add-vehicle]',
            manageChecklistPanel: 'SafeStartCompanyPage > panel[name=info-container] > panel[name=vehicle-manage]',
            reviewCard: 'SafeStartCompanyPage SafeStartVehicleInspection formpanel[name=checklist-card-review]'
        }
    },

    onSelectAction: function () {
        this.selectedRecord = this.getNavMain().getActiveItem().getStore().getNode();
        this.selectedNodeId = arguments[4].get('id');
        switch (arguments[4].get('action')) {
            case 'info':
                this.getInfoPanel().setActiveItem(0);
                this.showUpdateForm();
                break;
            case 'fill-checklist':
                this.getInfoPanel().setActiveItem(this.getVehicleInspectionPanel());
                this.loadChecklist(arguments[4].parentNode.get('id'));
                break;
            case 'inspections':
                this.getInfoPanel().setActiveItem(this.getVehicleInspectionsPanel());
                this.getVehicleInspectionsPanel().loadList(arguments[4].parentNode);
                break;
            case 'alerts':
                this.getInfoPanel().setActiveItem(this.getVehicleAlertsPanel());
                this.getVehicleAlertsPanel().loadList(arguments[4].parentNode.get('id'));
                break;
            case 'update-checklist':
                this.getInfoPanel().setActiveItem(4);
                this.showUpdateCheckList();
                break;
            case 'users':
                this.loadUsers(arguments[4].parentNode.get('id'));
                this.getInfoPanel().setActiveItem(this.getVehicleUsersPanel());
                break;
            case 'check-list':
                var panel = this.getVehicleInspectionDetailsPanel();
                this.getInfoPanel().setActiveItem(panel);
                panel.loadChecklist(arguments[4].parentNode.parentNode, arguments[4].get('checkListId'));
                break;
        }
    },

    hideSelectionAction: function() {
        this.getInfoPanel().setActiveItem(10);
    },

    checklistStores: {},
    checkListTrees: {},

    showUpdateCheckList: function () {
        Ext.Object.each(this.checkListTrees, function(key, obj) {
            obj.hide();
        });
        if (!this.checklistStores[this.selectedNodeId]) {
            this.checklistStores[this.selectedNodeId] = Ext.create('SafeStartApp.store.VehicleChecklist');
            this.checklistStores[this.selectedNodeId].getProxy().setExtraParam('vehicleId', this.selectedNodeId);
        }
        if (!this.checkListTrees[this.selectedNodeId]) {
            this.checkListTrees[this.selectedNodeId] = new SafeStartApp.view.components.UpdateVehicleChecklist({checkListStore: this.checklistStores[this.selectedNodeId]});
            this.getInfoPanel().getActiveItem().add(this.checkListTrees[this.selectedNodeId]);
        }
        this.checkListTrees[this.selectedNodeId].show();
    },

    loadUsers: function (id) {
        var self = this;
        SafeStartApp.AJAX('vehicle/' + id + '/users', {}, function (result) {
            self.getVehicleUsersPanel().buildList(result, id);
        });
    },

    updateUsersAction: function(value, id, obj) {
        SafeStartApp.AJAX('vehicle/' + id + '/update-users', {value: value}, function (result) {

        });
    },

    downloadVehicleInspectionDetailsPdf: function (btn) {
        window.open('/api/checklist/' + btn.config.checkListId + '/generate-pdf', '_blank');
    },

    onEditInspectionAction: function (vehicleId, checkListId, inspectionRecord) {
        var self = this;
        this.getInfoPanel().setActiveItem(this.getVehicleInspectionPanel());
        SafeStartApp.AJAX('vehicle/' + vehicleId + '/getchecklist?checklistId=' + checkListId, {}, function (result) {
            self.getVehicleInspectionPanel().loadChecklist(result.checklist, vehicleId, inspectionRecord);
        });
    }

});