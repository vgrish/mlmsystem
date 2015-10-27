mlmsystem.page.Client = function(config) {
	config = config || {};
	Ext.applyIf(config, {
		components: [{
			xtype: 'mlmsystem-panel-client',
			renderTo: 'mlmsystem-panel-client-div',
			baseCls: 'mlmsystem-formpanel'
		}]
	});
	mlmsystem.page.Client.superclass.constructor.call(this, config);
};
Ext.extend(mlmsystem.page.Client, MODx.Component);
Ext.reg('mlmsystem-page-client', mlmsystem.page.Client);

mlmsystem.panel.Client = function(config) {
	if (!config.class) {
		config.class = 'MlmSystemClient';
	}
	config = config || {};
	Ext.apply(config, {
		baseCls: 'modx-formpanel',
		layout: 'anchor',
		/*
		 stateful: true,
		 stateId: 'mlmsystem-panel-client',
		 stateEvents: ['tabchange'],
		 getState:function() {return {activeTab:this.items.indexOf(this.getActiveTab())};},
		 */
		hideMode: 'offclient',
		items: [{
			html: '<h2>' + _('mlmsystem') + ' :: ' + _('mlmsystem_clients') + '</h2>',
			cls: '',
			style: {
				margin: '15px 0'
			}
		}, {
			xtype: 'modx-tabs',
			defaults: {
				border: false,
				autoHeight: true
			},
			border: true,
			hideMode: 'offclient',
			items: [{
				title: _('mlmsystem_clients'),
				layout: 'anchor',
				items: [{
					html: _('mlmsystem_clients_intro'),
					cls: 'panel-desc'
				}, {
					xtype: 'mlmsystem-grid-client',
					class: config.class,
					cls: 'main-wrapper'
				}]
			}, {
				title: _('mlmsystem_statuses_client'),
				layout: 'anchor',
				items: [{
					html: _('mlmsystem_statuses_client_intro'),
					cls: 'panel-desc'
				}, {
					xtype: 'mlmsystem-grid-status',
					class: config.class,
					cls: 'main-wrapper'
				}]
			}, {
				title: _('mlmsystem_type_changes_client'),
				layout: 'anchor',
				items: [{
					html: _('mlmsystem_type_changes_client_intro'),
					cls: 'panel-desc'
				}, {
					xtype: 'mlmsystem-grid-type-change',
					class: config.class,
					cls: 'main-wrapper'
				}]
			}]
		}]
	});
	mlmsystem.panel.Client.superclass.constructor.call(this, config);
};
Ext.extend(mlmsystem.panel.Client, MODx.Panel);
Ext.reg('mlmsystem-panel-client', mlmsystem.panel.Client);
