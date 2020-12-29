define(["dojo", "dojo/_base/declare"], (dojo, declare) => {
  return declare("thecrew.questionTrait", null, {
    constructor(){
      this._notifications.push(
        ['speak', 1000]
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


    onEnteringStatePickCrew(args){
      this.showTasks(args.tasks);
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
