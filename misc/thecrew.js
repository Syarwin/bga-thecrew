        setup: function( gamedatas )
        {
            dojo.query('.playertable' ).connect( 'onclick', this, 'onPickCrew');
        },


        ///////////////////////////////////////////////////
        //// Game & client states

        // onEnteringState: this method is called each time we are entering into a new game state.
        //                  You can use this method to perform some user interface changes at this moment.
        //
        onEnteringState: function( stateName, args )
        {
        	this.stateName = stateName;
    		dojo.query("#playertable_central").removeClass("hidden");
    		dojo.query("#tasks").addClass("hidden");
    		dojo.query("#endPanel").addClass("hidden");

            switch( stateName )
            {
            case 'multiSelect':
        		this.showTasks(args.args.tasks);
        		this.multiSelect = args.args.ids;
                this.selected = null;

                dojo.query('.task_marker' ).connect( 'onclick', this, 'onMultiSelect');
    			dojo.query("#tasklists .taskontable").connect('onclick', this, 'onMultiSelect');

        		if(this.isCurrentPlayerActive())
        		{
            		for( var id in args.args.ids )
                    {
        				dojo.query("#"+id).addClass("selectable");
                    }
        		}
            	break;

            case 'dummmy':
                break;
            }
        },

        // onLeavingState: this method is called each time we are leaving a game state.
        //                 You can use this method to perform some user interface changes at this moment.
        //
        onLeavingState: function( stateName )
        {

            switch( stateName )
            {

            case 'commToken':
                debugger;
            	dojo.query(".radio_temp").forEach(dojo.destroy);
                break;
            case 'pickCrew':
            	dojo.query(".discussion_bubble").addClass("hidden");
            	break;
            case 'playerTurn':
    			dojo.query("#distress").removeClass("selectable");
            	dojo.query(".discussion_bubble").addClass("hidden");
                break;

            case 'dummmy':
                break;
            }
        },

        // onUpdateActionButtons: in this method you can manage "action buttons" that are displayed in the
        //                        action status bar (ie: the HTML links in the status bar).
        //
        onUpdateActionButtons: function( stateName, args )
        {

            if( this.isCurrentPlayerActive() )
            {
                switch( stateName )
                {
	            case "multiSelect":
            		this.addActionButton('cancel_button', _("No move") , 'onCancel');
            		break;
                }
            }
        },


        onCancel: function (event) {
            dojo.stopEvent( event );
            if( this.checkAction( "actCancel" ) ) {
        		dojo.query(".selectable").removeClass("selectable");
                this.ajaxcall('/thecrew/thecrew/actCancel.html', {
                    lock:true,
                },this, function( result ) {}, function( is_error ) { } );
            }
        },

        onMultiSelect:function(event)
        {
        	dojo.stopEvent( event );
            if(this.isCurrentPlayerActive() && this.stateName == "multiSelect" )
            {
            	if(event.currentTarget.classList.contains('selected'))
            	{
            		this.selected = null;
                    dojo.query(".selected").removeClass("selected");
                    for( var id in this.multiSelect )
                    {
        				dojo.query("#"+id).addClass("selectable");
                    }
            	}
            	else if(event.currentTarget.classList.contains('selectable') && this.checkAction( "actMultiSelect" ) ) {

            			var id = event.currentTarget.id
	                    dojo.query(".selectable").removeClass("selectable");

	                    if(this.selected == null)
	                    {
	                    	this.selected = id;
		                    dojo.query("#"+id).addClass("selected");

		                    for( var id in this.multiSelect[this.selected] )
		                    {
		        				dojo.query("#"+id).addClass("selectable");
		                    }
	                    }
	                    else
	                    {
		                    dojo.query(".selected").removeClass("selected");
		                    dojo.query(".selectable").removeClass("selectable");
		            		this.ajaxcall('/thecrew/thecrew/actMultiSelect.html', {
			 	                   lock:true,
				 	                  id1:this.selected,
				 	                  id2:id,
			 	                },this, function( result ) {
			 	                }, function( is_error ) { } );

	                    }
            	}
            }
        },




        setupNotifications: function()
        {
            this.notifqueue.setSynchronous('move', 1000);
        },


        notif_move: function(notif) {

            this.attachToNewParent( notif.args.item_id, notif.args.location_id);
            this.createTaskTooltip(notif.args.task);

            dojo.animateProperty({
	       	    node: notif.args.item_id,
	       	    duration: 1000,
	            easing: dojo.fx.easing.expoInOut,
	       	    properties: {
	               left: 13,
	               bottom: 4,
	       	    }

	       	  }).play();
        },


        notif_distress: function(notif) {
       		dojo.query(".selectable").removeClass("selectable");
        	dojo.query("#distress").addClass("activated");
        	this.attempts_counter.incValue(1);
        	this.total_attempts_counter.incValue(1);
        },


        notif_updatePlayerScore: function(notif) {
            // Update the score
            this.scoreCtrl[notif.args.player_id].incValue(notif.args.points);
        },

        notif_newTurn : function(notif) {
            this.turn_counter.incValue(1);
        },
   });
});
