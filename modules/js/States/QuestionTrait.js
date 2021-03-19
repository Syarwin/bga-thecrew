define(["dojo", "dojo/_base/declare"], (dojo, declare) => {
  return declare("thecrew.questionTrait", null, {
    constructor(){
      this._notifications.push(
        ['speak', 1000],
        ['clearReplies', 10]
      );
    },

    onEnteringStateQuestion(args){
      this.showTasks(args.tasks);

      if(this.isCurrentPlayerActive()){
        args.replies.forEach((reply, i) => {
          this.addPrimaryActionButton('btnReply' + i, _(reply), () => this.onChooseReply(i) );
        });
      }
    },

    onChooseReply(index){
      if(!this.isCurrentPlayerActive() || !this.checkAction("actReply"))
        return false;

      this.takeAction('actReply', { reply : index });
    },



    notif_speak(n) {
      debug('Someone answered', n);
      this.setupReply(n.args.player_id, _(n.args.content));
    },

/*
    notif_speak(n) {
      debug('Someone answered', n);
      let id = 'bubble-' + n.args.player_id;
      if($(id)){
        dojo.destroy(id);
      }

      // Create the bubble
      dojo.place('<div class="bubble" id="'+ id + '">' + _(n.args.content) + '</div>', 'player-table-name-' + n.args.player_id);

      setTimeout( () => {
        // Wait 100ms and make it appear
        dojo.addClass(id, "show");
        setTimeout(() => {
          // Wait 8sec and make it disappear, then destroy
          dojo.removeClass(id, "show");
          setTimeout(() => dojo.destroy(id), 1000);
        }, 8000)
      }, 100);
    },
*/
    setupReply(pId, content){
      $('reply-' + pId).innerHTML = content;
      dojo.addClass('reply-' + pId, 'show');
      dojo.toggleClass('reply-' + pId, "small", content.length > 10);
      dojo.attr('player-table-' + pId, 'data-reply', 'on');
    },

    notif_clearReplies(n){
       Object.values(this.gamedatas.players).forEach(player => {
        dojo.attr('player-table-' + player.id, 'data-reply', 'off');
      })
    },

    onEnteringStatePickCrew(args){
      this.showTasks(args.tasks);

      if(this.gamedatas.status.mId == 50){
        let first = this.gamedatas.specialId == 0;
        this.gamedatas.gamestate.description = first? this.gamedatas.gamestate.description50first : this.gamedatas.gamestate.description50last;
        this.gamedatas.gamestate.descriptionmyturn = first? this.gamedatas.gamestate.description50firstmyturn : this.gamedatas.gamestate.description50lastmyturn;
        this.updatePageTitle();
      }

      if(this.isCurrentPlayerActive()){
        this.makePlayersSelectable(args.players, this.onPickCrew.bind(this));
      }
    },

    onPickCrew(pId){
      if(!this.isCurrentPlayerActive() || !this.checkAction("actPickCrew"))
        return false;

      this.takeAction('actPickCrew', { pId });
    },
  });
});
