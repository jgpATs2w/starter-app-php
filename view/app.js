$(document).ready( function(){
	$App.fn.init();
});

$App = {
	watch: {},
	node: {
		//reference to objects
	},
	fn: {
		init: function(){

		},

		
	},
	rpc: {
		create: function(codigo, aid, success){

			var rpc = new RPC();
			rpc.post( {
				method: "gpo/create",
				args: {
					"codigo": codigo,
					"aid": aid
				},
				success : function( response ){
					success( response );
				}
			});

		},
		delete: function(name, success){

			var rpc = new RPC();
			rpc.post( {
				method: "gpo/delete",
				args: {
					"name": name
				},
				success : function( response ){
					success( response );
				}
			});

		},
		generate: function(parameters, success){

			var rpc = new RPC();
			rpc.post( {
				method: "gpo/generate",
				args: parameters,
				success : function( response ){
					success( response );
				}
			});

		}
	}
};
