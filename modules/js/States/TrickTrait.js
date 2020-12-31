define(["dojo", "dojo/_base/declare"], (dojo, declare) => {
  return declare("thecrew.trickTrait", null, {
    constructor(){
      this._notifications.push(
        ['playCard', 1000],
        ['trickWin', 2000],
        ['newTrick', 10]
      );
    },


    onEnteringStatePlayerTurn(args){
      debug("Player turn", args);
      this.switchCentralZone('cards');
      dojo.toggleClass("distress", "selectable", args.canDistress)

      if(this.isCurrentPlayerActive()){
        this.makeCardsSelectable(args.cards, (card) => this.onClickCardToPlay(card) );
      }
    },


    onClickCardToPlay(card){
      if(!this.isCurrentPlayerActive())
        return;

      this.takeAction('actPlayCard', { cardId : card.id });
    },

    notif_playCard(n) {
      debug("Playing a card", n);
      // Play a card on the table
      this.playCardOnTable(n.args.card);
      this.descCardsCounter(n.args.card.pId);
    },


    notif_trickWin(n){
      debug("Winning a trick", n);
      let winnerId = n.args.player_id;
      this.incTrickCounter(winnerId); // Increase the trick counter by one

      n.args.oCards.forEach(card => {
        let isWinningCard = winnerId == card.pId;
        let cardId = 'card-' + card.id;
        dojo.style(cardId, 'z-index', 11 + isWinningCard); // Ensure that the winning card stays on top

        // Program the anim: 1. Slide the cart (1 second)
        this.slide(cardId, 'mat-' + winnerId, 1000)
        .then(() => {
          // 2. Delete it : immediately for card under the top card, 1s for the top card
          setTimeout( () => dojo.destroy(cardId), isWinningCard? 1000 : 10);
        });
      });
    },

    notif_taskUpdate(n) {
      dojo.attr('task-' + n.args.task.id, 'data-status', n.args.task.status);
    },


    notif_newTrick(n){
      // Update trick count and communication in case of disruption
      this.gamedatas.trickCount = n.args.trickCount;
      this.updateMissionCommunication();

      if(!this.isSpectator){
        dojo.toggleClass('comm-card-' + this.player_id, "selectable", n.args.players[this.player_id].canCommunicate);
      }
    },
  });
});
