//http://www.kenneth-truyers.net/2013/04/27/javascript-namespaces-and-modules/
var CORELABS = CORELABS || {};

// create a general purpose namespace method
// this will allow us to create namespace a bit easier
CORELABS.createNS = function (namespace) {
	var nsparts = namespace.split(".");
	var parent = CORELABS;

	// we want to be able to include or exclude the root namespace
	// So we strip it if it's in the namespace
	if (nsparts[0] === "CORELABS") {
		nsparts = nsparts.slice(1);
	}

	// loop through the parts and create
	// a nested namespace if necessary
	for (var i = 0; i < nsparts.length; i++) {
		var partname = nsparts[i];
		// check if the current parent already has
		// the namespace declared, if not create it
		if (typeof parent[partname] === "undefined") {
			parent[partname] = {};
		}
		// get a reference to the deepest element
		// in the hierarchy so far
		parent = parent[partname];
	}
	// the parent is now completely constructed
	// with empty namespaces and can be used.
	return parent;
};

// Create the namespace for products
CORELABS.createNS("CORELABS.MODEL.APPMODULES");

CORELABS.MODEL.APPMODULES.modulehandler = function(){
    // private variables
	var modules = new Object();

    // private methods
    var addModule = function(name,object){

		modules[name] = object;
    };

    var removeModule = function(name){

		if (modules.hasOwnProperty(name)) {

			modules[name].destroy();
			delete modules[name];
		}
    };

    var destroyModule = function(modulename){

	};

	var getModule = function(modulename)
	{
		if (modules.hasOwnProperty(modulename)) {
			return modules[modulename];
		}
		else
			return undefined;
	};

    // public API
    return {
        addModule: addModule,
        removeModule: removeModule,
        destroyModule: destroyModule,
        getModule: getModule
    };
};

//Create a global module handler object
var module_handler = new CORELABS.MODEL.APPMODULES.modulehandler();
