define(["dojo", "dojo/_base/declare"], (dojo, declare) => {
  return declare("thecrew.trickTrait", null, {
    constructor(){
      this._notifications.push(
        ['playCard', 1000]
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
  });
});
