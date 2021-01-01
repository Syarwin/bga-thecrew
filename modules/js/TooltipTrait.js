define(["dojo", "dojo/_base/declare", "ebg/stock"], (dojo, declare) => {
  const HIDDEN_TASK = 7;

  return declare("thecrew.tooltipTrait", null, {
    constructor(){
    },

    /*
     * Called to setup player board, called at setup and when someone is eliminated
     */
    setupTooltips(){
      Object.values(this.gamedatas.players).forEach( player => {
        this.addTooltip('tricks-' + player.id, _('Number of tricks won.'), '');
        this.addTooltip('cards-in-hand-' + player.id, _('Number of cards in hand.'), '');

        let commanderDesc = _('The commander is always the player with the four rocket. <br/>The duties of the commander are: <br/>1. start the selection of the tasks <br/>2. start the first trick <br/>3. implement the special rules of individual missions');
        this.addTitledTooltip('panel-commander-' + player.id,_('Commander token'), commanderDesc);
        this.addTitledTooltip('commander-icon-' + player.id, _('Commander token'), commanderDesc);

        this.addTitledTooltip('special-icon-' + player.id, _('Special crew member'), this.getMissionSpecialDescription() );
        this.addTitledTooltip('special2-icon-' + player.id, _('Second special one'), _('This crew member must win the last trick.') );

        this.addTitledTooltip('radio-' + player.id, _('Radio communication token'), _('Communication token gives information of the communicated color card :<br/>- At the top, if it is your highest card of this color.<br/>- In the middle, if it is your only card of this color.<br/>- At the bottom, if it is your lowest card of this color.<br/>- Red, you cannot communicate.'));
        this.addTitledTooltip('comm-pending-' + player.id, _('Communication pending token'), _('If visible, you will start communication at the beggining of next trick.'));
        this.addTitledTooltip('disruption-' + player.id, _('Disruption token'), _('Disruption token gives information of how many tricks are left before being able to communicate again.'));
      });

      this.addTitledTooltip('distress', _('Distress token'), _('A distress signal can be sent out before the first trick of a mission and before any communication. If the distress signal is activated, each crew member may pass one card to his neighbor. Rockets may not be passed on! Decide together if you will pass the cards to the left or the right. Everyone has to pass in the same direction!') );
      this.addTitledTooltip('distress-panel-icon', _('Distress token'), _('A distress signal can be sent out before the first trick of a mission and before any communication. If the distress signal is activated, each crew member may pass one card to his neighbor. Rockets may not be passed on! Decide together if you will pass the cards to the left or the right. Everyone has to pass in the same direction!') );
    },

    updateSpecialTooltip(desc){
      Object.values(this.gamedatas.players).forEach( player => {
        this.addTitledTooltip('special-icon-' + player.id, _('Special crew member'), desc );
      });
      this.addTitledTooltip('mission-informations-special', _('Special crew member'), desc );

    },

    addTitledTooltip(id, title, description, className = ''){
      let withContent = description == ''? '' : 'with-content';
      var content = `
        <div class="tooltip-container ${withContent} ${className}">
      		<span class="tooltip-title">${title}</span>
      `;
      if(description != '' && description != undefined){
        content += `
          <hr/>
          <span class="tooltip-message">${description}</span>
        `;
      }
      content += '</div>';

      this.addTooltipHtml(id, content);
    },


    getTileDescription(tile){
      let taskTileDescriptions = {
        '1' :  _('This task must be fulfilled first.'),
        '2' :  _('This task must be fulfilled second.'),
        '3' :  _('This task must be fulfilled third.'),
        '4' :  _('This task must be fulfilled fourth.'),
        '5' :  _('This task must be fulfilled fifth.'),
        'o' :  _('This task must be fulfilled last.'),
        'i1' :  _('This task must be fulfilled before &rsaquo;&rsaquo;.'),
        'i2' :  _('This task must be fulfilled after &rsaquo;.'),
        'i3' :  _('This task must be fulfilled after &rsaquo;&rsaquo;.'),
        'i4' :  _('This task must be fulfilled after &rsaquo;&rsaquo;&rsaquo;.'),
      };
      return taskTileDescriptions[tile];
    },

    createTaskTooltip(task) {
      var msg = "";
      if(task.color != HIDDEN_TASK){ // TODO : whaat ??
        msg = this.getFormatedMsg(task);

        if(task.tile != null && this.getTileDescription(task.tile) != undefined){
          msg += '<br/>' + this.getTileDescription(task.tile);
        }
      }

      this.addTitledTooltip("task-" + task.id, _('Task'), msg, 'card-description');
    },


    createCardTooltip(card, elemId) {
      var msg = this.getFormatedMsg(card);
      this.addTitledTooltip(elemId, msg, '', 'card-description');
     },

     /*
      * getFormatedMsg : return a msg to describe a card/task using format_string_recursive
      */
     getFormatedMsg(o){
       return this.format_string_recursive(_("${value_symbol}${color_symbol} : ${value_name} ${color_name}."), {
         'value_name' :  o.value,
         'color_name' : o.color,
         'value_symbol' : o.value,
         'color_symbol' : o.color
       });
     },


     /* This enable to inject translatable styled things to logs or action bar */
     /* @Override */
     format_string_recursive (log, args) {
       try {
         if (log && args && !args.processed) {
           args.processed = true;

           // Representation of the value of a card
           if (args.value_symbol !== undefined) {
             args.value_symbol = dojo.string.substitute("<span class='card-value ${actual_color}'>${value_symbol}</span>", {'actual_color' : this.colors[args.color_symbol].color, 'value_symbol' : args.value_symbol});
           }

           // Representation of the color of a card
           if (args.color_symbol !== undefined) {
             args.color_symbol = dojo.string.substitute("<span class='logicon ${actual_color}' title='${actual_color_name}'></span>", {'actual_color' : this.colors[args.color_symbol].color, 'actual_color_name' : this.colors[args.color_symbol].name});
           }

           if (args.color_name !== undefined) {
             args.color_name = _(this.colors[args.color_name].name);
           }

           if (args.color_nameof !== undefined) {
             args.color_nameof = _(this.colors[args.color_nameof].nameof);
           }
         }
       } catch (e) {
         console.error(log,args,"Exception thrown", e.stack);
       }

       return this.inherited(arguments);
     },
  });
});
