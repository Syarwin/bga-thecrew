define(["dojo", "dojo/_base/declare"], (dojo, declare) => {
  const CLOCKWISE = 1, DONT_USE = 2, ANTICLOCKWISE = 3, AGREE = 4;
  return declare("thecrewleocaseiro.distressTrait", null, {
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
      this.connect($('whatever-button'),  'click', () => this.onChooseDistressDirection(AGREE) );

      Object.keys(args.players).forEach(pId => dojo.attr('distress-choice-' + pId, 'data-choice', args.players[pId]) );
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
      debugger;
      this._selectedDistressJarvis = null;
      this._selectedDistress = null;

      if(!this.isSpectator){
        this.makeCardsSelectable(args['_private'].cards, this.onClickCardToDistress.bind(this));

        this.gamedatas.gamestate.descriptionmyturn = dojo.string.substitute(
          _('${you} must choose a card to pass to ${player_name}'), {
            you: this.coloredName(),
            player_name : this.coloredName(args._private.pId),
        });
        this.updatePageTitle();
      }

      if (args._private.jarvis) {
        this.gamedatas.gamestate.descriptionmyturn = dojo.string.substitute(
          _('${you} must choose a card to pass to ${player_name} and a card for Jarvis to pass to ${player_name2}'),
          {
            you: this.coloredYou(),
            player_name: this.coloredPlayerName(this.gamedatas.players[args._private.pId].name),
            player_name2: this.coloredPlayerName(this.gamedatas.players[args._private.jarvisTarget].name),
          },
        );
        this._jarvisDistress = true;
        args._private.jarvis.forEach((cardId) =>
          this.onClick('card-' + cardId, () => this.onClickCardToDistressJarvis(cardId)),
        );
      } else {
        this._jarvisDistress = false;
      }

    },

    onClickCardToDistress(card){
      debug("Choosing to distress :", card);
      this.takeAction('actChooseCardDistress', { cardId: card.id });
      this._selectedDistress = card.id;
      this.updateDistressBtn();
    },

    onClickCardToDistressJarvis(card){
      debug("Choosing to distress Jarvis:", card);
      dojo.query('#jarvis-hand-container .selected').removeClass('selected');
      dojo.addClass('card-' + cardId, 'selected');
      this._selectedDistressJarvis = card.id;
      this.updateDistressBtn();
    },



    updateDistressBtn() {
      dojo.destroy('btnConfirmDistressChoice');
      if (!this._jarvisDistress) {
        this.addPrimaryActionButton('btnConfirmDistressChoice', _('Confirm'), () => {
          dojo.destroy('btnConfirmDistressChoice');
          this.takeAction('actChooseCardDistress', { cardId: this._selectedDistress }, false);
        });
      } else if (this._selectedDistress && this._selectedDistressJarvis) {
        this.addPrimaryActionButton('btnConfirmDistressChoice', _('Confirm'), () => {
          dojo.destroy('btnConfirmDistressChoice');
          this.takeAction(
            'actChooseCardDistressJarvis',
            { cardId: this._selectedDistress, jarvisCardId: this._selectedDistressJarvis },
            false,
          );
        });
      }
    },

    notif_chooseDistressCard(n){
      debug('Choosing a card to distress', n);
      this.highlightCard(n.args.card.id);
    },
  });
});
