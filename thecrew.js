/**
 *------
 * BGA framework: Â© Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * thecrew implementation : © Nicolas Gocel <nicolas.gocel@gmail.com>
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

 var isDebug = window.location.host == 'studio.boardgamearena.com' || window.location.hash.indexOf('debug') > -1;
 var debug = isDebug ? console.info.bind(window.console) : function () { };
 define([
     "dojo", "dojo/_base/declare",
     "ebg/core/gamegui",
     "ebg/counter",
     g_gamethemeurl + "modules/js/Game/game.js",
     g_gamethemeurl + "modules/js/Game/modal.js",

     g_gamethemeurl + "modules/js/CardTrait.js",
     g_gamethemeurl + "modules/js/PlayerTrait.js",
     g_gamethemeurl + "modules/js/TooltipTrait.js",
     g_gamethemeurl + "modules/js/MissionTrait.js",

     g_gamethemeurl + "modules/js/States/PickTaskTrait.js",
     g_gamethemeurl + "modules/js/States/TrickTrait.js",
     g_gamethemeurl + "modules/js/States/CommunicationTrait.js",

 ], function (dojo, declare) {
    return declare("bgagame.thecrew", [
      customgame.game,
      thecrew.cardTrait,
      thecrew.playerTrait,
      thecrew.tooltipTrait,
      thecrew.missionTrait,
      thecrew.pickTaskTrait,
      thecrew.trickTrait,
      thecrew.communicationTrait,
    ], {
      constructor(){
        this._notifications.push(
          ['nopremium', 10],
          ['cleanUp', 10]
        );
      },

      /*
       * Setup:
       *	This method set up the game user interface according to current game situation specified in parameters
       *	The method is called each time the game interface is displayed to a player, ie: when the game starts and when a player refreshes the game page (F5)
       *
       * Params :
       *	- mixed gamedatas : contains all datas retrieved by the getAllDatas PHP method.
       */
      setup(gamedatas) {
      	debug('SETUP', gamedatas);

        this.setupPlayers();
        this.setupMission();
        this.setupTooltips();
        this.inherited(arguments);
      },

      onUpdateActionButtons(){
        this.updatePlayersStatus();
      },


      // Switch from cardsOnTable / tasks / endPanel
      switchCentralZone(type){
        dojo.attr('table-middle', 'data-display', type);
      },

      clearPossible(){
        this._callbackOnCard = null;
        this._selectableCards = [];
        dojo.query(".task").removeClass("selectable");
        dojo.query("#hand .stockitem").removeClass("selectable unselectable");

        this.inherited(arguments);
      },



      notif_nopremium(notif) {
      	this.myDlg = new ebg.popindialog();
      	this.myDlg.create( 'myDialogUniqueId' );
      	this.myDlg.setTitle( _("Premium member required") );
      	this.myDlg.setMaxWidth( 500 );
      	var html = _('Congratulations for your success !<br/><br/>The Crew board game is including 50 different missions.<br/>You can access the 10 first missions for free, and you can access the other 40 missions if at least one Premium member is around the table.<br/><br/><a href="https://boardgamearena.com/premium">Go Premium now</a><br/><br/>Good luck for your Quest.');
      	this.myDlg.setContent( html );
      	this.myDlg.show();
      },


      notif_cleanUp(n) {
        this.gamedatas.status = n.args.status;
        this.updateMissionStatus()

        this.gamedatas.players = n.args.players;
        this.updatePlayersData();

        dojo.query('.task').forEach(dojo.destroy);
        dojo.query(".card").forEach(dojo.destroy);

        /*
            for( var player_id in this.players )
              {
                  dojo.removeClass('radio_' + player_id);
                  dojo.addClass('radio_' + player_id, 'radio appears middle');
              }
              dojo.query(".card_com .cardontable").connect('onclick', this, 'onStartComm');
        */
      },

   });
});
