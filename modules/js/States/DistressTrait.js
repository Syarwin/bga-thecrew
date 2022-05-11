define(["dojo", "dojo/_base/declare"], (dojo, declare) => {
  const CLOCKWISE = 1, DONT_USE = 2, ANTICLOCKWISE = 3, AGREE = 4;
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

      if(!this.isSpectator){
        this.connect($('clockwise-button'), 'click', () => this.onChooseDistressDirection(CLOCKWISE) );
        this.connect($('dont-use-button'),  'click', () => this.onChooseDistressDirection(DONT_USE) );
        this.connect($('anticlockwise-button'),  'click', () => this.onChooseDistressDirection(ANTICLOCKWISE) );
        this.connect($('whatever-button'),  'click', () => this.onChooseDistressDirection(AGREE) );
      }

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
      this._selectedDistressJarvis = null;
      this._selectedDistress = null;

      if(!this.isSpectator){
        this.makeCardsSelectable(args['_private'].cards, this.onClickCardToDistress.bind(this));

        this.gamedatas.gamestate.descriptionmyturn = dojo.string.substitute(
          _('${you} must choose a card to pass to ${player_name}'), {
            you: this.coloredName(),
            player_name : this.coloredName(args._private.pId),
        });

        if (args._private.jarvis) {
          this.gamedatas.gamestate.descriptionmyturn = dojo.string.substitute(
            _('${you} must choose a card to pass to ${player_name} and a card for Jarvis to pass to ${player_name2}'),
            {
              you: this.coloredName(this.player_id),
              player_name: this.coloredName(args._private.pId),
              player_name2: this.coloredName(args._private.jarvisTarget),
            },
          );
          this._jarvisDistress = true;
          this.makeJarvisCardsSelectable(args._private.jarvis, this.onClickCardToDistressJarvis.bind(this));
        } else {
          this._jarvisDistress = false;
        }
        this.updatePageTitle();
      }

    },

    onClickCardToDistress(card){
      debug("Choosing to distress :", card);
      if (this.isJarvisPlaying()) {
        dojo.query('#hand-container .selected').removeClass('selected');
        dojo.addClass('hand_item_' + card.id, 'selected');
        this._selectedDistress = card.id;
        this.updateDistressBtn();
      } else {
        this.takeAction('actChooseCardDistress', { cardId: card.id });
      }
    },

    onClickCardToDistressJarvis(card){
      debug("Choosing to distress Jarvis:", card);
      dojo.query('#jarvis-hand-container .selected').removeClass('selected');
      dojo.addClass('hand_item_' + card.id, 'selected');
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
        // this.startActionTimer('btnConfirmDistressChoice', 10);
      } else if (this._selectedDistress && this._selectedDistressJarvis) {
        this.addPrimaryActionButton('btnConfirmDistressChoice', _('Confirm'), () => {
          dojo.destroy('btnConfirmDistressChoice');
          this.takeAction(
            'actChooseCardDistressJarvis',
            { cardId: this._selectedDistress, jarvisCardId: this._selectedDistressJarvis },
            false,
          );
        });
        // this.startActionTimer('btnConfirmDistressChoice', 10);
      }
    },

    notif_chooseDistressCard(n){
      debug('Choosing a card to distress', n);
      this.highlightCard(n.args.card.id);
    },
  });
});
