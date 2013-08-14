Ext.define('SafeStartApp.view.forms.CompanySettings', {
    extend: 'Ext.form.Panel',
    xtype: 'SafeStartCompanySettingsForm',
    config: {
        height: 300,
        scrollable: false,
        items: [
            {
                xtype: 'fieldset',
                title: 'Company Settings',
                instructions: 'You chan change company settings info above.',
                items: [
                    {
                        xtype: 'textfield',
                        label: 'Title',
                        name: 'title'
                    },
                    {
                        xtype: 'emailfield',
                        label: 'Responsible person email',
                        name: 'email'
                    }
                ]
            }
        ]
    }
});
