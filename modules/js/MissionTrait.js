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
      this.createMissionInformations();
      this.missionCounter.setValue(mId);
      this.attemptsCounter.setValue(this.gamedatas.status.attempts);
      this.totalAttemptsCounter.setValue(this.gamedatas.status.total);

      dojo.toggleClass('distress', "activated", this.gamedatas.status.distress);
    },

    createMissionInformations(container = 'mission-informations'){
      dojo.empty(container);
      let mission = this.gamedatas.missions[this.gamedatas.status.mId - 1];

      // Tasks
      if(mission.tasks > 0){
        dojo.place(`<div id="mission-informations-tasks" class="mission-informations-tasks">${mission.tasks}</div>`, container);
        this.addTooltip('mission-informations-tasks', _('Number of tasks used for this mission'), '');
      }

      // Tiles
      if(mission.tiles.length > 0){
        dojo.place(`<div id="mission-informations-tiles" class="mission-informations-tiles"></div>`, container);
        mission.tiles.forEach((tile, i) => {
          dojo.place(`<div id='${container}-tile-${i}' class='task-tile tile-${tile}'></div>`, "mission-informations-tiles");
          this.addTooltip(`${container}-tile-${i}`, this.getTileDescription(tile), '');
        });
      }

      // Deadzone
      if(mission.deadzone){
        dojo.place('<div id="mission-informations-deadzone"><div id="deadzone-radio-token"></div><div id="deadzone-question"></div></div>', container);
        this.addTitledTooltip("mission-informations-deadzone", _('Dead zone'), _('Your communications have been disrupted and you only have limited communication. When you want to communicate, place your card in front of you as you normally would. It must meet one of the three conditions (highest, single, or lowest of the cards in your hand, in the color suit). You are not however, allowed to place your radio communication token on the card.') );
      }
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
