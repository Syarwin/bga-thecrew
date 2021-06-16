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
     g_gamethemeurl + "modules/js/States/DistressTrait.js",
     g_gamethemeurl + "modules/js/States/QuestionTrait.js",
     g_gamethemeurl + "modules/js/States/MoveTileTrait.js",
     g_gamethemeurl + "modules/js/States/GiveTaskTrait.js",

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
      thecrew.distressTrait,
      thecrew.questionTrait,
      thecrew.moveTileTrait,
      thecrew.giveTaskTrait,
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
        if(gamedatas.isVisibleDiscard){
          this.setupDiscard();
        }

        this.setupSettings();
        this.inherited(arguments);
      },

      onUpdateActionButtons(){
        this.updatePlayersStatus();

        // Checkmarks
        // Must put the code here since that's the only place that is triggered when a player is set inactive
        //   in a multiplayer state
        dojo.query("#end-panel .check-ok").addClass("check-confirm");
        this.getActivePlayers().forEach(pId => dojo.removeClass("continue-ok-" + pId, "check-confirm"));

        dojo.query("#give-task-panel .check-ok").addClass("check-confirm");
        this.getActivePlayers().forEach(pId => dojo.removeClass("give-task-ok-" + pId, "check-confirm"));
      },


      // Switch from cardsOnTable / tasks / endPanel
      switchCentralZone(type){
        dojo.attr('table-middle', 'data-display', type);
      },

      clearPossible(){
        this._callbackOnCard = null;
        this._selectableCards = [];
        this._callbackOnPlayer = null;
        this._selectablePlayers = [];
        this._selectedComm = null;
        this._selectableTiles = [];
        this._selectedTile = null;
        this._selectedTask = null;


        dojo.query(".task").removeClass("unselectable selectable tile-selectable tile-selected selected");
        dojo.query(".player-table").removeClass("selectable");
        dojo.query("#hand .stockitem").removeClass("selectable unselectable");
        dojo.query(".selectableToPlay").removeClass("selectableToPlay"); // Selectable comm card to play
        dojo.empty('proposal-task');

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
        this.gamedatas.trickCount = 0;
        this.updateMissionStatus()

        this.gamedatas.players = n.args.players;
        this.updatePlayersData();

        this.gamedatas.commanderId = null;
        this.gamedatas.specialId = null;
        this.gamedatas.specialId2 = null;
        this.updateCommander();

        dojo.query('.task').forEach(dojo.destroy);
        dojo.query('.card').forEach(dojo.destroy);
        dojo.query('.preselected').removeClass('preselected');
        dojo.query('.discard-slot').removeClass('played');
      },

      startCampaign(){
        debug("Showing intro:");
        let today = new Date();
        let date = today.toLocaleString();

        var dialogText = [
          _('After years of discussion, the International Astronomical Union decided on August 24th, 2006, to withdraw Pluto’s status as the ninth planet in our solar system. From that day on, there were only eight planets in our solar system, Neptune being the eighth and the furthest away from the Sun.'),
          _('Years later, however, a sensational theory emerged — that a huge, hitherto unknown heavenly body must be positioned at the edge of our solar system. The origin of these theories was the data transmitted by the spacecraft Voyager 2 and then later by New Horizons. Unusual distortions in their measurements and phased interruptions in their transmissions left scientists perplexed. Initially dismissed by their peers as a figment of their imagination, many skeptics eventually became convinced by the evidence over time. However, the data ultimately proved inconclusive. Even though a cadre of scientists had thoroughly examined it, it still had not provided any concrete evidence of the theory.'),
          _('Out of options, the research team built around Dr. Markow created project NAUTILUS: A manned mission that would be sent to verify the existence of Planet Nine. After years of research and countless setbacks, they had finally developed the technology to carry out the mission. And now the real question is: with what crew? Are you ready to join project NAUTILUS? Volunteers needed!')
        ];

        new customgame.modal("campaignIntro", {
          autoShow:true,
          class:"thecrew_popin",
          closeIcon:'fa-times',
          verticalAlign:'flex-start',
          title: _("THE CREW"),
          contents: '<p>' + dialogText.join('</p><p>') + '</p>',
          modalTpl: this.bookModalTpl(_("LOGBOOK"), date, 1, 2),
          titleTpl:'<h2 id="popin_${id}_title" class="\${class}_title chapter-title">${title}</h2>',
        });
      },


      bookModalTpl(bookTitle, subTitle, leftPage, rightPage){
        return `
        <div id='popin_\${id}_container' class="\${class}_container">
          <div id='popin_\${id}_underlay' class="\${class}_underlay"></div>
          <div id='popin_\${id}_wrapper' class="\${class}_wrapper">
            <div id="popin_\${id}" class="\${class} book">
              \${closeIconTpl}
              <section class="open-book">
                <header>
                  <h1>${bookTitle}</h1>
                  <h6>${subTitle}</h6>
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
        </div>`;
      },


      setupSettings(){
        if(this.isReadOnly())
          return;

        this.place('jstpl_configPlayerBoard', {
          autopick:_('Auto-answer distress signal'),
          disabled:_('Disabled'),
          alwaysno:_('Always no'),
          alwaysagree:_('Always agree'),

          autocontinue:_('Auto-answer continue missions'),
          alwaysyes:_('Always yes'),
        }, 'player_boards', 'first');

        // Auto answer distress
        $('autopick').value = this.gamedatas.players[this.player_id].distressAuto;
        dojo.connect($('autopick'), 'change', () => {
          this.ajaxcall("/thecrew/thecrew/setAutopick.html", { autopick: $("autopick").value }, () => {});
        });


        // Auto answer continue
        $('autocontinue').value = this.gamedatas.players[this.player_id].continueAuto;
        dojo.connect($('autocontinue'), 'change', () => {
          this.ajaxcall("/thecrew/thecrew/setAutocontinue.html", { autocontinue: $("autocontinue").value }, () => {});
        });
      },
   });
});
