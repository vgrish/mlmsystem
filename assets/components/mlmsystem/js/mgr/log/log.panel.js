mlmsystem.page.Log = function(config) {
	config = config || {};
	Ext.applyIf(config, {
		components: [{
			xtype: 'mlmsystem-panel-log',
			renderTo: 'mlmsystem-panel-log-div',
			baseCls: 'mlmsystem-formpanel'
		}]
	});
	mlmsystem.page.Log.superclass.constructor.call(this, config);
};
Ext.extend(mlmsystem.page.Log, MODx.Component);
Ext.reg('mlmsystem-page-log', mlmsystem.page.Log);

mlmsystem.panel.Log = function(config) {
	if (!config.class) {
		config.class = '';
	}
	config = config || {};
	Ext.apply(config, {
		baseCls: 'modx-formpanel',
		layout: 'anchor',
		/*
		 stateful: true,
		 stateId: 'mlmsystem-panel-log',
		 stateEvents: ['tabchange'],
		 getState:function() {return {activeTab:this.items.indexOf(this.getActiveTab())};},
		 */
		hideMode: 'offclient',
		items: [{
			html: '<h2>' + _('mlmsystem') + ' :: ' + _('mlmsystem_logs') + '</h2>',
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
				title: _('mlmsystem_logs'),
				layout: 'anchor',
				items: [{
					html: _('mlmsystem_logs_intro'),
					cls: 'panel-desc'
				}, {
					xtype: 'mlmsystem-grid-log',
					class: config.class,
					cls: 'main-wrapper'
				}]
			}]
		}]
	});
	mlmsystem.panel.Log.superclass.constructor.call(this, config);
};
Ext.extend(mlmsystem.panel.Log, MODx.Panel);
Ext.reg('mlmsystem-panel-log', mlmsystem.panel.Log);
