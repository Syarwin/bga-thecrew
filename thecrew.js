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
        if(gamedatas.showIntro){
          this.startCampaign();
        }

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
      },

      startCampaign(){
        debug("Showing intro:");

        let today = new Date();
        let date = today.toLocaleString();
        let bookTitle = _("LOGBOOK");
        let leftPage = 1;
        let rightPage = 2;

        new customgame.modal("campaignIntro", {
          autoShow:true,
          class:"thecrew_popin",
          closeIcon:'fa-times',
          verticalAlign:'flex-start',
          title: _("THE CREW"),
          contents: _('After years of discussion, the International Astronomical Union decided on August 24th, 2006, to withdraw Pluto’s status as the ninth planet in our solar system. From that day on, there were only eight planets in our solar system, Neptune being the eighth and the furthest away from the Sun.<br/><br/> Years later, however, a sensational theory emerged — that a huge, hitherto unknown heavenly body must be positioned at the edge of our solar system. The origin of these theories was the data transmitted by the spacecraft Voyager 2 and then later by New Horizons. Unusual distortions in their measurements and phased interruptions in their transmissions left scientists perplexed. Initially dismissed by their peers as a figment of their imagination, many skeptics eventually became convinced by the evidence over time. However, the data ultimately proved inconclusive. Even though a cadre of scientists had thoroughly examined it, it still had not provided any concrete evidence of the theory.<br/><br/> Out of options, the research team built around Dr. Markow created project NAUTILUS: A manned mission that would be sent to verify the existence of Planet Nine. After years of research and countless setbacks, they had finally developed the technology to carry out the mission. And now the real question is: with what crew? Are you ready to join project NAUTILUS? Volunteers needed!'),
          modalTpl: `
          <div id='popin_\${id}_container' class="\${class}_container">
            <div id='popin_\${id}_underlay' class="\${class}_underlay"></div>
            <div id='popin_\${id}_wrapper' class="\${class}_wrapper">
              <div id="popin_\${id}" class="\${class}">
                \${closeIconTpl}
                <section class="open-book">
                  <header>
                    <h1>${bookTitle}</h1>
                    <h6>${date}</h6>
                  </header>
                  <article>
                    \${titleTpl}
                    \${contentsTpl}
                  </article>
                  <footer>
                    <ol class="page-numbers">
                      <li>${leftPage}</li>
                      <li>${rightPage}</li>
                    </ol>
                  </footer>
                </section>
              </div>
            </div>
          </div>`
        });
      },
   });
});
