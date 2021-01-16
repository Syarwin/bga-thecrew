define(["dojo", "dojo/_base/declare"], (dojo, declare) => {
  const CLOCKWISE = 1, DONT_USE = 2, ANTICLOCKWISE = 3;
  return declare("thecrew.distressTrait", null, {
    constructor(){
      this._notifications.push(
        ['chooseDistressDirection', 10],
        ['distressActivated', 10],
        ['chooseDistressCard', 10]
      );
    },

    onEnteringStateDistressSetup(args){
      this.switchCentralZone('distress');
      dojo.attr('distress-panel', 'data-dir', 0);
      this.addSecondaryActionButton('distressHelp', '?', () => this.showMessage(_('A distress signal can be sent out before the first trick of a mission and before any communication. If the distress signal is activated, each crew member may pass one card to his neighbor. Rockets may not be passed on! Decide together if you will pass the cards to the left or the right. Everyone has to pass in the same direction!'), 'info') );
      this.addTooltip('distressHelp', _('A distress signal can be sent out before the first trick of a mission and before any communication. If the distress signal is activated, each crew member may pass one card to his neighbor. Rockets may not be passed on! Decide together if you will pass the cards to the left or the right. Everyone has to pass in the same direction!'), '');

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
      if(n.args.dir != DONT_USE){
        this.gamedatas.status.distress = true;
        this.updateMissionStatus();
      }
    },

    onEnteringStateDistress(args){
      this.switchCentralZone('distress');
      dojo.attr('distress-panel', 'data-dir', args.dir);
      if(!this.isSpectator){
        this.makeCardsSelectable(args['_private'].cards, this.onClickCardToDistress.bind(this));

        this.gamedatas.gamestate.descriptionmyturn = dojo.string.substitute(
          _('${you} must choose a card to pass to ${player_name}'), {
            you: this.coloredName(),
            player_name : this.coloredName(args._private.pId),
        });
        this.updatePageTitle();
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
  });
});
