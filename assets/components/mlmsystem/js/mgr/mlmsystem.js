var mlmsystem = function(config) {
	config = config || {};
	mlmsystem.superclass.constructor.call(this, config);
};
Ext.extend(mlmsystem, Ext.Component, {
	page: {},
	window: {},
	grid: {},
	tree: {},
	panel: {},
	combo: {},
	config: {},
	view: {},
	utils: {}
});
Ext.reg('mlmsystem', mlmsystem);

mlmsystem = new mlmsystem();
