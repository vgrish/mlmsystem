mlmsystem.page.Story = function(config) {
	config = config || {};
	Ext.applyIf(config, {
		components: [{
			xtype: 'mlmsystem-panel-story',
			renderTo: 'mlmsystem-panel-story-div',
			baseCls: 'mlmsystem-formpanel'
		}]
	});
	mlmsystem.page.Story.superclass.constructor.call(this, config);
};
Ext.extend(mlmsystem.page.Story, MODx.Component);
Ext.reg('mlmsystem-page-story', mlmsystem.page.Story);

mlmsystem.panel.Story = function(config) {
	if (!config.class) {
		config.class = '';
	}
	config = config || {};
	Ext.apply(config, {
		baseCls: 'modx-formpanel',
		layout: 'anchor',
		/*
		 stateful: true,
		 stateId: 'mlmsystem-panel-story',
		 stateEvents: ['tabchange'],
		 getState:function() {return {activeTab:this.items.indexOf(this.getActiveTab())};},
		 */
		hideMode: 'offclient',
		items: [{
			html: '<h2>' + _('mlmsystem') + ' :: ' + _('mlmsystem_stories') + '</h2>',
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
				title: _('mlmsystem_stories'),
				layout: 'anchor',
				items: [{
					html: _('mlmsystem_stories_intro'),
					cls: 'panel-desc'
				}, {
					xtype: 'mlmsystem-grid-story',
					class: config.class,
					cls: 'main-wrapper'
				}]
			}]
		}]
	});
	mlmsystem.panel.Story.superclass.constructor.call(this, config);
};
Ext.extend(mlmsystem.panel.Story, MODx.Panel);
Ext.reg('mlmsystem-panel-story', mlmsystem.panel.Story);
