define(["dojo", "dojo/_base/declare"], (dojo, declare) => {
  return declare("thecrew.communicationTrait", null, {
    constructor(){
      this._notifications.push(
        ['commpending', 1],
        ['startComm', 10],
        ['cancelComm', 10],
        ['endComm', 700],
        ['usedComm', 10]
      );
      this._selectedComm = null;
    },

    toggleCommunication(){
      debug("Clicking on communication card");

      if(!dojo.hasClass('comm-card-' + this.player_id, "selectable")
        || (['comm', 'commToken'].includes(this.gamedatas.gamestate.name) && this.isCurrentPlayerActive() ))
        return false;

      this.takeAction('actToggleComm');
    },


    notif_commpending(n) {
      debug("Changing communication token pending state", n);
      dojo.attr('comm-card-' + this.player_id, "data-pending", n.args.pending? 1 : 0);
      dojo.toggleClass('comm-card-' + this.player_id, "selectable", n.args.canCommunicate);
    },

    notif_startComm(n) {
      dojo.attr('comm-card-' + n.args.player_id, "data-pending", 0);
    },

    notif_cancelComm(n) {
      this.setRadioToken(n.args.player_id, 'middle');
      this.setCommunicationCard(n.args.player_id);
    },


    onEnteringStateComm(args){
      this.setRadioToken(args.pId, 'waiting');
      this.switchCentralZone('cards');
      if(!this.isCurrentPlayerActive())
        return;

      let cardIds = args.cards.map(comm => comm.card.id);
      this.makeCardsSelectable(cardIds, this.onSelectingCardToCommunicate.bind(this));
      this.addSecondaryActionButton('commCancel', _("Cancel communication"), () => this.onCancelCommunication() );
    },

    onSelectingCardToCommunicate(card){
      debug("You chose", card);
      let comm = this.gamedatas.gamestate.args.cards.find(comm => comm.card.id == card.id);
      if(comm){
        this.setCommunicationCard(this.player_id, card);
        this.setRadioToken(this.player_id, comm.status);
        this._selectedComm = comm;
        this.addPrimaryActionButton("commConfirm", _("Confirm"), () => this.onConfirmCommunication() );
      }
    },

    onConfirmCommunication(){
      let comm = this._selectedComm;
      this.takeAction("actConfirmComm", {
        cardId: comm.card.id,
        status: comm.status
      });
    },

    onCancelCommunication(){
      this.takeAction("actCancelComm");
    },

    notif_endComm(n){
      debug("Someone communicate a card", n);
      this.setRadioToken(n.args.player_id, n.args.comm_status);
      this.setCommunicationCard(n.args.player_id, n.args.card);
      dojo.removeClass('comm-card-' + n.args.player_id, "selectable");
    },

    notif_usedComm(n){
      debug("Someone played his communicated card", n);
      this.setRadioToken(n.args.player_id, 'used');
      this.setCommunicationCard(n.args.player_id);
      dojo.removeClass('comm-card-' + n.args.player_id, "selectable");
    },



    setCommunicationCard(pId, card = null){
      dojo.attr('comm-card-' + pId, 'data-color', card == null? 6 : card.color);
      dojo.attr('comm-card-' + pId, 'data-value', card == null? '' : card.value);
    },

    setRadioToken(pId, position = 'middle'){
      dojo.attr('comm-card-' + pId, 'data-radio', position);
    },

  });
});
