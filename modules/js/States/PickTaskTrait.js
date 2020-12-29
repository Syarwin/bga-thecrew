define(["dojo", "dojo/_base/declare"], (dojo, declare) => {
  return declare("thecrew.pickTaskTrait", null, {
    constructor(){
      this._notifications.push(
        ['takeTask', 1000],
        ['taskUpdate', 10]
      );
    },


    onEnteringStatePickTask(args){
      debug("Pick task", args);

      this.showTasks(args.tasks);
      if(this.isCurrentPlayerActive())
        this.makeTaskSelectable(args.tasks);
    },

    makeTaskSelectable(tasks){
      tasks.forEach(task => {
        var oTask = $("task-" + task.id);
        dojo.addClass(oTask, "selectable");
        this.connect(oTask, 'click', () => this.onChooseTask(task) );
      })
    },

    showTasks(tasks){
      if(tasks.length == 0){
        this.switchCentralZone('none');
        return;
      }

      this.switchCentralZone('tasks');
      dojo.empty("tasks-list");
      tasks.forEach(task => this.addTask(task, 'tasks-list') );
    },


    addTask(task, container){
      task.tileClass = task.tile == ''? 'no-tile' : ('tile-' + task.tile);
      this.place('jstpl_task', task, container);
      this.createTaskTooltip(task);
    },

    onChooseTask(task){
      debug("Clicked on task ", task);
      if(!this.isCurrentPlayerActive() || !this.checkAction("actChooseTask"))
        return false;

      this.takeAction('actChooseTask', { taskId : task.id });
    },



    notif_takeTask(n) {
      debug("Picking a task", n);
      let task = n.args.task,
          oTask = $('task-' + task.id);

      this.attachToNewParent(oTask, 'player-table-missions-' + task.pId);

/*
TODO
        if($(taskId).classList.contains('col7'))
        {
          //reveal task if necessary
          dojo.query("#"+taskId).removeClass("col7 val0");
          dojo.query("#"+taskId).addClass("col"+task['card_type']+" val"+task['card_type_arg']);
        }
        this.createTaskTooltip(task);
*/

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


    notif_taskUpdate(n){
      debug("Update task status", n);
      dojo.attr('task-' + n.args.task.id, 'data-status', n.args.task.status);
      $task
    },
  });
});
