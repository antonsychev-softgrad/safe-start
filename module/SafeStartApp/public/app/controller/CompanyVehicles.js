Ext.define('SafeStartApp.controller.CompanyVehicles', {
    extend: 'Ext.app.Controller',
    
    mixins: ['SafeStartApp.controller.mixins.Form'],

    requires: [
        'SafeStartApp.view.components.UpdateChecklist',
        'SafeStartApp.store.VehicleChecklist',
        'SafeStartApp.model.Vehicle',
        'SafeStartApp.view.forms.Vehicle'
    ],

    selectedNodeId: 0,
    selectedRecord: 0,

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
        var me = this;
        this.getInfoPanel().setActiveItem(this.getVehicleInspectionPanel());
        SafeStartApp.AJAX('vehicle/' + vehicleId + '/getchecklist?checklistId=' + checkListId, {}, function (result) {
            me.getVehicleInspectionPanel().loadChecklist(result.checklist, vehicleId, inspectionRecord);
            me.loadAlerts(checkListId);
        });
    },

    loadAlerts: function (checkListId) {
        var me = this;
        SafeStartApp.AJAX('vehicle/inspection/' + checkListId + '/alerts', {}, function (result) {
            me.getVehicleInspectionPanel().fillAlertsData(result);
        });
    },

    showUpdateForm: function () {
        if (!this.currentForm) {
            this._createForm();
        }
        this.currentForm.setRecord(this.selectedRecord);
        this.currentForm.down('button[name=delete-data]').show();
        this.currentForm.down('button[name=reset-data]').hide();
    },

    addAction: function () {
        this.getInfoPanel().setActiveItem(0);
        this.getNavMain().goToNode(this.getNavMain().getStore().getRoot());
        this.selectedNodeId = 0;
        if (!this.currentForm) {
            this._createForm();
        }
        if (this.vehicleModel) {
            this.vehicleModel.destroy();
        }
        this.vehicleModel = new SafeStartApp.model.Vehicle();
        this.currentForm.setRecord(this.vehicleModel);
        this.currentForm.down('button[name=delete-data]').hide();
        this.currentForm.down('button[name=reset-data]').show();
    },

    saveAction: function () {
        if (!this.vehicleModel) {
            this.vehicleModel = Ext.create('SafeStartApp.model.Vehicle');
        }
        if (this.validateFormByModel(this.vehicleModel, this.currentForm)) {
            var self = this;
            var formValues = this.currentForm.getValues();
            if (SafeStartApp.companyModel) {
                formValues.companyId = SafeStartApp.companyModel.get('id');
            } else {
                formValues.companyId = SafeStartApp.userModel.get('companyId');
            }
            SafeStartApp.AJAX('vehicle/' + this.currentForm.getValues().id + '/update', formValues, function (result) {
                if (result.vehicleId) {
                    self._reloadStore(result.vehicleId);
                    self.currentForm.down('button[name=delete-data]').show();
                    self.currentForm.down('button[name=reset-data]').hide();
                }
            });
        }
    },

    deleteAction: function () {
        var self = this;
        Ext.Msg.confirm("Confirmation", "Are you sure you want to delete this vehicle?", function () {
            SafeStartApp.AJAX('vehicle/' + self.currentForm.getValues().id + '/delete', {}, function (result) {
                self.getNavMain().getStore().loadData();
                self.currentForm.reset();
                self.currentForm.down('button[name=delete-data]').hide();
                self.currentForm.down('button[name=reset-data]').show();
                self.getNavMain().goToNode(self.getNavMain().getStore().getRoot());
            });
        });
    },

    resetAction: function () {
        this.currentForm.reset();
    },

    _createForm: function () {
        if (!this.currentForm) {
            this.currentForm = Ext.create('SafeStartApp.view.forms.Vehicle');
            this.getInfoPanel().getActiveItem().add(this.currentForm);
            this.currentForm.addListener('save-data', this.saveAction, this);
            this.currentForm.addListener('reset-data', this.resetAction, this);
            this.currentForm.addListener('delete-data', this.deleteAction, this);
        }
    },

    _reloadStore: function (vehicleId) {
        this.getNavMain().goToNode(this.getNavMain().getStore().getRoot());
        this.getNavMain().getStore().loadData();
        this.getNavMain().getStore().addListener('load', function () {
            if (!vehicleId) {
                return;
            }
            this.getNavMain().goToNode(this.getNavMain().getStore().getNodeById(vehicleId));
            this.getNavMain().goToLeaf(this.getNavMain().getStore().getNodeById(vehicleId + '-info'));
        }, this);

    },


    loadChecklist: function (id) {
        var self = this;
        SafeStartApp.AJAX('vehicle/' + id + '/getchecklist', {}, function (result) {
            self.getVehicleInspectionPanel().loadChecklist(result.checklist, id);
        });
    },

    onNextBtnTap: function (btn) {
        var checklistPanel = this.getVehicleInspectionPanel(),
            activeCard = btn.up('formpanel'),
            includedCards = this.getIncludedChecklistCards(),
            nextIndex = 0,
            index = includedCards.indexOf(activeCard);

        if (index !== -1) {
            nextIndex = index + 1;
        }
        if (includedCards[nextIndex]) {
            checklistPanel.setActiveItem(includedCards[nextIndex]);
        }
    },

    onPrevBtnTap: function (btn) {
        var vehicleInspectionPanel = this.getVehicleInspectionPanel(),
            activeCard = btn.up('formpanel'),
            includedCards = this.getIncludedChecklistCards(),
            prevIndex = 0,
            index = includedCards.indexOf(activeCard);

        if (index !== -1) {
            prevIndex = index - 1;
        }
        if (includedCards[prevIndex]) {
            vehicleInspectionPanel.setActiveItem(includedCards[prevIndex]);
        }
    },

    onSelectAdditional: function (checkbox, state) {
        this.getVehicleInspectionPanel()
            .down('formpanel{config.groupId === ' + checkbox.config.checklistGroupId + '}')
            .isIncluded = state ? true : false;
    },

    getIncludedChecklistCards: function () {
        var query = [
            'formpanel[name=checklist-card]',
            'formpanel[name=checklist-card-choise-additional]',
            'formpanel[name=checklist-card-additional][isIncluded]',
            'formpanel[name=checklist-card-review]'
        ].join(', ');
        return this.getVehicleInspectionPanel().query(query);
    },

    getChecklistForms: function () {
        var query = [
            'formpanel[name=checklist-card]',
            'formpanel[name=checklist-card-additional][isIncluded]'
        ].join(', ');
        return this.getVehicleInspectionPanel().query(query);
    },

    onActivateReviewCard: function (reviewCard, vehicleInspectionPanel) {
        var checklists = this.getChecklistForms();
        var passedCards = [];
        var vehicleInspectionPanel = this.getVehicleInspectionPanel();
        var alertsStore = vehicleInspectionPanel.getAlertsStore();
        var alerts = [];
        Ext.each(checklists, function (checklist) {
            var triggerableFields = checklist.query('[triggerable]');
            var alert = false;
            Ext.each(triggerableFields, function (field) {
                if (field.config.fieldId) {
                    alertsStore.each(function (record) {
                        if (record.get('fieldId') === field.config.fieldId && record.get('active') === true) {
                            Ext.Array.include(alerts, record);
                            alert = true;
                        }
                    });
                }
            });
            passedCards.push({
                groupName: checklist.config.groupName,
                additional: checklist.config.additional,
                alert: alert
            });
        });
        vehicleInspectionPanel.updateReview(passedCards, alerts);
    },

    onReviewSubmitBtnTap: function (button) {
        var submitMsgBox = Ext.create('Ext.MessageBox', {
            cls: 'sfa-messagebox-confirm',
            message: 'Please confirm your submission',
            buttons: [
                {
                    ui: 'confirm',
                    action: 'confirm',
                    text: 'Confirm'
                },
                {
                    ui: 'action',
                    text: 'Cancel',
                    handler: function (btn) {
                        btn.up('sheet[cls=sfa-messagebox-confirm]').destroy();
                    }
                }
            ]
        });

        this.getVehicleInspectionPanel().add(submitMsgBox);
    },

    onReviewConfirmBtnTap: function (button) {
        var controller = this,
            alerts = [],
            vehicleInspectionPanel = this.getVehicleInspectionPanel(),
            checklists = this.getChecklistForms(),
            fieldValues = [],
            gpsContainer = vehicleInspectionPanel.down('container[cls=sfa-vehicle-inspection-gps]'),
            odometerKms = vehicleInspectionPanel.down('field[name=current-odometer-kms]').getValue(),
            odometerHours = vehicleInspectionPanel.down('field[name=current-odometer-hours]').getValue(),
            location = '',
            gps,
            alert,
            inspectionRecord = vehicleInspectionPanel.inspectionRecord,
            getParams = '',
            fields = [],
            data;



        if (gpsContainer.down('togglefield').getValue() && gpsContainer.gps) {
            gps = gpsContainer.gps;
            location = gps.getLatitude() + ';' + gps.getLongitude();
        }
        Ext.each(vehicleInspectionPanel.query('container[name=alert-container]'), function (alertContaienr) {
            alert = alertContaienr.config.alertModel;
            alerts.push({
                fieldId: parseInt(alert.get('fieldId')),
                comment: alert.get('comment'),
                images: alert.get('photos')
            });
        });
        Ext.each(checklists, function (checklist) {
            fields = checklist.query('field');
            Ext.each(fields, function (field) {
                if (field.isHidden()) {
                    return;
                }
                switch (field.xtype) {
                    case 'checkboxfield':
                        //TODO: unhardcode field value
                        if (field.isChecked()) {
                            value = 'Yes';
                        } else {
                            value = 'No';
                        }
                        fieldValues.push({
                            id: field.config.fieldId,
                            value: value
                        });
                        break;
                    case 'radiofield':
                        if (field.isChecked()) {
                            fieldValues.push({
                                id: field.config.fieldId,
                                value: field.getValue()
                            });
                        }
                        break;
                    case 'textfield':
                        fieldValues.push({
                            id: field.config.fieldId,
                            value: field.getValue()
                        });
                        break;
                    case 'datepickerfield':
                        fieldValues.push({
                            id: field.config.fieldId,
                            value: field.getValue()
                        });
                        break;
                }
            });
        });

        data = {
            date: Date.now(),
            fields: fieldValues,
            alerts: alerts,
            odometer: odometerKms, 
            odometer_hours: odometerHours,
            gps: location
        };

        if (inspectionRecord) {
            getParams = '?checklistId=' + inspectionRecord.get('checkListId');
        }

        var navMain = this.getNavMain();
        var inspectionsPanel = this.getVehicleInspectionsPanel();

        SafeStartApp.AJAX('vehicle/' + vehicleInspectionPanel.vehicleId + '/completechecklist' + getParams, data, function (result) {
            vehicleInspectionPanel.clearChecklist();
            vehicleInspectionPanel.down('sheet[cls=sfa-messagebox-confirm]').destroy();
            var active = navMain.getActiveItem();
            var vehicleId = vehicleInspectionPanel.vehicleId;
            var inspectionsNode = navMain.getStore().getNodeById(vehicleId + '-inspections');
            var index = navMain.getStore().getNodeById(vehicleId).indexOf(inspectionsNode);
            inspectionsPanel.inspectionsStore.on({
                load: function (store, records) {
                    var record = store.findRecord('hash', result.checklist);
                    inspectionsPanel.loadChecklistDetails(record);
                },
                single: true
            });
            navMain.fireEvent('itemtap', navMain, active, index, null, inspectionsNode);
        });
    }

});