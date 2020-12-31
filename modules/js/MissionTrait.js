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
      this.updateMissionCommunication();

      dojo.toggleClass('distress', "activated", this.gamedatas.status.distress);
    },

    updateMissionCommunication(){
      let mission = this.gamedatas.missions[this.gamedatas.status.mId - 1];
      let disruption = mission.disruption ?? 0;
      dojo.attr('thecrew-table', 'data-disruption', Math.max(0, disruption - this.gamedatas.trickCount));
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

      // Question
      if(mission.question){
        let question = _(mission.question);
        let replies = mission.replies.map(reply => _(reply)).join('/');
        dojo.place(`<div id="mission-informations-question"><div class='bubble left show'>${question}</div><div class='bubble show'>${replies}</div></div>`, container);
      }

      // Special
      if(mission.informations.special){
        let desc = _(mission.informations.special);
        dojo.place(`<div id="mission-informations-special"><div class='icon-special'></div><div class='special-desc'>${desc}</div></div>`, container);
        if(mission.informations.specialTooltip){
          this.updateSpecialTooltip(_(mission.informations.specialTooltip));
        }
      }

      // Deadzone
      if(mission.deadzone){
        dojo.place('<div id="mission-informations-deadzone"><div id="deadzone-radio-token"></div><div id="deadzone-question"></div></div>', container);
        this.addTitledTooltip("mission-informations-deadzone", _('Dead zone'), _('Your communications have been disrupted and you only have limited communication. When you want to communicate, place your card in front of you as you normally would. It must meet one of the three conditions (highest, single, or lowest of the cards in your hand, in the color suit). You are not however, allowed to place your radio communication token on the card.') );
      }

      // Disruption
      if(mission.disruption > 0){
        dojo.place('<div id="mission-informations-disruption"><div id="disruption-radio-token"></div><div id="disruption-number"><i class="fa fa-bolt" aria-hidden="true"></i>' + mission.disruption + '</div></div>', container);
        this.addTitledTooltip("mission-informations-disruption", _('Disruption'), _('Your communication is completely interrupted for a short period of time. The number will tell you during which trick communication can begin once again. Until then, no crew member can communicate about a card. Starting from the named trick, regular communication rules apply.') );
      }


      // Cards special
      if(mission.informations.cards || mission.informations.cardsType){
        let desc = mission.informations.cards? _(mission.informations.cards) : '';
        let type = mission.informations.cardsType;
        dojo.place(`<div id="mission-informations-cards"><div class='special-desc'>${desc}</div><div class='icon-cards' data-type='${type}'></div></div>`, container);
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

    getMissionSpecialDescription(){
      let mission = this.gamedatas.missions[this.gamedatas.status.mId - 1];
      return mission.informations.specialTooltip? _(mission.informations.specialTooltip) : _('This crew member is special for this mission.');
    },
  });
});
