define(["dojo", "dojo/_base/declare"], (dojo, declare) => {
  const CLOCKWISE = 1, DONT_USE = 2, ANTICLOCKWISE = 3;
  return declare("thecrew.distressTrait", null, {
    constructor(){
      this._notifications.push(
        ['chooseDistressDirection', 10],
        ['distressActivated', 10],
        ['chooseDistressCard', 10],
        ['giveDistressCard', 1000],
        ['receiveDistressCard', 1000]
      );
    },

    onEnteringStateDistressSetup(args){
      this.switchCentralZone('distress');
      dojo.attr('distress-panel', 'data-dir', 0);

      this.connect($('clockwise-button'), 'click', () => this.onChooseDistressDirection(CLOCKWISE) );
      this.connect($('dont-use-button'),  'click', () => this.onChooseDistressDirection(DONT_USE) );
      this.connect($('anticlockwise-button'),  'click', () => this.onChooseDistressDirection(ANTICLOCKWISE) );
    },

    onChooseDistressDirection(dir){
      this.takeAction("actChooseDirection", { dir });
    },

    notif_chooseDistressDirection(n){
      debug("Choosing a direction for distress signal", n);
      dojo.attr('distress-choice-' + n.args.pId, 'data-choice', n.args.dir);
    },

    notif_distressActivated(n){
      this.gamedatas.status.distress = true;
      this.updateMissionStatus();
    },

    onEnteringStateDistress(args){
      this.switchCentralZone('distress');
      dojo.attr('distress-panel', 'data-dir', args.dir);
      if(!this.isSpectator){
        this.makeCardsSelectable(args['_private'], this.onClickCardToDistress.bind(this));
      }
    },

    onClickCardToDistress(card){
      debug("Choosing to distress :", card);
      this.takeAction('actChooseCardDistress', { cardId: card.id });
    },

    notif_chooseDistressCard(n){
      debug('Choosing a card to distress', n);
      this.highlightCard(n.args.card.id);
    },


    notif_giveDistressCard(n) {
      this.slide('hand_item_' + n.args.card.id, 'player-table-' + n.args.player_id, 1000)
        .then(() => this._hand.removeFromStockById(n.args.card.id) );
    },

    notif_receiveDistressCard(n) {
      this.addCardInHand(n.args.card);
      dojo.addClass("hand_item_" + n.args.card.id, "received");
    },

  });
});