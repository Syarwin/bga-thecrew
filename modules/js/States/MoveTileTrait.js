define(["dojo", "dojo/_base/declare"], (dojo, declare) => {
  return declare("thecrew.moveTileTrait", null, {
    constructor(){
      this._notifications.push(
        ['swapTiles', 1000]
      );
    },


    onEnteringStateMoveTile(args){
      debug("Pick task", args);

      this.showTasks(args.tasks);
      if(this.isCurrentPlayerActive()){
        this._selectableTiles = args.tiles;
        this._selectedTile = null;
        this._selectedTile2 = null;
        this.makeTaskTileSelectable(Object.keys(args.tiles));
        this.addPrimaryActionButton('passMoveTile', _('Pass'), () => this.takeAction('actPassMoveTile') );
      }
    },

    makeTaskTileSelectable(taskIds){
      this.disconnectAll();
      dojo.query('#tasks-list .task').removeClass('tile-selectable selectable');
      taskIds.forEach(taskId => {
        var oTask = $("task-" + taskId);
        dojo.addClass(oTask, dojo.attr(oTask, 'data-tile') != ''? "tile-selectable" : "selectable");
        this.connect(oTask, 'click', () => this.onChooseTaskTile(taskId) );
      })
    },

    cancelSelectedTile(){
      dojo.removeClass('task-' + this._selectedTile, 'tile-selected');
      dojo.removeClass('task-' + this._selectedTile2, 'selected');
      this._selectedTile = null;
      this._selectedTile2 = null;
      this.makeTaskTileSelectable(Object.keys(this._selectableTiles));
      if($('cancelSelectedTile'))
        dojo.destroy('cancelSelectedTile');
      if($('btnConfirmMoveTile'))
        dojo.destroy('btnConfirmMoveTile');
    },

    onChooseTaskTile(taskId){
      debug("Clicked on task : ", taskId);

      if($('btnConfirmMoveTile'))
        dojo.destroy('btnConfirmMoveTile');

      // First click, select the tile
      if(this._selectedTile == null){
        this._selectedTile = taskId;
        this.makeTaskTileSelectable(this._selectableTiles[taskId]);
        dojo.addClass('task-' + taskId, 'tile-selected');

        this.connect($('task-' + taskId), 'click', () => this.cancelSelectedTile() );
        this.addSecondaryActionButton('cancelSelectedTile', _('Cancel'), () => this.cancelSelectedTile() );
      }
      // Click on already selected tile => unselect it
      else if(taskId == this._selectedTile){
        this.cancelSelectedTile();
      }
      // Click on second card => take action
      else {
        if (this._selectedTile2 !== null) {
          dojo.removeClass('task-' + this._selectedTile2, 'selected');
        }

        this._selectedTile2 = taskId;
        dojo.addClass('task-' + taskId, 'selected');

        this.addPrimaryActionButton('btnConfirmMoveTile', _('Confirm'), () => {
          dojo.destroy('btnConfirmMoveTile');
          this.takeAction('actMoveTile', {
            taskId1 : this._selectedTile,
            taskId2: this._selectedTile2
          });
        });
      }
    },


    notif_swapTiles(n) {
      debug('Swapping tiles', n);
      let tId1 = n.args.task1['id'], tId2 = n.args.task2['id'];
      let tiles = [tId1, tId2].map(tId => document.querySelector('#task-' + tId + ' .task-tile') );

      tiles.forEach((tile, i) => {
        tile.id = 'task-tile-animation-' + i;
        this.attachToNewParent(tile.id, 'tasks-list');
        let target = 'task-' + (i == 0? tId2 : tId1);
        this.slidePos(tile.id, target, 11, 51, 1000).then(() => {
          this.attachToNewParent(tile.id, target);
        });
      });

      // Update tooltips
      this.createTaskTooltip(n.args.task1);
      this.createTaskTooltip(n.args.task2);
    },

  });
});
