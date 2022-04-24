define(["dojo", "dojo/_base/declare"], (dojo, declare) => {
  return declare("thecrew.giveTaskTrait", null, {
    constructor(){
      this._notifications.push(
        ['confirmGiveTask', 10],
        ['giveTask', 1000]
      );
    },


    onEnteringStateGiveTask(args){
      debug("Give task", args);
      this.switchCentralZone('');

      if(!this.isSpectator){
        this._selectedTask = null;
        this.makeTaskSelectable(args.players[this.player_id], this.onChooseTaskToGive.bind(this));
        if(this.isCurrentPlayerActive())
          this.addPrimaryActionButton('btnPassGiveTask', _('Nothing to propose'), () => this.takeAction('actPassGiveTask') );
      }
    },

    onChooseTaskToGive(task){
      debug("Clicked on task ", task);

      if(this._selectedTask != null){
        var previousTask = this._selectedTask.id;
        this.cancelSelectedTask();
        if(previousTask == task.id)
          return;
      }

      this._selectedTask = task;
      dojo.addClass('task-' + task.id, "selected");
      this.addSecondaryActionButton('btnCancelSelectedTask', _('Cancel'), () => this.cancelSelectedTask() );

      let players = Object.keys(this.gamedatas.players).filter(pId => pId != this.player_id);
      this.makePlayersSelectable(players, this.onClickPlayerToGiveTask.bind(this));
    },

    cancelSelectedTask(){
      dojo.removeClass('task-' + this._selectedTask.id, "selected");
      dojo.destroy("btnCancelSelectedTask");
      this._selectedTask = null;
      this.clearSelectablePlayers();
    },

    onClickPlayerToGiveTask(player){
      debug("Clicked player to give task :", player)

      if(!this.checkAction("actGiveTask"))
        return false;

      this.takeAction('actGiveTask', {
        taskId : this._selectedTask.id,
        pId : player,
      });
    },



    onEnteringStateGiveTaskConfirmation(args){
      this.switchCentralZone('give-task');

      // Show proposal
      dojo.empty('proposal-task');
      this.addTask(args.task, 'proposal-task');
      dojo.style('proposal-source', 'color', '#' + this.gamedatas.players[args.sourceId].color);
      $('proposal-source').innerHTML = this.gamedatas.players[args.sourceId].name;
      dojo.style('proposal-target', 'color', '#' + this.gamedatas.players[args.targetId].color);
      $('proposal-target').innerHTML = this.gamedatas.players[args.targetId].name;

      // Show/hide Yes/No buttons
      let active = this.isCurrentPlayerActive() && this.player_id != args.sourceId;
      dojo.query('#give-task-buttons .finalbutton').toggleClass('hidden', !active);
      if(active){
        this.connect($('agree-button'), 'click', () => this.takeAction("actConfirmGiveTask") );
        this.connect($('reject-button'),  'click', () => this.takeAction("actRejectGiveTask") );
      }
    },

    notif_confirmGiveTask(n){
      dojo.addClass("give-task-ok-" + n.args.player_id, "check-confirm");
      dojo.query('#give-task-buttons .finalbutton').toggleClass('hidden', this.player_id == n.args.player_id);
    },


    notif_giveTask(n) {
      debug("Giving a task", n);
      dojo.empty('proposal-task');

      let task = n.args.task,
          oTask = $('task-' + task.id);

      this.attachToNewParent(oTask, 'player-table-missions-' + task.pId);

      // Slide it
      dojo.animateProperty({
        node: oTask.id,
        duration: 1000,
        easing: dojo.fx.easing.expoInOut,
        properties: {
           left: 0,
           top: 0,
        }
      }).play();
    },
  });
});
