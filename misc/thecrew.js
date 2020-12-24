/**
 *------
 * BGA framework: Â© Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * thecrew implementation : Â© Nicolas Gocel <nicolas.gocel@gmail.com>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * thecrew.js
 *
 * thecrew user interface script
 *
 * In this file, you are describing the logic of your user interface, in Javascript language.
 *
 */

define([
    "dojo","dojo/_base/declare",
    "ebg/core/gamegui",
    "ebg/counter",
    "ebg/stock",
],
function (dojo, declare) {
    return declare("bgagame.thecrew", ebg.core.gamegui, {
        constructor: function(){

            // Here, you can init the global variables of your user interface
            // Example:
            this.commander_id = null;
            this.players = null;
            this.colors = null;


            // Number of turns
            this.mission_counter = null;
            this.attempts_counter = null;
            this.total_attempts_counter = null;
            this.trick_counters = {};
            this.cards_counters = {};
            this.multiSelect = null;
            this.selected = null;
            this.stateName = null;

        },

        /*
            setup:

            This method must set up the game user interface according to current game situation specified
            in parameters.

            The method is called each time the game interface is displayed to a player, ie:
            _ when the game starts
            _ when a player refreshes the game page (F5)

            "gamedatas" argument contains all datas retrieved by your "getAllDatas" PHP method.
        */

        setup: function( gamedatas )
        {

            this.players = gamedatas.players;
            this.colors = gamedatas.colors;

            // Number of turns


            // Setting up player boards
            for( var rel in ordered )
            {

                var cardontable =  player['comm'];
                if(cardontable  !== undefined)
                {
                	dojo.place( this.format_block('jstpl_cardontable', cardontable), $('comcard_'+player_id));
                	dojo.query('#card_' + cardontable['id']).connect('onclick', this, 'onPlayCard');
                    this.createCardTooltip('card_' + cardontable['id'], cardontable['type'], cardontable['type_arg']);
                }

                dojo.place(this.format_block('jstpl_player', {player_id:player_id}), 'player_board_' + player_id);


                dojo.removeClass('radio_' + player['id']);
                dojo.addClass('radio_' + player['id'], 'radio');
                dojo.addClass('radio_' + player['id'], player['comm_token']);

                this.addTooltipHtml( 'radio_' + player_id, this.format_block('jstpl_tooltip_common', {title: _('Radio communication token'), description:  _('Communication token gives information of the communicated color card :<br/>- At the top, if it is your highest card of this color.<br/>- In the middle, if it is your only card of this color.<br/>- At the bottom, if it is your lowest card of this color.<br/>- Red, you cannot communicate.')}));

            }


         // Cards in player's hand


            if(this.gamedatas.distress == 1)
            {
            	dojo.query("#distress").addClass("activated");
            }
            this.addTooltipHtml( 'distress', this.format_block('jstpl_tooltip_common', {title: _('Distress token'), description:  _('A distress signal can be sent out before the first trick of a mission and before any communication. If the distress signal is activated, each crew member may pass one card to his neighbor. Rockets may not be passed on!')}));

            dojo.query('#distress' ).connect( 'onclick', this, 'onDistress');
            dojo.query('.playertable' ).connect( 'onclick', this, 'onPickCrew');


            // Setup game notifications to handle (see "setupNotifications" method below)
            this.setupNotifications();

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

            	break;
            case 'comm':
    			dojo.query(".selectable").removeClass("selectable");
            	if(this.isCurrentPlayerActive())
        		{
	            	for( var card_id in args.args )
	                {
	        			dojo.query("#myhand_item_"+card_id).addClass("selectable");
	                }
        		}
            	break;

            case 'commToken':
            	if(this.isCurrentPlayerActive())
        		{
            		for( var status_id in args.args.possible )
	                {
                        var status = args.args.possible[status_id];
                        debugger;
                    	dojo.place( this.format_block('jstpl_temp_comm', {player_id: this.player_id , status:status}), $('comcard_'+this.player_id), 'first');
	                }

        			dojo.query(".radio_temp").connect('onclick', this, 'onFinishComm');
        			this.addTooltipHtmlToClass( "radio_temp", this.format_block('jstpl_tooltip_common', {title: _('Radio communication token'), description:  _('Communication token gives information of the communicated color card :<br/>- At the top, if it is your highest card of this color.<br/>- In the middle, if it is your only card of this color.<br/>- At the bottom, if it is your lowest card of this color.<br/>- Red, you cannot communicate.')}));

        		}
            	break;

            case 'distress':

            	if(this.isCurrentPlayerActive())
        		{
	            	for( var card_id in args.args.cards )
	                {
	        			dojo.query("#myhand_item_"+card_id).addClass("selectable");
	                }
        		}
                break;

            case 'question':
        		this.showTasks(args.args.tasks);
            	break;

            case 'pickCrew':
        		this.showTasks(args.args.tasks);
            	if(this.isCurrentPlayerActive())
        		{
            		for( var player_id in args.args.possible )
                    {
            			dojo.query("#playertable_"+player_id).addClass("selectable");
                    }
        		}
            	break;

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


		            case "question":
		            	var i = 0;
		            	var split = args.replies.split("/");
		            	for (i = 0; i < split.length; i++)
		            	{
			            	this.addActionButton(i+'_button', split[i] , 'onButtonChoose');
		            	}
		            	break;

		            case "distressSetup":
		            	this.addActionButton('left_button', _("Left") , 'onButtonChoose');
		            	this.addActionButton('right_button', _("Right") , 'onButtonChoose');
		            	this.addActionButton('no_button', _("No card passed") , 'onButtonChoose');
		            	break;
                }
            }
        },

        onButtonChoose:function(event)
        {
            dojo.stopEvent( event );
            if(this.isCurrentPlayerActive()&& this.checkAction( "actButton" ))
            {
            	$action = event.currentTarget.id.replace('_button','');

    			dojo.query(".finalbutton").addClass("hidden");

            	this.ajaxcall('/thecrew/thecrew/actButton.html', {
                    lock:true,
                    choice:$action,
                },this, function( result ) {
                }, function( is_error ) { } );
            }
       },



       onDistress: function (event) {
    	   dojo.stopEvent( event );

	       	if(event.currentTarget.classList.contains('selectable') ) {


	            this.confirmationDialog( _("Are you sure you want to launch a distress signal?"), dojo.hitch( this, function() {

	       		dojo.query(".selectable").removeClass("selectable");

	       		this.ajaxcall('/thecrew/thecrew/actDistress.html', {
		                   lock:true,
		                },this, function( result ) {
		                }, function( is_error ) { } );
	            } ) );
                return;
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


        onFinishComm:function(event)
        {
            dojo.stopEvent( event );

        	if(event.currentTarget.classList.contains('selectable') ) {

        		dojo.query(".selectable").removeClass("selectable");

        		var place = event.currentTarget.classList[event.currentTarget.classList.length-1];

        		this.ajaxcall('/thecrew/thecrew/actFinishComm.html', {
 	                   lock:true,
 	                   place:place
 	                },this, function( result ) {
 	                }, function( is_error ) { } );
        	}

        },

        onPickCrew:function(event)
        {
        	dojo.stopEvent( event );
            if(this.isCurrentPlayerActive())
            {
            	if(event.currentTarget.classList.contains('selectable') && this.checkAction( "actPickCrew" ) ) {

            		var split = event.currentTarget.id.split('_');
	            		var id = split[split.length - 1];

	                    dojo.query(".selectable").removeClass("selectable");

	            		this.ajaxcall('/thecrew/thecrew/actPickCrew.html', {
		 	                   lock:true,
		 	                  crewId:id
		 	                },this, function( result ) {
		 	                }, function( is_error ) { } );
            	}
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


        onChooseTask:function(event)
        {
            dojo.stopEvent( event );
            if(this.isCurrentPlayerActive())
            {
            	if(event.currentTarget.classList.contains('selectable')  && this.checkAction( "actChooseTask" ) ) {

            		var id = event.currentTarget.id.split('_')[1];

                    dojo.query(".selectable").removeClass("selectable");

            		this.ajaxcall('/thecrew/thecrew/actChooseTask.html', {
	 	                   lock:true,
	 	                  taskId:id
	 	                },this, function( result ) {
	 	                }, function( is_error ) { } );
            	}
            }
        },



     // Enable to declare an optional parameter and its value
        setDefault: function(variable, default_value) {
            return variable === undefined ? default_value : variable;
        },

        // Gets the image of the symbol from the sprite
        symbol: function(name) {
            return dojo.string.substitute("<span class='logicon ${name}'></span>", {'name' : name});
        },


        ///////////////////////////////////////////////////
        //// Player's action

        /*

            Here, you are defining methods to handle player's action (ex: results of mouse click on
            game objects).

            Most of the time, these methods:
            _ check the action is possible at this game state.
            _ make a call to the game server

        */




        ///////////////////////////////////////////////////
        //// Reaction to cometD notifications

        /*
            setupNotifications:

            In this method, you associate each of your game notifications with your local method to handle it.

            Note: game notification names correspond to "notifyAllPlayers" and "notifyPlayer" calls in
                  your thecrew.game.php file.

        */
        setupNotifications: function()
        {
            dojo.subscribe('cleanUp', this, "notif_cleanUp");
            dojo.subscribe('commander', this, "notif_commander");
            dojo.subscribe('special', this, "notif_special");
            dojo.subscribe('newHand', this, "notif_newHand");
            dojo.subscribe('takeTask', this, "notif_takeTask");
            dojo.subscribe('playCard', this, "notif_playCard");
            dojo.subscribe('commCard', this, "notif_commCard");
            dojo.subscribe('continue', this, "notif_continue");
            dojo.subscribe('move', this, "notif_move");
            dojo.subscribe('resetComm', this, "notif_resetComm");
            dojo.subscribe('commpending', this, "notif_commpending");
            dojo.subscribe('distress', this, "notif_distress");
            dojo.subscribe('speak', this, "notif_speak");
            dojo.subscribe('give', this, "notif_give");
            dojo.subscribe('receive', this, "notif_receive");
            dojo.subscribe('taskUpdate', this, "notif_taskUpdate");
            dojo.subscribe('endComm', this, "notif_endComm");
            dojo.subscribe('trickWin', this, "notif_trickWin");
            dojo.subscribe('noPremium', this, "notif_nopremium");
            dojo.subscribe('giveAllCardsToPlayer', this, "notif_giveAllCardsToPlayer");

            this.notifqueue.setSynchronous('move', 1000);
            this.notifqueue.setSynchronous('trickWin', 1000); // Reasonable time for players to see the cards played before they are gathered
            this.notifqueue.setSynchronous('giveAllCardsToPlayer', 2000); // The time needed for cards to move and disappear

        },



        notif_speak: function(notif)
        {
        	var elem = dojo.byId("discussion_bubble_tasks_"+notif.args.player_id);
        	if(elem == null)
        	{
	        	this.showBubble( 'playertable_'+notif.args.player_id, notif.args.content, 0, 10000, 'bubble_custom' ) ;
        	}
        },


        notif_continue: function(notif)
        {
			dojo.query("#continue_ok_"+notif.args.player_id).addClass("check_confirm");
        },


        notif_commander: function(notif) {
        	dojo.query(".commander").addClass("hidden");
    		dojo.query("#commander_icon_spot_"+notif.args.player_id).removeClass("hidden");
    		dojo.query(".commander_in_panel").addClass("hidden");
    		dojo.query("#commander_in_panel_"+notif.args.player_id).removeClass("hidden");
        },

        notif_special: function(notif) {
        	if(notif.args.special2 == true)
        	{
	        	dojo.query(".special2").addClass("hidden");
	    		dojo.query("#special2_icon_spot_"+notif.args.player_id).removeClass("hidden");
        	}
        	else
        	{
	        	dojo.query(".special").addClass("hidden");
	    		dojo.query("#special_icon_spot_"+notif.args.player_id).removeClass("hidden");
        	}
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

        notif_trickWin: function(notif) {
            // We do nothing here (just wait in order players can view the cards played before they are gathered
        },


        notif_resetComm: function(notif)  {

        },

        notif_commCard: function(notif) {

        	var player_id = notif.args.card['location_arg'];
            if (player_id == this.player_id) {
            	this.player_hand.addToStockWithId(this.getCardUniqueId(6, 0), notif.args.reminder_id);
            	dojo.query("#myhand_item_"+notif.args.reminder_id).connect('onclick', this, 'onPlayCard');
            }
        	dojo.addClass('radio_'+player_id, 'hidden');
        	dojo.destroy('card_'+notif.args.reminder_id);
            this.playCardOnTable(notif.args.card, true);
        },



        notif_distress: function(notif) {
       		dojo.query(".selectable").removeClass("selectable");
        	dojo.query("#distress").addClass("activated");
        	this.attempts_counter.incValue(1);
        	this.total_attempts_counter.incValue(1);
        },

        notif_give: function(notif) {
            this.player_hand.removeFromStockById(notif.args.card_id);
        },

        notif_receive: function(notif) {

            var card = notif.args.card;
            var color = card.type;
            var value = card.type_arg;
            this.player_hand.addToStockWithId(this.getCardUniqueId(color, value), card.id);
			dojo.query("#myhand_item_"+card.id).connect('onclick', this, 'onPlayCard');
        },


        notif_newHand: function(notif) {

            this.player_hand.removeAll(); // Remove cards in hand if any

            for (var i in notif.args.hand) {
                var card = notif.args.hand[i];
                var color = card.type;
                var value = card.type_arg;

                this.player_hand.addToStockWithId(this.getCardUniqueId(color, value), card.id);
    			dojo.query("#myhand_item_"+card.id).connect('onclick', this, 'onPlayCard');
            }
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
