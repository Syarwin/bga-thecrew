define(["dojo", "dojo/_base/declare"], (dojo, declare) => {
  return declare("thecrew.playerTrait", null, {
    constructor(){
      this._notifications.push(
        ['commander', 100],
        ['specialCrewMember', 100]
      );

      this.trickCounters = [];
      this.cardsCounters = [];
      this.positions = [];
      this._callbackOnPlayer = null;
      this._selectablePlayers = [];

      this.JARVIS_ID = 1;
    },

    isJarvisPlaying() {
      return Object.values(this.gamedatas.players).some(({id}) => id === this.JARVIS_ID);
    },

    /*
     * Called to setup player board, called at setup and when someone is eliminated
     */
    setupPlayers(){
      let players = this.gamedatas.playersOrder.map(id => this.gamedatas.players[id]);
      let nPlayers = players.length;
      let currentPlayerNo = players.reduce((carry, player) => (player.id == this.player_id && !player.eliminated)? player.no : carry, 0);
      let topRowNo = nPlayers == 5? [1,2,3] : [1,2];

      players.forEach((player, i) => {
        if (player.id === this.JARVIS_ID) {
          dojo.place(this.format_string(jstpl_jarvis, {}), 'overall_player_board_' + player.afterPlayer, 'after');
        }

        player.no = i;
        this.positions[player.id] = player.no;

        let row = topRowNo.includes(player.no)? 'top' : 'bottom';
        this.place('jstpl_playerTable', player, 'table-' + row);
        this.place('jstpl_playerMat', player, 'card-mat-' + row);
        this.place('jstpl_playerCheckMark', player, 'end-panel-' + row);
        this.place('jstpl_playerCheckMarkGiveTask', player, 'give-task-panel-' + row);
        this.place('jstpl_playerDistressChoice', player, 'distress-panel-' + row);
        this.place('jstpl_playerFailMissionAnswer', player, 'fail-mission-panel-' + row);
        dojo.connect($('player-table-' + player.id), 'click', () => this.onClickPlayer(player.id));

        // Create tasks
        player.tasks.forEach(task => this.addTask(task, 'player-table-missions-' + player.id) );

        // Place communication card and radio token
        this.setCommunicationCard(player.id, player.commCard);
        this.setRadioToken(player.id, player.commToken);

        // Create card on table
        if(player.table.length){
          this.addCardOnTable(player.table[0]);
        }

        /*
         * Create player panel and counters
         */
        this.place('jstpl_playerPanel', player,  'player_board_' + player.id);
        this.trickCounters[player.id] = new ebg.counter();
        this.trickCounters[player.id].create('trick-counter-' + player.id);

        this.cardsCounters[player.id] = new ebg.counter();
        this.cardsCounters[player.id].create('cards-in-hands-counter-' + player.id);

        /*
         * Display answer if needed
         */
        let mission = this.getMission();
        if(player.reply != null && mission.replies){
          var msg = _(mission.replies[player.reply]);
          this.setupReply(player.id, msg);
        }

        // Create hand if current player
        if(this.player_id == player.id){
          dojo.place('<div id="hand-wrapper"><div id="hand"></div></div>', 'hand-container');
          this.setupHand(player.cards);
          if(player.distressCard)
            this.highlightCard(player.distressCard);
          dojo.connect($('comm-card-' + player.id), 'onclick', () => this.toggleCommunication() );

          if(player.preselected && $('hand_item_' + player.preselected))
            dojo.addClass('hand_item_' + player.preselected, 'preselected');
        }

        // Setup Jarvis
        if (player.id == this.JARVIS_ID) {
          this.setupJarvisCards();
        }
      });

      this.updatePlayersData();
      this.updateCommander();
      this.updatePlayersPositions();
    },

    updatePlayersPositions() {
      let players = Object.values(this.gamedatas.players);
      let playersOrder = players.sort((a,b) => a.no - b.no);

      playersOrder.forEach((player) => {
        let isLeft = player.no == 1;
        $(`player-table-${player.id}`).classList.toggle('left-side', isLeft);

        if (player.id == this.JARVIS_ID) {
          $('jarvis-hand-container').classList.toggle('left-side', isLeft);
        }
      });
    },

    descCardsCounter(pId){
      this.cardsCounters[pId].incValue(-1);
    },

    incTrickCounter(pId, value = 1){
      this.trickCounters[pId].incValue(value);
    },


    updatePlayersData(){
      Object.values(this.gamedatas.players).forEach( player => {
        player.no = this.positions[player.id]; // Erased in the notif_cleanUp
        this.trickCounters[player.id].setValue(player.nTricks);
        this.cardsCounters[player.id].setValue(player.nCards);

        this.setCommunicationCard(player.id, player.commCard);
        this.setRadioToken(player.id, player.commToken);
        // Set distress choice
        dojo.attr('distress-choice-' + player.id, 'data-choice', player.distressChoice);
        // Set fail mission answer
        dojo.attr('fail-mission-answer-' + player.id, 'data-answer', player.restartMissionAnswer);

        if(this.player_id == player.id){
          dojo.attr('comm-card-' + player.id, 'data-pending', player.commPending? 1 : 0);
          dojo.toggleClass('comm-card-' + player.id, "selectable", player.canCommunicate);
        }
      });
    },

    updatePlayersStatus(){
      dojo.query("#thecrew-table .player-table").removeClass("active");
      this.getActivePlayers().forEach(pId => dojo.addClass("player-table-" + pId, "active"));
    },


    updateCommander(){
      let commander = this.gamedatas.players[this.gamedatas.commanderId] ? this.gamedatas.players[this.gamedatas.commanderId] : { no : -1};
      dojo.attr('overall-content', 'data-commander', commander.no);

      let special = this.gamedatas.players[this.gamedatas.specialId] ?  this.gamedatas.players[this.gamedatas.specialId] : { no : -1};
      dojo.attr('overall-content', 'data-special', special.no);

      let special2 = this.gamedatas.players[this.gamedatas.specialId2] ? this.gamedatas.players[this.gamedatas.specialId2] : { no : -1};
      dojo.attr('overall-content', 'data-special2', special2.no);
    },

    notif_commander(n) {
      debug("New commander", n);
      this.gamedatas.commanderId = n.args.player_id;
      this.updateCommander();
    },

    notif_specialCrewMember(n){
      debug("New special crew member", n);
      if(n.args.special_id){
        this.gamedatas.specialId = n.args.special_id;
      }

      if(n.args.special2_id){
        this.gamedatas.specialId2 = n.args.special2_id;
      }

      this.updateCommander();
    },


    makePlayersSelectable(players, callback){
      this._callbackOnPlayer = callback;
      this._selectablePlayers = players.map(pId => parseInt(pId));

      dojo.query(".player-table").removeClass("selectable").addClass("unselectable");
      players.forEach(pId => {
        dojo.query("#player-table-" + pId).removeClass('unselectable').addClass('selectable');
        this.addPrimaryActionButton('btnPlayer'+pId, this.gamedatas.players[pId].name, () => this.onClickPlayer(parseInt(pId)) );
      });
    },

    clearSelectablePlayers(){
      dojo.query(".player-table").removeClass("selectable unselectable");
      this._selectablePlayers.forEach(pId => dojo.destroy('btnPlayer'+pId) );
      this._selectablePlayers = [];
    },

    onClickPlayer(pId){
      debug("Clicked on player : ", pId, this._selectablePlayers);

      if(!this._selectablePlayers.includes(pId))
        return;

      this._callbackOnPlayer(pId);
    },

  });
});
