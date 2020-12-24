define(["dojo", "dojo/_base/declare"], (dojo, declare) => {
  return declare("thecrew.playerTrait", null, {
    constructor(){
      this._notifications.push(
        ['commander', 100]
      );

      this.trickCounters = [];
      this.cardsCounters = [];
      this.positions = [];
    },

    /*
     * Called to setup player board, called at setup and when someone is eliminated
     */
    setupPlayers(){
      let players = Object.values(this.gamedatas.players);
      let nPlayers = players.length;
      let currentPlayerNo = players.reduce((carry, player) => (player.id == this.player_id && !player.eliminated)? player.no : carry, 0);
      let topRowNo = nPlayers == 5? [1,2,3] : [1,2];

      players.forEach( player => {
        player.no = (player.no + nPlayers - currentPlayerNo) % nPlayers;
        this.positions[player.id] = player.no;

        let row = topRowNo.includes(player.no)? 'top' : 'bottom';
        this.place('jstpl_playerTable', player, 'table-' + row);
        this.place('jstpl_playerMat', player, 'card-mat-' + row);
        this.place('jstpl_playerCheckMark', player, 'end-panel-' + row);

        // Create tasks
        player.tasks.forEach(task => this.addTask(task, 'tasks-' + player.id) );

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


        // Create hand if current player
        if(this.player_id == player.id){
          dojo.place('<div id="hand-wrapper"><div id="hand"></div></div>', 'hand-container');
          this.setupHand(player.cards);
          dojo.connect($('comm-card-' + player.id), 'onclick', () => this.toggleCommunication() );
        }
      });

      this.updatePlayersData();
      this.updateCommander();
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
      dojo.attr('overall-content', 'data-commander', this.gamedatas.players[this.gamedatas.commanderId].no);
      dojo.attr('overall-content', 'data-special', null); // TODO
      dojo.attr('overall-content', 'data-special2', null);
    },

    notif_commander(n) {
      this.gamedatas.commanderId = n.args.player_id;
      this.updateCommander();
    },

  });
});
