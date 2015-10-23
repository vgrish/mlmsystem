mlmsystem.page.Profit = function(config) {
	config = config || {};
	Ext.applyIf(config, {
		components: [{
			xtype: 'mlmsystem-panel-profit',
			renderTo: 'mlmsystem-panel-profit-div',
			baseCls: 'mlmsystem-formpanel'
		}]
	});
	mlmsystem.page.Profit.superclass.constructor.call(this, config);
};
Ext.extend(mlmsystem.page.Profit, MODx.Component);
Ext.reg('mlmsystem-page-profit', mlmsystem.page.Profit);

mlmsystem.panel.Profit = function(config) {
	if (!config.class) {
		config.class = '';
	}
	config = config || {};
	Ext.apply(config, {
		baseCls: 'modx-formpanel',
		layout: 'anchor',
		/*
		 stateful: true,
		 stateId: 'mlmsystem-panel-profit',
		 stateEvents: ['tabchange'],
		 getState:function() {return {activeTab:this.items.indexOf(this.getActiveTab())};},
		 */
		hideMode: 'offclient',
		items: [{
			html: '<h2>' + _('mlmsystem') + ' :: ' + _('mlmsystem_profit') + '</h2>',
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
				title: _('mlmsystem_profit'),
				layout: 'anchor',
				items: [{
					html: _('mlmsystem_profit_intro'),
					cls: 'panel-desc'
				}, {
					xtype: 'mlmsystem-grid-profit',
					class: config.class,
					cls: 'main-wrapper'
				}]
			}]
		}]
	});
	mlmsystem.panel.Profit.superclass.constructor.call(this, config);
};
Ext.extend(mlmsystem.panel.Profit, MODx.Panel);
Ext.reg('mlmsystem-panel-profit', mlmsystem.panel.Profit);
