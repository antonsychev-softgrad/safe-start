Ext.define('SafeStartExt.view.form.UserProfile', {
	extend: 'Ext.form.Panel',
	requires: [
		'Ext.form.field.Text',
		'Ext.form.FieldSet'
	],
	xtype: 'SafeStartExtUserProfileForm',
	config: {
		height: 430,
		scrollable: false,
		items: [
			{
				xtype: 'fieldset',
				title: 'Personal Info',
				/*    instructions: 'You can change your info above.',*/
				items: [
					{
						xtype: 'textfield',
						fieldLabel: 'First Name',
						name: 'firstName'
					},
					{
						xtype: 'textfield',
						fieldLabel: 'Last Name',
						name: 'lastName'
					},
					{
						xtype: 'textfield',
						vtype: 'email',
						fieldLabel: 'Email',
						name: 'email'
					},
					{
						xtype: 'fieldset',
						title: 'Change password:',
						items: [
							{
								xtype: 'textfield',
								name: 'newPassword',
								fieldLabel: 'New'
							},
							{
								xtype: 'textfield',
								name: 'confirmPassword',
								fieldLabel: 'Confirm'
							}
						]
					}
				]
			}
		]
	}
});
