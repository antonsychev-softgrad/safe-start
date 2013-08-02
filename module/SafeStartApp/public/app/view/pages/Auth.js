Ext.define('SafeStartApp.view.pages.Auth', {
    extend: 'Ext.Container',
    require: [
        'SafeStartApp.form.Auth'
    ],

    xtype: 'SafeStartAuthPage',

    config: {
        title: 'Welcome',
        iconCls: 'home',

        layout: 'auto',
        styleHtmlContent: false,
        scrollable: true
    },


    initialize: function () {
        this.callParent();

        this.AuthForm = this.add(Ext.create('SafeStartApp.form.Auth'));

     /*   var myPanel = Ext.create('Ext.Panel', {
         items: [
         {
         xtype: 'fieldset',
         title: 'Contact Us',
         instructions: '(email address is optional)',
         items: [
         {
         xtype: 'textfield',
         label: 'Name'
         },
         {
         xtype: 'emailfield',
         label: 'Email'
         }
         ]
         },
         {
         xtype: 'button',
         text: 'Send',
         ui: 'confirm'
         }
         ]
         });

         this.add(myPanel);*/
    }

});
