Ext.define('SafeStartApp.controller.DefaultVehicles', {
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

    showUpdateForm: function () {
        if (!this.currentForm) this._createForm();
        this.currentForm.setRecord(this.selectedRecord);
        this.currentForm.down('button[name=delete-data]').show();
        this.currentForm.down('button[name=reset-data]').hide();
    },

    addAction: function () {
        this.getInfoPanel().setActiveItem(0);
        this.getNavMain().goToNode(this.getNavMain().getStore().getRoot());
        this.selectedNodeId = 0;
        if (!this.currentForm) this._createForm();
        if (this.vehicleModel) this.vehicleModel.destroy();
        this.vehicleModel = new SafeStartApp.model.Vehicle();
        this.currentForm.setRecord(this.vehicleModel);
        this.currentForm.down('button[name=delete-data]').hide();
        this.currentForm.down('button[name=reset-data]').show();
    },

    saveAction: function () {
        if (!this.vehicleModel) this.vehicleModel = new SafeStartApp.model.Vehicle();
        if (this.validateFormByModel(this.vehicleModel, this.currentForm)) {
            var self = this;
            var formValues = this.currentForm.getValues();
            formValues.companyId = SafeStartApp.companyModel.get('id');
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
        this.getNavMain().getStore().loadData();
        this.getNavMain().getStore().addListener('data-load-success', function () {
            if (!vehicleId) return;
            this.currentForm.setRecord(this.getNavMain().getStore().getById(vehicleId));
        }, this);

    },

    showUpdateCheckList: function () {
        var self = this;
        if (!this.vehicleChecklistStore) {
            this.vehicleChecklistStore = new SafeStartApp.store.VehicleChecklist();
            this.vehicleChecklistStore.getProxy().setExtraParam('vehicleId', this.selectedNodeId);
        } else {
            this.vehicleChecklistStore.getProxy().setExtraParam('vehicleId', this.selectedNodeId);
            this.vehicleChecklistStore.loadData();
        }
        if (!this.checkListTree) {
            this.checkListTree = new SafeStartApp.view.components.UpdateChecklist({checkListStore: this.vehicleChecklistStore});
            this.getInfoPanel().getActiveItem().add(this.checkListTree);
        }
        this.vehicleChecklistStore.addListener('data-load-success', function () {
            if (self.vehicleChecklistStore.getRoot()) self.checkListTree.getTreeList().goToNode(self.vehicleChecklistStore.getRoot());
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
            buttons: [{
                ui: 'confirm',
                action: 'confirm',
                text: 'Confirm'
            }, {
                ui: 'action',
                text: 'Cancel',
                handler: function (btn) {
                    btn.up('sheet[cls=sfa-messagebox-confirm]').destroy();
                }
            }]
        });

        this.getVehicleInspectionPanel().add(submitMsgBox);
    },

    onReviewConfirmBtnTap: function (button) {
        var controller = this;
        var alerts = [];
        var vehicleInspectionPanel = this.getVehicleInspectionPanel();
        var checklists = this.getChecklistForms();
        var fieldValues = [];
        var gpsContainer = vehicleInspectionPanel.down('container[cls=sfa-vehicle-inspection-gps]');
        var location = '';
        if (gpsContainer.down('togglefield').getValue() && gpsContainer.gps) {
            var gps = gpsContainer.gps;
            location = gps.getLatitude() + ';' + gps.getLongitude();
        }
        Ext.each(vehicleInspectionPanel.query('container[name=alert-container]'), function (alertContaienr) {
            var alert = alertContaienr.config.alertModel;
            alerts.push({
                fieldId: parseInt(alert.get('fieldId')),
                comment: alert.get('comment'),
                images: alert.get('photos')
            });
        });
        Ext.each(checklists, function (checklist) {
            var fields = checklist.query('field'); 
            Ext.each(fields, function (field) {
                if (field.isHidden()) {
                    return;

                }
                switch(field.xtype) {
                    case 'checkboxfield':
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

        var data = {
            date: Date.now(),
            fields: fieldValues,
            alerts: alerts,
            gps: location
        };

        var navMain = this.getNavMain();

        SafeStartApp.AJAX('vehicle/' + vehicleInspectionPanel.vehicleId + '/completechecklist', data, function (result) {
            vehicleInspectionPanel.down('sheet[cls=sfa-messagebox-confirm]').destroy();
            window.testMsg = vehicleInspectionPanel.down('sheet[cls=sfa-messagebox-confirm]');
            setTimeout(function () {
                var hash = result.checklist;
                var vehicleId = navMain.getActiveItem().getSelection()[0].parentNode.get('id');
                navMain.getStore().on('load', function () {
                    var inspectionsNode = navMain.getStore().getRoot().findChild('id', vehicleId).findChild('action', 'inspections');
                    navMain.goToNode(inspectionsNode); 
                    inspectionNode = inspectionsNode.findChild('checkListHash', hash);
                    if (inspectionNode) {
                        var active = navMain.getActiveItem();
                        var index = inspectionsNode.indexOf(inspectionNode);
                        navMain.fireEvent('itemtap', navMain, active, index, null, inspectionNode);
                    }
                }, null, {single: true});

                controller.getNavMain().getStore().load();
            }, 0);
        });
    },

    showAlerts: function() {

    }

});
