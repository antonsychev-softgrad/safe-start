Ext.define('SafeStartApp.controller.CompanyVehicles', {
    extend: 'Ext.app.Controller',
    
    mixins: ['SafeStartApp.controller.mixins.Form'],

    requires: [
        'SafeStartApp.view.components.UpdateChecklist',
        'SafeStartApp.store.VehicleChecklist',
        'SafeStartApp.model.MenuVehicle',
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
                editInspection: 'onEditInspectionAction',
                deleteInspection: 'onDeleteInspectionAction'
            },
            reviewCard: {
                activate: 'onActivateReviewCard'
            },
            navMain: {
                selectAction: 'onSelectAction',
                selectVehicle: 'onSelectVehicle',
                back: 'hideSelectionAction'
            },
            addButton: {
                tap: 'addAction'
            }
        },

        refs: {
            mainToolbar: 'SafeStartCompanyPage SafeStartCompanyToolbar',
            navMain: 'SafeStartCompanyPage SafeStartNestedListVehicles',
            infoPanel: 'SafeStartCompanyPage > panel[name=info-container]',
            vehicleInfoPanel: 'SafeStartCompanyPage > panel[name=info-container] > panel[name=vehicle-info]',
            vehicleInspectionPanel: 'SafeStartCompanyPage SafeStartVehicleInspection',
            vehicleUsersPanel: 'SafeStartCompanyPage SafeStartVehicleUsersPanel',
            vehicleAlertsPanel: 'SafeStartCompanyPage SafeStartVehicleAlertsPanel',
            vehicleReportPanel: 'SafeStartCompanyPage SafeStartVehicleReportPanel',
            vehicleInspectionDetailsPanel: 'SafeStartCompanyPage SafeStartVehicleInspectionDetails',
            vehicleInspectionsPanel: 'SafeStartCompanyPage SafeStartVehicleInspectionsPanel',
            vehiclesPanel: 'SafeStartCompanyPage SafeStartVehiclesPanel',
            addButton: 'SafeStartCompanyPage SafeStartCompanyToolbar > button[action=add-vehicle]',
            updateChecklistPanel: 'SafeStartCompanyPage SafeStartUpdateVehicleChecklistPanel',
            reviewCard: 'SafeStartCompanyPage SafeStartVehicleInspection formpanel[name=checklist-card-review]'
        }
    },

    onSelectAction: function (record, silent) {
        this.selectedRecord = this.getNavMain().getActiveItem().getStore().getNode();
        this.selectedNodeId = record.get('id');

        var button = null;
        if (Ext.os.deviceType !== 'Desktop') {
            button = this.getMainToolbar().down('button[action=toggle-menu]');
            if (button && button.config.isPressed && ! silent) {
                // console.log(button.config.isPressed);
                button.getHandler().call(button, button);
            }
        }

        switch (record.get('action')) {
            case 'info':
                this.getInfoPanel().setActiveItem(this.getVehicleInfoPanel());
                this.showUpdateForm(record.parentNode);
                break;
            case 'fill-checklist':
                this.getInfoPanel().setActiveItem(this.getVehicleInspectionPanel());
                this.loadChecklist(record.parentNode.get('id'));
                break;
            case 'inspections':
                this.getInfoPanel().setActiveItem(this.getVehicleInspectionsPanel());
                this.getVehicleInspectionsPanel().loadList(record.parentNode);
                break;
            case 'report':
                this.getInfoPanel().setActiveItem(this.getVehicleReportPanel());
                this.getVehicleReportPanel().loadData(record.parentNode);
                break;
            case 'alerts':
                this.getInfoPanel().setActiveItem(this.getVehicleAlertsPanel());
                this.getVehicleAlertsPanel().loadList(record.parentNode.get('id'));
                break;
            case 'update-checklist':
                this.getInfoPanel().setActiveItem(this.getUpdateChecklistPanel());
                this.getUpdateChecklistPanel().setVehicleId(record.parentNode.get('id'));
                break;
            case 'users':
                this.loadUsers(record.parentNode.get('id'));
                this.getInfoPanel().setActiveItem(this.getVehicleUsersPanel());
                break;
        }
    },

    onSelectVehicle: function (record, silent) {
        this.getNavMain().tapOnActionNode('info', record.get('id'), silent);
    },

    hideSelectionAction: function() {
        this.getInfoPanel().setActiveItem(10);
    },

    checklistStores: {},
    checkListTrees: {},

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

    onDeleteInspectionAction: function (vehicleId, checkListId) {
        var navMain = this.getNavMain();
        SafeStartApp.AJAX('vehicle/inspection/' + checkListId + '/delete', {}, function (result) {
            navMain.tapOnActionNode('inspections', vehicleId, true);
        });
    },

    loadAlerts: function (checkListId) {
        var me = this;
        SafeStartApp.AJAX('vehicle/inspection/' + checkListId + '/alerts', {}, function (result) {
            me.getVehicleInspectionPanel().fillAlertsData(result);
        });
    },

    showUpdateForm: function (selectedRecord) {
        if (!this.currentForm) {
            this._createForm();
        }
        this.currentForm.setRecord(selectedRecord);
        if (SafeStartApp.userModel.get('role') !== 'companyUser') {
            this.currentForm.down('button[name=delete-data]').show();
        }
        this.currentForm.down('button[name=reset-data]').hide();
    },

    addAction: function () {
        this.getInfoPanel().setActiveItem(0);
        this.getNavMain().goToNode(this.getNavMain().getStore().getRoot());
        this.selectedNodeId = 0;   
        this._createForm();
        if (this.vehicleModel) this.vehicleModel.destroy();
        this.vehicleModel = new SafeStartApp.model.MenuVehicle();
        this.currentForm.setRecord(this.vehicleModel);
        this.currentForm.down('button[name=delete-data]').hide();
        this.currentForm.down('button[name=reset-data]').show();
    },

    saveAction: function () {
        if (!this.vehicleModel) {
            this.vehicleModel = Ext.create('SafeStartApp.model.MenuVehicle');
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
                    self.currentForm.down('hiddenfield[name=id]').setValue(result.vehicleId);
                    if (SafeStartApp.userModel.get('role') !== 'companyUser') {
                        self.currentForm.down('button[name=delete-data]').show();
                    }
                    self.currentForm.down('button[name=reset-data]').hide();
                }
            });
        }
    },

    deleteAction: function () {
        var self = this;
        Ext.Msg.confirm("Confirmation", "Are you sure you want to delete this vehicle?", function (buttonId) {
            if (buttonId === 'yes') {
                SafeStartApp.AJAX('vehicle/' + self.currentForm.getValues().id + '/delete', {}, function (result) {
                    self.getNavMain().getVehiclesStore().loadData();
                    self.currentForm.reset();
                    self.currentForm.down('button[name=delete-data]').hide();
                    self.currentForm.down('button[name=reset-data]').show();
                    self.getInfoPanel().setActiveItem(self.getVehiclesPanel());
                });
            }
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
        this.getNavMain().getVehiclesStore().addListener('load', function () {
            var vehicleNode = this.getNavMain().getStore().getRoot().findChild('id', vehicleId);
            if (vehicleNode) {
                this.getNavMain().tapOnActionNode('info', vehicleId, true);
                this.currentForm.setRecord(vehicleNode);
            }
        }, this, {single: true, order: 'after'});

        this.getNavMain().getVehiclesStore().loadData();
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
            var vehicleId = vehicleInspectionPanel.vehicleId;
            inspectionsPanel.inspectionsStore.on({
                load: function (store, records) {
                    var record = store.findRecord('hash', result.checklist);
                    inspectionsPanel.loadChecklistDetails(record);
                },
                single: true
            });
            navMain.tapOnActionNode('inspections', null, true);
        });
    }
});