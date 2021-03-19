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

      dojo.connect($('mission-description'), 'click', () => dojo.toggleClass('mission-status', 'collapse'));
    },

    getMission(){
      return this.gamedatas.missions[this.gamedatas.status.mId - 1];
    },

    updateMissionStatus(){
      let mId = this.gamedatas.status.mId;
      let tDesc = _(this.gamedatas.missions[mId - 1].desc);
      $('mission-description').innerHTML = tDesc;
      dojo.toggleClass('mission-status', 'small-description', tDesc.length < 500);
      this.createMissionInformations();
      this.missionCounter.setValue(mId);
      this.attemptsCounter.setValue(this.gamedatas.status.attempts);
      this.totalAttemptsCounter.setValue(this.gamedatas.status.total);
      this.updateMissionCommunication();

      dojo.toggleClass('distress', "activated", this.gamedatas.status.distress);
    },

    updateMissionCommunication(){
      let mission = this.gamedatas.missions[this.gamedatas.status.mId - 1];
      let disruption = mission.disruption? mission.disruption : 0;
      dojo.attr('thecrew-table', 'data-disruption', Math.max(0, disruption - this.gamedatas.trickCount));
    },

    createMissionInformations(container = 'mission-informations'){
      dojo.empty(container);
      let mission = this.getMission();

      // 5 players special rule
      dojo.toggleClass('mission-counter', 'special-rule', mission.specialRule);

      // Token commander
      if(mission.hiddenTasks){
        dojo.place(`<div id="mission-informations-commander"></div>`, container);
        this.addTooltip('mission-informations-commander', _('Place the specified number of task cards face down in the middle of the table. After each player has reviewed their cards, your commander asks each crew member, whether they see themselves fit to take on all of the tasks. It may only be answered with "yes" or "no".  actually receives the assignment and reveals the task cards. The mission is fulfilled when the crew member has completed all the tasks. Your commander may not choose himself or herself.'), '');
      }

      // Tasks
      if(mission.tasks > 0){
        let arrow = mission.hiddenTasks? '<div class="thecrew-arrow-down"></div>' : '';
        dojo.place(`<div id="mission-informations-tasks-container">${arrow}<div id="mission-informations-tasks" class="mission-informations-tasks">${mission.tasks}</div></div>`, container);
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


      // Distribution
      if(mission.distribution){
        dojo.place(`<div id="mission-informations-commander"></div>`, container);
        this.addTooltip('mission-informations-commander', _('Your commander now uncovers a task card and asks each crew member in turn whether he or she wants to take on the task. It may only be answered with "yes" or "no". Afterwards, your commander decides who actually receives the assignment. He or she can also choose himself or herself. Repeat the process until all of the tasks are distributed. Note, however, that the tasks must be evenly distributed: At the end of the distribution, no one may have two tasks more than another crew member. '), '');
        dojo.place(`<div id="mission-informations-distribution"><div class="thecrew-arrow-down"></div><div class="thecrew-arrow-down"></div><div class="thecrew-arrow-down"></div></div>`, container);
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
        let icon = mission.informations.specialIcon ? mission.informations.specialIcon : "special";
        dojo.place(`<div id="mission-informations-special"><div class='icon-${icon}'></div><div class='special-desc'>${desc}</div></div>`, container);
        if(mission.informations.specialTooltip){
          this.updateSpecialTooltip(_(mission.informations.specialTooltip));
        }
      }

      if(mission.informations.special2){
        let desc = _(mission.informations.special2);
        let icon = mission.informations.special2Icon ? mission.informations.special2Icon : "special2";
        dojo.place(`<div id="mission-informations-special2"><div class='icon-${icon}'></div><div class='special-desc'>${desc}</div></div>`, container);
        this.addTitledTooltip('mission-informations-special2', _('Second special one'), _('This crew member must win only the last trick.') );
      }


      // Deadzone
      if(mission.deadzone){
        dojo.place('<div id="mission-informations-deadzone"><div id="deadzone-radio-token"></div><div id="deadzone-question"></div></div>', container);
        this.addTitledTooltip("mission-informations-deadzone", _('Dead zone'), _('Your communications have been disrupted and you only have limited communication. When you want to communicate, place your card in front of you as you normally would. It must meet one of the three conditions (highest, single, or lowest of the cards in your hand, in the color suit). You are not however, allowed to place your radio communication token on the card.') );
      }

      // Balanced
      if(mission.balanced){
        dojo.place(`<div id="mission-informations-balanced"></div>`, container);
        this.addTooltip('mission-informations-balanced', _('At no time may a crew member have won 2 tricks more than another crew member.'), '');
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

      let noMsg = '';
      if(this.gamedatas.isCampaign){
        msg = args.end > 0? _('Do you want to play next mission or leave campaign for now?') : _('Do you want to retry this mission or leave campaign for now?');
        noMsg = _('Leave');
      } else {
        msg = args.end > 0? _('Do you want to play next mission or stop here?') : _('Do you want to retry this mission or stop here?');
        noMsg = _('Stop');
      }
      $('endResultMessage').innerHTML = msg;
      $('yes-button').innerHTML = args.end > 0? _('Next mission') : _('Retry');
      $('no-button').innerHTML = noMsg;

      // Show/hide Yes/No buttons
      dojo.query('#end-panel-buttons .finalbutton').toggleClass('hidden', !this.isCurrentPlayerActive());
      if(!this.isSpectator){
        this.connect($('yes-button'), 'click', () => this.takeAction("actContinueMissions") );
        this.connect($('no-button'),  'click', () => this.takeAction("actStopMissions") );
      }

      // Checkmarks
      if(!this.isSpectator){
        dojo.removeClass('comm-card-' + this.player_id, "selectable");
      }
    },

    notif_continue(n){
      dojo.addClass("continue-ok-" + n.args.player_id, "check-confirm");
      if(this.player_id == n.args.player_id)
        dojo.query('#end-panel-buttons .finalbutton').addClass('hidden');
    },

    getMissionSpecialDescription(){
      let mission = this.gamedatas.missions[this.gamedatas.status.mId - 1];
      return mission.informations.specialTooltip? _(mission.informations.specialTooltip) : _('This crew member is special for this mission.');
    },
  });
});
