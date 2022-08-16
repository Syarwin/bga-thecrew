define(["dojo", "dojo/_base/declare"], (dojo, declare) => {
  const CONFIRM_TIMEOUT = 10;

  return declare("thecrew.trickTrait", null, {
    constructor(){
      this._notifications.push(
        ['playCard', 1000],
        ['trickWin', 1500],
        ['newTrick', 10],
        ['clearPreselect', 10],
        ['preselect', 10]
      );
    },


    onEnteringStatePlayerTurn(args){
      debug("Player turn", args);
      this.switchCentralZone('cards');
      dojo.toggleClass("distress", "selectable", args.canDistress)

      if(this.isCurrentPlayerActive()){
        this.makeCardsSelectable(args.cards, (card) => this.onClickCardToPlay(card) );
        if(args.canPlayCommunicatedCard){
          dojo.addClass('comm-card-' + this.player_id, 'selectableToPlay');
          this.connect($('comm-card-' + this.player_id), 'click', () => this.onClickCardToPlay(args.commCard) );
        }
      }

      // Preselection
      else if(!this.isReadOnly()){
        let cards = this._hand.items.map(item => item.id);
        this.makeCardsSelectable(cards, (card) => this.onClickCardToPreselect(card) );
      }
    },

    onClickCardToPlay(card){
      if(!this.isCurrentPlayerActive())
        return;

      this.takeAction('actPlayCard', { cardId : card.id });
    },

    notif_playCard(n) {
      debug("Playing a card", n);
      this.clearPossible();

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
          dojo.fadeOut({ node: cardId }).play();
          // 2. Delete it : immediately for card under the top card, 1s for the top card
          setTimeout( () => dojo.destroy(cardId), isWinningCard? 500 : 10);

          if(this.gamedatas.isVisibleDiscard){
            dojo.addClass('discard-'+ card.color + '-' + card.value, "played");
          }
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



    onClickCardToPreselect(card){
      this.highlightCard(card.id);

      dojo.destroy('btnConfirmPreselectCard');
      dojo.destroy('btnCancelPreselectCard');
      this.addPrimaryActionButton('btnConfirmPreselectCard', _('Pre-select card'), () => {
        dojo.destroy('btnConfirmPreselectCard');
        dojo.destroy('btnCancelPreselectCard');
        this.takeAction("actPreselectCard", { cardId: card.id});
      });
      this.stopActionTimer();
      this.startActionTimer('btnConfirmPreselectCard', CONFIRM_TIMEOUT);
      setTimeout(() => {
        dojo.query('#hand_item_' + card.id).removeClass('selected');
      }, CONFIRM_TIMEOUT * 1000);

      this.addSecondaryActionButton('btnCancelPreselectCard', _('Cancel'), () => {
        dojo.destroy('btnConfirmPreselectCard');
        dojo.destroy('btnCancelPreselectCard');
        dojo.query('#hand_item_' + card.id).removeClass('selected');
      });
    },

    notif_preselect(n){
      debug("Preselected card", n);
      dojo.query('.preselected').removeClass('preselected');
      dojo.addClass('hand_item_' + n.args.card.id, 'preselected');
    },

    notif_clearPreselect(n){
      dojo.query('.preselected').removeClass('preselected');
    },
  });
});
