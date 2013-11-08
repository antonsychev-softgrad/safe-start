Ext.define('SafeStartExt.view.form.UserProfile', {
	extend: 'Ext.form.Panel',
	requires: [
		'Ext.form.field.Text',
		'Ext.form.FieldSet'
	],
	xtype: 'SafeStartExtUserProfileForm',
	minWidth: 400,
	scrollable: false,

	initComponent: function() {
		Ext.apply(this, {
			items: [{
				xtype: 'fieldcontainer',
				fieldLabel: 'Personal Info',
				cls: 'sfa-field-group',
				labelCls: 'sfa-field-group-label',
				labelAlign: 'top',
				layout: {
					type: 'vbox',
					align: 'stretch'
				},
				maxWidth: 400,
				items: [{
					xtype: 'textfield',
					fieldLabel: 'First Name',
					labelWidth: 130,
					labelSeparator: '',
					name: 'firstName'
				}, {
					xtype: 'textfield',
					fieldLabel: 'Last Name',
					labelWidth: 130,
					labelSeparator: '',
					name: 'lastName'
				}, {
					xtype: 'textfield',
					vtype: 'email',
					labelWidth: 130,
					labelSeparator: '',
					fieldLabel: 'Email',
					name: 'email'
				}]
			}, {
				xtype: 'fieldcontainer',
				fieldLabel: 'Change password',
				cls: 'sfa-field-group',
				labelCls: 'sfa-field-group-label',
				labelAlign: 'top',
				layout: {
					type: 'vbox',
					align: 'stretch'
				},
				maxWidth: 400,
				items: [{
					xtype: 'textfield',
					name: 'newPassword',
					labelWidth: 130,
					labelSeparator: '',
					fieldLabel: 'New'
				}, {
					xtype: 'textfield',
					name: 'confirmPassword',
					labelWidth: 130,
					labelSeparator: '',
					fieldLabel: 'Confirm'
				}]
			}]
		});
		this.callParent();

		this.loadRecord(this.record);
	}
});