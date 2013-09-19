Ext.define('SafeStartApp.view.pages.panel.VehicleInspectionDetails', {
    extend: 'Ext.Panel',

    alias: 'widget.SafeStartVehicleInspectionDetails',

    requires: [

    ],

    config: {
        layout: 'card',
        margin: 10,
        items: [{
            xtype: 'panel',
            name: 'vehicle-inspection-details',
            cls: 'sfa-vehicle-inspection-details',
            scrollable: true,
            layout: {
                type: 'vbox',
                align: 'center',
                pack: 'middle'
            }
        }, {
            xtype: 'panel',
            cls: 'sfa-vehicle-inspection-details-map', 
            layout: 'fit',
            items: [{
                xtype: 'button',
                top: 20,
                left: 80,
                text: 'Back',
                handler: function (btn) {
                    var panel = btn.up('SafeStartVehicleInspectionDetails');
                    panel.setActiveItem(0);
                }
            }]
        }]
    },

    initialize: function () {
        this.callParent();
    },

    loadChecklist: function (vehicle, inspection) {
        var me = this;
        SafeStartApp.AJAX('vehicle/' + inspection.get('checkListId') + '/getchecklistdata', {}, function (result) {
            me.createView(vehicle, result.checklist, inspection);
        }, true, true);
    },

    createView: function (vehicle, checklist, inspection) {
        var infoGroup = [],
            responsibleUser = vehicle.responsibleUsers().first(),
            cords;

        this.setActiveItem(0);
        this.down('panel[cls=sfa-vehicle-inspection-details]').removeAll();

        infoGroup.push(
            this.createContainer('Project number', vehicle.get('projectNumber')),
            this.createContainer('Project name', vehicle.get('projectName'))
        );
        if (responsibleUser) {
            infoGroup.push(this.createContainer('Operators name', responsibleUser.getFullName()));
        }
        infoGroup.push(this.createContainer('Date and Time', checklist.creationDate.date));
        if (checklist.gpsCoords) {
            cords = checklist.gpsCoords.split(';');
            infoGroup.push(this.createMapsContainer('Location', parseFloat(cords[0]), parseFloat(cords[1])));
        }
        this.createGroup(infoGroup);

        this.createGroup([
            this.createContainer('Plant ID', vehicle.get('plantId')),
            this.createContainer('Registration', vehicle.get('registration')),
            this.createContainer('Type of vehicle', vehicle.get('type'))
        ]);

        var serviceDueString = vehicle.get('serviceDueKm') + ' km '+ vehicle.get('serviceDueHours') + ' hours';
        var odometerString = '';
        if (inspection.get('odometerKms')) {
            odometerString += inspection.get('odometerKms') + ' km';
        }
        if (inspection.get('odometerHours')) {
            odometerString += ' ' + inspection.get('odometerHours') + ' hours';
        }
        this.createGroup([
            this.createContainer('Service due', serviceDueString),
            this.createContainer('Current odometer', odometerString)
        ]);

        this.createButtons(checklist.id);

        this.checkListId = checklist.id;
        this.vehicleId = vehicle.get('id');
        this.inspectionRecord = inspection;

        Ext.each(checklist.fieldsStructure, function (fieldGroup) {
            this.createFields(
                fieldGroup.fields, 
                checklist.fieldsData, 
                checklist.alerts, 
                fieldGroup.groupName, 
                1
            );
        }, this);
    },

    createContainer: function (key, value, alert) {
        var cls = alert ? 'sfa-vehicle-details-container-alert'
            : 'sfa-vehicle-details-container';
        return {
            xtype: 'container',
            cls: cls,
            layout: {
                type: 'hbox',
                align: 'left',
                pack: 'left'
            },
            width: '100%',
            maxWidth: 700,
            items: [{
                xtype: 'container',
                html: key
            }, {
                xtype: 'spacer',
                flex: 1
            }, {
                xtype: 'container',
                html: value
            }]
        };
    },

    createMapsContainer: function (title, lat, lon) {
        var me = this;
        return {
            xtype: 'container',
            cls: 'sfa-vehicle-inspection-details-container2',
            layout: {
                type: 'hbox',
                align: 'left',
                pack: 'left'
            },
            width: '100%',
            maxWidth: 700,
            items: [{
                xtype: 'container',
                html: title 
            }, {
                xtype: 'spacer',
                flex: 1
            }, {
                xtype: 'button',
                ui: 'small',
                text: 'Open map',
                handler: function (btn) {
                    var panel = me.down('panel[cls=sfa-vehicle-inspection-details-map]');
                    var position = new google.maps.LatLng(lat, lon);
                    var map = panel.down('map');
                    if (map) {
                        map.marker.setPosition(position);
                        map.getMap().setCenter(position);
                    } else {
                        panel.add({
                            xtype: 'map',
                            mapOptions: {
                                center: position
                            },
                            listeners: {
                                maprender: function (mapCmp) {
                                    mapCmp.marker = new google.maps.Marker({
                                        position: position,
                                        title: 'Vehicle Inspection',
                                        map: mapCmp.getMap()
                                    });
                                }
                            }
                        });
                    }
                    me.setActiveItem(1);
                }
            }]
        };
    },

    createGroup: function (items, title, depth) {
        if (title) {
            if (depth == 1) {
                items.unshift({
                    xtype: 'container',
                    cls: 'sfa-vehicle-inspection-details-title',
                    html: title
                });
            } else {
                items.unshift({
                    xtype: 'container',
                    cls: 'sfa-vehicle-inspection-details-subtitle',
                    html: title
                });
            }
        }
        var item = {
            xtype: 'container',
            margin: '0 0 10 0',
            layout: {
                type: 'vbox',
                align: 'center',
                pack: 'middle'
            },
            width: '100%',
            items: items
        };
        if (depth > 1) {
            return item;
        }
        this.down('panel[cls=sfa-vehicle-inspection-details]').add(item);
    },

    createFields: function (fields, values, alerts, title, depth) {
        var items = [];
        Ext.each(fields, function (field) {
            var isAlert = false;
            switch (field.type) {
                case 'group':
                    items.push(this.createFields(field.items, values, alerts, field.fieldName, depth + 1));
                    break;
                case 'radio':
                case 'text':
                case 'checkbox':
                    Ext.each(alerts, function (alert) {
                        if (alert.field.id == field.fieldId) {
                            isAlert = true;
                        }
                    }, this);
                    Ext.each(values, function (value) {
                        if (value.id == field.fieldId) {
                            items.push(this.createContainer(field.fieldName, value.value, isAlert));
                        }
                    }, this);
                    break;
                case 'datePicker':
                    Ext.each(values, function (value) {
                        if (value.id == field.fieldId) {
                            if (value.value) {
                                var date = new Date(value.value);
                                items.push(this.createContainer(field.fieldName, Ext.Date.format(date, 'Y-m-d')));
                            } else {
                                items.push(this.createContainer(field.fieldName, 'N/A'));
                            }
                        }
                    }, this);
                    break;
            }
        }, this);

        if (items.length) {
            return this.createGroup(items, title, depth);
        }
    },

    createButtons: function (checkListId) {
        this.down('panel[cls=sfa-vehicle-inspection-details]').add({
            xtype: 'toolbar',
            margin: '0 0 10 0',
            items: [{
                ui: 'confirm',
                action: 'print',
                checkListId: checkListId,
                text: 'Print'
            }]
        });
    }

});
