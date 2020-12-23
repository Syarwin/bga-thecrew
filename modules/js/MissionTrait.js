define(["dojo", "dojo/_base/declare"], (dojo, declare) => {
  return declare("thecrew.missionTrait", null, {
    constructor(){
      this._notifications.push(
        ['continue', 10]
      );
    },


    setupMission(){
      this.missionCounter = new ebg.counter();
      this.missionCounter.create('mission-counter');
      this.attemptsCounter = new ebg.counter();
      this.attemptsCounter.create('try-counter');
      this.totalAttemptsCounter = new ebg.counter();
      this.totalAttemptsCounter.create('total-try-counter');
      this.updateMissionStatus();
    },

    updateMissionStatus(){
      let mId = this.gamedatas.status.mId;
      $('mission-description').innerHTML = _(this.gamedatas.missions[mId - 1].desc);
      this.missionCounter.setValue(mId);
      this.attemptsCounter.setValue(this.gamedatas.status.attempts);
      this.totalAttemptsCounter.setValue(this.gamedatas.status.total);

      dojo.toggleClass('distress', "activated", this.gamedatas.status.distress);
    },


    onEnteringStateEndMission(args){
      this.switchCentralZone('end');

      let msg = (args.end > 0)? _('Mission ${nb} <span class="success">completed</span>') :  _('Mission ${nb} <span class="failure">failed</span>');
      $('endResult').innerHTML = msg.replace('${nb}', args.number);

      // Show/hide Yes/No buttons
      dojo.query('#end-panel-buttons .finalbutton').toggleClass('hidden', !this.isCurrentPlayerActive());
      if(this.isCurrentPlayerActive()){
        this.connect($('yes-button'), 'click', () => this.takeAction("actContinueMissions") );
        this.connect($('no-button'),  'click', () => this.takeAction("actStopMissions") );
      }

      // Checkmarks
      dojo.query("#end-panel .check-ok").addClass("check-confirm");
      this.getActivePlayers().forEach(pId => dojo.removeClass("continue-ok-" + pId, "check-confirm"));
      if(!this.isSpectator){
        dojo.removeClass('comm-card-' + this.player_id, "selectable");
      }
    },

    notif_continue(n){
      dojo.addClass("continue-ok-" + n.args.player_id, "check-confirm");
    },
  });
});
