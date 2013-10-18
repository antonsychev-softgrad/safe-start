Ext.define('SafeStartExt.view.component.Contact', {
    extend: 'Ext.container.Container',
    requires: [
        'Ext.form.Panel' 
    ],
    xtype: 'SafeStartExtComponentContact',

    layout: {
        type: 'vbox',
        pack: 'center',
        align: 'center'
    },

    initComponent: function () {
        Ext.apply(this, {
            items: [{
                xtype: 'form',
                ui: 'form',
                width: 450,
                height: 500,
                title: 'Contanct Us',
                defaults: {
                    labelAlign: 'top',
                    margin: '0 0 20 0',
                    width: '100%'
                },
                buttonAlign: 'left',
                buttons: [{
                    text: 'Send',
                    scale: 'large',
                    handler: function () {
                        if (!(this.down('form').getForm().isValid())) {
                        }
                    },
                    scope: this
                }],
                items: [{
                    xtype: 'textfield',
                    height: 56,
                    labelAlign: 'top',
                    allowBlank: false,
                    fieldLabel: 'Name'
                }, {
                    xtype: 'textfield',
                    height: 56,
                    labelAlign: 'top',
                    allowBlank: false,
                    vtype: 'email',
                    fieldLabel: 'Email'
                }, {
                    xtype: 'textarea',
                    height: 140,
                    labelAlign: 'top',
                    allowBlank: false,
                    required: true,
                    fieldLabel: 'Message'
                }]
            }]
        });

        this.callParent();
    }
});
