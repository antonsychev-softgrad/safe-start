Ext.define('SafeStartExt.view.panel.CompanyOtherUsers', {
    extend: 'Ext.form.Panel',
    xtype: 'SafeStartExtPanelCompanyOtherUsers',
    requires: [],
    layout: {
        type: 'vbox',
        align: 'stretch'
    },
    ui: 'transparent',
    width: '100%',
    autoScroll: true,
    name: 'company-users',
    title: 'Automatically Send Email',
    initComponent: function () {
        Ext.apply(this, {
            items: [{
                xtype: 'container',
                flex: 1,
                padding: 20,
                cls: 'sfa-info-container',
                layout: {
                    type: 'fit'
                },
                items: [{
                    xtype: 'panel',
                    ui: 'transparent',
                    autoScroll: true,
                    buttonAlign: 'left',
                    border: 0,
                    padding: '10 20',
                    layout: {
                        type: 'vbox',
                        align: 'stretch'
                    },
                    bbar: {
                        xtype: 'container',
                        layout: {
                            type: 'vbox'
                        },
                        maxWidth: 400,
                        padding: '10 0 0',
                        items: [{
                            xtype: 'button',
                            text: 'Save',
                            scale: 'medium',
                            ui: 'blue',
                            scope: this,
                            handler: function () {
                                var me = this;
                                SafeStartExt.Ajax.request({
                                    url: 'company/' + me.getRecord().getId() + '/update-other-users',
                                    data: {
                                        other_users: me.getValues()
                                    },
                                    success: function () {
                                        me.getRecord().set('other_users', me.getValues());
                                    }
                                });
                            }
                        }]
                    },
                    listeners: {
                        afterrender: function () {
                            this.loadRecord(SafeStartExt.getApplication().getCompanyRecord());
                            this.buildList();
                        },
                        scope: this
                    },
                    items: [{
                        xtype: 'fieldcontainer',
                        layout: 'hbox',
                        fieldDefaults: {
                            msgTarget: 'side'
                        },
                        items: [{
                            xtype: 'textfield',
                            vtype: 'email',
                            fieldLabel: 'User email',
                            allowBlank: false,
                            name: 'email',
                            emptyText: 'user@example.com',
                            value: '',
                            listeners: {
                                focus: function() {
                                    if('' === this.getValue()) {
                                        this.reset();
                                    }
                                }
                            }
                        }, {
                            xtype: 'button',
                            text: 'Add',
                            ui: 'blue',
                            name: 'add',
                            scale: 'medium',
                            margin: '0 0 0 5',
                            minWidth: '117',
                            scope: this,
                            handler: function () {
                                var field = this.down('textfield[name=email]');
                                var value = field.getValue();
                                if(this.isValid()) {
                                    if(this.isUniqueValue(value)) {
                                        field.setValue('');
                                        field.reset();
                                        this.onAddClick(value);
                                    } else {
                                        Ext.Msg.alert({
                                            minWidth: 290,
                                            title: 'Warning',
                                            msg: 'This email already exists in a list of users!',
                                            buttons: Ext.Msg.OK,
                                            fn: function (btn) {
                                                return false;
                                            }
                                        });
                                    }
                                }
                            }
                        }]
                    }, {
                        xtype: 'container',
                        layout: 'vbox',
                        name: 'user-container',
                        margin: '10 0'
                    }]
                }]
            }]
        });

        this.callParent();
    },

    isUniqueValue: function (value) {
        var values = this.getValues();
        if(Ext.Array.contains(values, Ext.util.Format.lowercase(value))) {
            return false;
        }

        return true;
    },

    onAddClick: function(value) {
        var me = this;
        var container = this.down('container[name=user-container]');
        container.add({
            xtype: 'fieldcontainer',
            layout: 'hbox',
            items: [{
                xtype: 'textfield',
                vtype: 'email',
                name: 'item',
                fieldLabel: 'User email:',
                value: value,
                readOnly: true,
                disabled: true,
                allowBlank: false
            }, {
                xtype: 'button',
                text: 'Delete',
                ui: 'red',
                name: 'delete',
                scale: 'medium',
                margin: '0 0 0 5',
                handler: function () {
                    me.onRemoveClick(this);
                }
            }]
        });
    },

    onRemoveClick: function(item) {
        var container = this.down('container[name=user-container]');
        container.remove(item.up(), true);
    },

    buildList: function () {
        var container = this.down('container[name=user-container]');
        container.removeAll();
        Ext.each(this.getRecord().get('other_users'), function (item) {
            this.onAddClick(item);
        }, this);
    },

    getValues: function () {
        var values = [];    
        Ext.each(this.query('textfield[name=item]'), function (field) {
            var value = field.getValue();
            if(!Ext.isEmpty(value)) {
                values.push(Ext.util.Format.lowercase(value));
            }
        });
        return values;
    }

});
