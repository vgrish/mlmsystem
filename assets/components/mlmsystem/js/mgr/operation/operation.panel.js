mlmsystem.page.Operation = function(config) {
	config = config || {};
	Ext.applyIf(config, {
		components: [{
			xtype: 'mlmsystem-panel-operation',
			renderTo: 'mlmsystem-panel-operation-div',
			cls: 'mlmsystem-formpanel'
		}]
	});
	mlmsystem.page.Operation.superclass.constructor.call(this, config);
};
Ext.extend(mlmsystem.page.Operation, MODx.Component);
Ext.reg('mlmsystem-page-operation', mlmsystem.page.Operation);

mlmsystem.panel.Operation = function(config) {
	if (!config.class) {
		config.class = 'MlmSystemOperation';
	}
	config = config || {};
	Ext.apply(config, {
		baseCls: 'modx-formpanel',
		layout: 'anchor',
		/*
		 stateful: true,
		 stateId: 'mlmsystem-panel-operation',
		 stateEvents: ['tabchange'],
		 getState:function() {return {activeTab:this.items.indexOf(this.getActiveTab())};},
		 */
		hideMode: 'offclient',
		items: [{
			html: '<h2>' + _('mlmsystem') + ' :: ' + _('mlmsystem_operations') + '</h2>',
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
				title: _('mlmsystem_operations'),
				layout: 'anchor',
				items: [{
					html: _('mlmsystem_operations_intro'),
					cls: 'panel-desc'
				}, {
					//xtype: 'mlmsystem-grid-operation',
					class: config.class,
					cls: 'main-wrapper'
				}]
			}, {
				title: _('mlmsystem_types_operation'),
				layout: 'anchor',
				items: [{
					html: _('mlmsystem_types_operation_intro'),
					cls: 'panel-desc'
				}, {
					//xtype: 'mlmsystem-grid-type',
					class: config.class,
					cls: 'main-wrapper'
				}]
			}]
		}]
	});
	mlmsystem.panel.Operation.superclass.constructor.call(this, config);
};
Ext.extend(mlmsystem.panel.Operation, MODx.Panel);
Ext.reg('mlmsystem-panel-operation', mlmsystem.panel.Operation);
