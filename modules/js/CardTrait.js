define(["dojo", "dojo/_base/declare"], (dojo, declare) => {
  const BLUE = 1, GREEN = 2, PINK = 3, YELLOW = 4, ROCKET = 5;
  const CARD_WIDTH = 100;
  const CARD_HEIGHT = 157;

  const JARVIS_ID = 1;

  return declare("thecrewleocaseiro.cardTrait", null, {
    constructor(){
      this._notifications.push(
        ['newHand', 100],
        ['giveCard', 1000],
        ['receiveCard', 1000],
        ['receiveCardJarvis', 1000],
        ['jarvisRevealNewCard', 10],
        ['newJarvisHand', 100],
      );
      this._callbackOnCard = null;
      this._selectableCards = [];

      this.colors = {
        1 : {
          'name':'Blue',
          'nameof':'Blues',
          'color':'blue',
        },
        2 : {
          'name':'Green',
          'nameof':'Greens',
          'color':'green',
        },
        3 : {
          'name':'Pink',
          'nameof':'Pinks',
          'color':'pink',
        },
        4 : {
          'name':'Yellow',
          'nameof':'Yellows',
          'color':'yellow',
        },
        5 : {
          'name':'Rocket',
          'nameof':'Rockets',
          'color':'black',
        }
      };
    },


    setupHand(cards){
      // Initialize stock component
      this._hand = new ebg.stock();
      this._hand.create(this, $('hand'), CARD_WIDTH, CARD_HEIGHT);
      this._hand.image_items_per_row = 11;
      this._hand.setSelectionMode(0);
      this._hand.setSelectionAppearance('class');
      this._hand.centerItems = true;
      this._hand.item_margin = 10;

      // Create one type for each card
      for(var color = 1; color <= 5; color++) {
        for(var value = 1; value <= (color == 5 ? 4 : 9); value++) {
          let type = this.getCardUniqueType(color, value);
          let pos = (color < 5)? (color-1)*11+value-1
                  : 9 + (value-1)*11; // Rocket cards

          this._hand.addItemType(type, type, g_gamethemeurl + 'img/cards.png', pos);
        }
      }

      // Create the hand
      cards.forEach(card => this.addCardInHand(card) );
    },

    getCardUniqueType(color, value) {
      return (color-1)*10+(value-1);
    },

    addCardInHand(card){
      let type = this.getCardUniqueType(card.color, card.value);
      this._hand.addToStockWithId(type, card.id);
      this.createCardTooltip(card, "hand_item_" + card.id);
      dojo.connect($("hand_item_"+card.id), 'onclick', () => this.onPlayCard(card) );
    },

    highlightCard(cardId, exclusive = true){
      if(exclusive){
        dojo.query("#hand .stockitem").removeClass("selected");
      }
      if($("hand_item_" + cardId)){
        dojo.addClass("hand_item_" + cardId, "selected");
      }
    },

    notif_newHand(n){
      this._hand.removeAll(); // Remove cards in hand if any
      n.args.hand.forEach(card => this.addCardInHand(card) );
    },


    makeCardsSelectable(cards, callback){
      this._callbackOnCard = callback;
      this._selectableCards = cards;

      dojo.query("#hand .stockitem").removeClass("selectable").addClass("unselectable");
      dojo.query('#jarvis-hand-container .jarvis-card').removeClass('selectable').addClass('unselectable');
      cards.forEach(cardId => dojo.query("#hand_item_" + cardId).removeClass('unselectable').addClass('selectable') );
    },

    onPlayCard(card){
      debug("Clicked on card : ", card);

      if(!this._selectableCards.includes(card.id))
        return;

      this._callbackOnCard(card);
    },

    addCardOnTable(card, container = null){
      if(container == null) container = 'mat-' + card.pId;

      this.place('jstpl_card', card, container);
      this.createCardTooltip(card, "card-" + card.id);
      dojo.connect($("card-" + card.id), 'onclick', () => this.onPlayCard(card) );
    },

    playCardOnTable(card, comm = false){
      let preexist = $('card-' + card.id);
      let target = (comm? 'comcard-' : 'mat-') + card.pId;

      // Create card if needed, otherwise reattach
      if(preexist){
        this.attachToNewParent('card-' + card.id, target);
      } else {
        this.addCardOnTable(card, target);
      }

      // Place from right spot
      var from = null;
      if(card.pId != this.player_id && !preexist){
        from = 'player-table-' + card.pId;
      }
      if(card.pId == this.player_id && $('hand_item_' + card.id)){
        from = 'hand_item_' + card.id;
        this._hand.removeFromStockById(card.id);
      }

      if(from != null){
        this.placeOnObject('card-' + card.id, from);
      }

      // Slide it !
      dojo.animateProperty({
        node: 'card-' + card.id,
        duration: 1000,
        easing: dojo.fx.easing.expoInOut,
        properties: {
           left: 0,
           top: 0,
        }
      }).play();
    },


    notif_giveCard(n) {
      this.slide('hand_item_' + n.args.card.id, 'player-table-' + n.args.player_id, 1000)
        .then(() => this._hand.removeFromStockById(n.args.card.id) );
      // this.slide('card-' + n.args.card.id, 'jarvis-column-' + n.args.column);
    },

    notif_receiveCard(n) {
      this.addCardInHand(n.args.card);
      dojo.addClass("hand_item_" + n.args.card.id, "received");
    },

    notif_receiveCardJarvis(n) {
      this.addCardInJarvisHand(n.args.card);
      dojo.addClass("hand_item_" + n.args.card.id, "received");
    },



    /**********************
    ******* DISCARD *******
    **********************/
    setupDiscard(){
      dojo.addClass('discard-container', 'active');

      let addSquare = (c, i) => {
        dojo.place(`<div id="discard-${c}-${i}" class="discard-slot color-${c} number-${i}">${i}</div>`, 'discard-grid');
      };

      for(let i = 1; i <= 9; i++){
        addSquare(1, i);
      }
      for(let i = 1; i <= 9; i++){
        addSquare(2, i);
      }
      addSquare(5, 1);
      addSquare(5, 2);

      for(let i = 1; i <= 9; i++){
        addSquare(3, i);
      }
      for(let i = 1; i <= 9; i++){
        addSquare(4, i);
      }
      addSquare(5, 3);
      addSquare(5, 4);

      this.gamedatas.discard.forEach(card => {
        dojo.addClass('discard-' + card.color + '-' + card.value, 'played');
      })
    },


    /**********************
     ******* JARVIS *******
     **********************/
     setupJarvisCards() {
      // Create the columns
      for (let i = 1; i <= 7; i++) {
        dojo.place(`<div class='jarvis-column' id='jarvis-column-${i}'></div>`, 'jarvis-hand');
      }

      let cards = this.gamedatas.players[JARVIS_ID].cards;
      cards.forEach((card) => this.addCardInJarvisHand(card));
    },

    /**
     * tplJarvisCard: template for card
     */
    setJarvisCard(card) {
      dojo.place(`<div class="jarvis-card" data-color="${card.color || 0}" data-value="${card.value || 0}" id="jarvis-card-${
        card.id
      }"></div>`, `jarvis-column-${card.column}`);
    },

    /**
     * addCardInHand: add a card to Tonojo's hand
     */
    addCardInJarvisHand(card) {
      debugger;
      // this.place('tplJarvisCard', card, 'jarvis-column-' + card.column);
      this.setJarvisCard(card);
      if (!card.hidden) {
        // this.addCardInHand(card);
        this.createCardTooltip(card, 'card-' + card.id);
      }
    },

    /**
     * Jarvis reveal a new card
     */
    notif_jarvisRevealNewCard(n) {
      debug('Notif: Jarvis reveal a new card', n);
      let card = n.args.card;
      let oCard = $('card-' + (n.args.column + 100));
      oCard.id = 'card-' + card.id;
      oCard.setAttribute('data-color', card.color);
      oCard.setAttribute('data-value', card.value);
      this.createCardTooltip(card, 'card-' + card.id);
    },

    /**
     * New jarvis hand
     */
    notif_newJarvisHand(n) {
      debug('Notif: new jarvis hand', n);

      // Remove cards in hand if any
      dojo.query('#jarvis-hand .jarvis-card').forEach(dojo.destroy);
      // Create the new cards
      n.args.hand.forEach((card) => this.addCardInJarvisHand(card));
    },
  });
});
