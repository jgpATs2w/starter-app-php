RPC = function(){
	this.request = {
		url : 'rpc/',
		file : null,
		method: null,
		args: null
	};
	this.success = function(){
		console.info( "success default" );
	};

	this.errorAjax = function( xhr ){
				var state= xhr.state();
				var msg= "error ajax: state: '"+state+"'";
				alert(msg);
        throw new Error(msg);

   };
	this.errorRemote = function( message ){
		 alert( "se ha producido un error en el servidor: " + message );
     console.error( "RPC default errorRemote:" + message );
   };
	this.errorProcessing = function( error, response ){
		 var alertMessage= "rpc.errorProcessing: ";
		 if(typeof error.message == "string" && error.message.indexOf("JSON")>0)
		 	alertMessage+= "la respuesta tiene un mensaje de error.";
		 else
		 	alertMessage+= "puede ser un error procesando la respuesta.";

		 var consoleMessage = "rpc[" + this.request.file  + this.request.method + "].errorProcessing:" + error.message +
     				" received: " + response.message;

		 alert(alertMessage);
     throw new Error(consoleMessage);
   };

   this.loadingShow = function(){
   		if( typeof $O != "undefined" && this.loading )
   			$O.fn.loadingShow( this.loadingText );
   };

   this.loadingHide = function(){
   		if( typeof $O != "undefined" && self.loading )
			$O.fn.loadingHide();
   };

	this.complete= function(){};

	this.loading = false;
	this.loadingText = null;

	this.post = function( args ){
		//this.request.url = args.url || this.request.url;
		this.request.file = args.file || this.request.file;
		this.request.method = args.method || this.request.method;
		this.request.args = args.args || this.request.args;

		this.success = args.success || this.success;
		this.errorAjax = args.errorAjax || this.errorAjax;
		this.errorRemote = args.errorRemote || this.errorRemote;
		this.complete = args.complete || this.complete;

		this.loading = args.loading || this.loading;
		this.loadingText = args.loadingText || this.loadingText;

		this.loadingShow();

		var self = this;
		var url = this.request.url + self.request.method;
		//if($C.debug)
			//url += "?debug";

		this.xhr = $.ajax({
            url: url,
            data : JSON.stringify( this.request ),
            type : "POST",
            headers : {
                "Accept": "application/json",
                "Content-type": "application/json",
                "Pragma": "no-cache",
                "Cache-Control": "no-store, no-cache, must-revalidate, post-check=0, pre-check=0",
                "Expires": 0,
                "Last-Modified": new Date(0),
                "If-Modified-Since": new Date(0)
            },
            success : function( response ){//TODO report messages of callback errors
		         	try{
		         		if( ! response || response == "" ){
		         			var msg = "empty response file: " + self.request.file + "; method: " + self.request.method + "; request: " + self.request;
		         			console.error( msg );
					   			self.logError( msg );
		         			return;
		         		}
								if(typeof response == "string")
			         		response = JSON.parse( response );

							   if ( response.result ){
							   		self.success( response );

							   }else{
							   		self.errorRemote( response.message );
							   		self.logError( "error remote " );
							   }

		         	}catch(e){
		         		self.errorProcessing( e, response );
					   		self.logError( "error processing " + e + ", con mensaje de servidor " + response.message );
		         	}

            },

            error: function( xhr ){
            	self.loadingHide();
            	self.errorAjax( xhr );
            },

            complete: function(){
            	self.loadingHide();

            	self.complete();
            }

        });
};

this.get = function( args ){
	args.args = null;
	this.post( args );
};

this.testEmptyResponse = function(){

	this.post({
		args: null,
		method: "testEmptyResponse"
	});
};

this.logError = function( message ){};

};
