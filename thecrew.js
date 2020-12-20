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

     g_gamethemeurl + "modules/js/States/PickTaskTrait.js",
     g_gamethemeurl + "modules/js/States/TrickTrait.js",

 ], function (dojo, declare) {
    return declare("bgagame.thecrew", [
      customgame.game,
      thecrew.cardTrait,
      thecrew.playerTrait,
      thecrew.tooltipTrait,
      thecrew.pickTaskTrait,
      thecrew.trickTrait,
    ], {
      constructor(){

      /*
      this.commander_id = null;
      this.players = null;
      this.colors = null;

      this.card_width = 100;
      this.card_height = 157;

      // Number of turns
      this.mission_counter = null;
      this.attempts_counter = null;
      this.total_attempts_counter = null;
      this.trick_counters = {};
      this.cards_counters = {};
      this.multiSelect = null;
      this.selected = null;
      this.stateName = null;

      */
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

      setupMission(){
        this.missionCounter = new ebg.counter();
        this.missionCounter.create('mission-counter');
        this.attemptsCounter = new ebg.counter();
        this.attemptsCounter.create('try-counter');
        this.totalAttemptsCounter = new ebg.counter();
        this.totalAttemptsCounter.create('total-try-counter');
        this.updateMissionStatus();
      },

      updateMissionStatus(){
        let mId = this.gamedatas.status.mId;
        $('mission-description').innerHTML = _(this.gamedatas.missions[mId - 1].desc);
        this.missionCounter.setValue(mId);
        this.attemptsCounter.setValue(this.gamedatas.status.attempts);
        this.totalAttemptsCounter.setValue(this.gamedatas.status.total);

        dojo.toggleClass('distress', "activated", this.gamedatas.status.distress);
      }
   });
});
